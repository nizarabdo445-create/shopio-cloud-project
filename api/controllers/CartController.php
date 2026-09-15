<?php
/**
 * Cart Controller
 * Handles server-side persistent cart operations
 * All endpoints require authentication
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/Response.php';

class CartController {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/cart
     * Get current user's cart items
     */
    public function index(): void {
        $user = AuthMiddleware::requireAuth();
        $userId = $user['user_id'];

        $stmt = $this->db->prepare(
            "SELECT ci.id, ci.quantity, ci.created_at,
                    p.id AS product_id, p.prod_name, p.prod_price, p.prod_img
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.user_id = ?
             ORDER BY ci.created_at DESC"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        $total = 0;
        while ($row = $result->fetch_assoc()) {
            $subtotal = (float)$row['prod_price'] * (int)$row['quantity'];
            $total += $subtotal;
            $items[] = [
                'id'         => (int)$row['id'],
                'product_id' => (int)$row['product_id'],
                'name'       => $row['prod_name'],
                'price'      => (float)$row['prod_price'],
                'image'      => $row['prod_img'],
                'quantity'   => (int)$row['quantity'],
                'subtotal'   => $subtotal,
                'created_at' => $row['created_at']
            ];
        }
        $stmt->close();

        Response::success([
            'items'      => $items,
            'item_count' => count($items),
            'total'      => $total
        ]);
    }

    /**
     * POST /api/cart
     * Add item to cart (or increase quantity if exists)
     * Body: { "product_id": 1, "quantity": 1 }
     */
    public function store(): void {
        $user = AuthMiddleware::requireAuth();
        $userId = $user['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        if (empty($data['product_id']) || !is_numeric($data['product_id'])) {
            Response::error('Valid product_id is required', 400);
        }
        $quantity = (int)($data['quantity'] ?? 1);
        if ($quantity < 1) {
            Response::error('Quantity must be at least 1', 400);
        }

        $productId = (int)$data['product_id'];

        // Check product exists
        $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Product not found');
        }
        $stmt->close();

        // Upsert: insert or update quantity if already in cart
        $stmt = $this->db->prepare(
            "INSERT INTO cart_items (user_id, product_id, quantity)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)"
        );
        $stmt->bind_param("iii", $userId, $productId, $quantity);

        if (!$stmt->execute()) {
            Response::serverError('Failed to add item to cart');
        }
        $stmt->close();

        Response::created(['message' => 'Item added to cart']);
    }

    /**
     * PUT /api/cart/{id}
     * Update cart item quantity
     * Body: { "quantity": 3 }
     */
    public function update(int $cartItemId): void {
        $user = AuthMiddleware::requireAuth();
        $userId = $user['user_id'];
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] < 1) {
            Response::error('Quantity must be at least 1', 400);
        }

        // Check cart item belongs to user
        $stmt = $this->db->prepare("SELECT id FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Cart item not found');
        }
        $stmt->close();

        $quantity = (int)$data['quantity'];
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartItemId, $userId);
        $stmt->execute();
        $stmt->close();

        Response::success(['message' => 'Cart updated', 'quantity' => $quantity]);
    }

    /**
     * DELETE /api/cart/{id}
     * Remove item from cart
     */
    public function destroy(int $cartItemId): void {
        $user = AuthMiddleware::requireAuth();
        $userId = $user['user_id'];

        $stmt = $this->db->prepare("SELECT id FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Cart item not found');
        }
        $stmt->close();

        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        $stmt->close();

        Response::noContent();
    }
}
