<?php
/**
 * Order Controller
 * Handles order creation from cart, order listing, and status updates
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/Response.php';

class OrderController {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/orders
     * List orders for current user (or all orders for admin)
     */
    public function index(): void {
        $user = AuthMiddleware::requireAuth();

        if ($user['role'] === 1) {
            // Admin sees all orders
            $result = $this->db->query(
                "SELECT o.*, u.user_name, u.user_email
                 FROM orders o
                 JOIN users u ON o.user_id = u.user_id
                 ORDER BY o.created_at DESC"
            );
        } else {
            // User sees only their orders
            $stmt = $this->db->prepare(
                "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC"
            );
            $stmt->bind_param("i", $user['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $order = [
                'id'           => (int)$row['id'],
                'user_id'      => (int)$row['user_id'],
                'total_amount' => (float)$row['total_amount'],
                'status'       => $row['status'],
                'created_at'   => $row['created_at']
            ];
            if (isset($row['user_name'])) {
                $order['user_name']  = $row['user_name'];
                $order['user_email'] = $row['user_email'];
            }
            $orders[] = $order;
        }

        Response::success($orders);
    }

    /**
     * GET /api/orders/{id}
     * Get order details with items
     */
    public function show(int $id): void {
        $user = AuthMiddleware::requireAuth();

        // Get order
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            Response::notFound('Order not found');
        }

        $order = $result->fetch_assoc();
        $stmt->close();

        // Non-admin can only view their own orders
        if ($user['role'] !== 1 && (int)$order['user_id'] !== $user['user_id']) {
            Response::error('Forbidden', 403);
        }

        // Get order items
        $stmt = $this->db->prepare(
            "SELECT oi.*, p.prod_name, p.prod_img
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $itemsResult = $stmt->get_result();

        $items = [];
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = [
                'id'                => (int)$row['id'],
                'product_id'        => (int)$row['product_id'],
                'product_name'      => $row['prod_name'],
                'product_image'     => $row['prod_img'],
                'quantity'          => (int)$row['quantity'],
                'price_at_purchase' => (float)$row['price_at_purchase']
            ];
        }
        $stmt->close();

        Response::success([
            'id'           => (int)$order['id'],
            'user_id'      => (int)$order['user_id'],
            'total_amount' => (float)$order['total_amount'],
            'status'       => $order['status'],
            'created_at'   => $order['created_at'],
            'items'        => $items
        ]);
    }

    /**
     * POST /api/orders
     * Create order from current cart (checkout)
     * Cart is cleared after successful order creation
     */
    public function store(): void {
        $user = AuthMiddleware::requireAuth();
        $userId = $user['user_id'];

        // Get cart items
        $stmt = $this->db->prepare(
            "SELECT ci.*, p.prod_price, p.prod_name
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            Response::error('Cart is empty. Add items before placing an order.', 400);
        }

        $cartItems = [];
        $totalAmount = 0;
        while ($row = $result->fetch_assoc()) {
            $subtotal = (float)$row['prod_price'] * (int)$row['quantity'];
            $totalAmount += $subtotal;
            $cartItems[] = $row;
        }
        $stmt->close();

        // Begin transaction
        $this->db->begin_transaction();

        try {
            // Create order
            $stmt = $this->db->prepare(
                "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')"
            );
            $stmt->bind_param("id", $userId, $totalAmount);
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();

            // Create order items
            $stmt = $this->db->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase)
                 VALUES (?, ?, ?, ?)"
            );
            foreach ($cartItems as $item) {
                $productId = (int)$item['product_id'];
                $quantity  = (int)$item['quantity'];
                $price     = (float)$item['prod_price'];
                $stmt->bind_param("iiid", $orderId, $productId, $quantity, $price);
                $stmt->execute();
            }
            $stmt->close();

            // Clear cart
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            $this->db->commit();

            Response::created([
                'order_id'     => $orderId,
                'total_amount' => $totalAmount,
                'status'       => 'pending',
                'items_count'  => count($cartItems)
            ]);

        } catch (\Exception $e) {
            $this->db->rollback();
            Response::serverError('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * PUT /api/orders/{id}
     * Update order status (Admin only)
     * Body: { "status": "confirmed" }
     */
    public function update(int $id): void {
        AuthMiddleware::requireAdmin();

        $data = json_decode(file_get_contents('php://input'), true);
        $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

        if (empty($data['status']) || !in_array($data['status'], $validStatuses)) {
            Response::error('Valid status required: ' . implode(', ', $validStatuses), 400);
        }

        // Check order exists
        $stmt = $this->db->prepare("SELECT id FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Order not found');
        }
        $stmt->close();

        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $data['status'], $id);
        $stmt->execute();
        $stmt->close();

        Response::success([
            'order_id' => $id,
            'status'   => $data['status']
        ]);
    }
}
