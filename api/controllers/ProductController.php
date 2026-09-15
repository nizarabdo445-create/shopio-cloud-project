<?php
/**
 * Product Controller
 * Handles CRUD operations for products
 * GET/GET{id} = public, POST/PUT/DELETE = Admin only
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/Response.php';

class ProductController {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/products
     * Public — no auth required
     */
    public function index(): void {
        $result = $this->db->query("SELECT * FROM products ORDER BY id DESC");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = [
                'id'        => (int)$row['id'],
                'name'      => $row['prod_name'],
                'price'     => (float)$row['prod_price'],
                'image'     => $row['prod_img'],
                'created_at'=> $row['created_at']
            ];
        }
        Response::success($products);
    }

    /**
     * GET /api/products/{id}
     * Public — no auth required
     */
    public function show(int $id): void {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            Response::notFound('Product not found');
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        Response::success([
            'id'        => (int)$row['id'],
            'name'      => $row['prod_name'],
            'price'     => (float)$row['prod_price'],
            'image'     => $row['prod_img'],
            'created_at'=> $row['created_at']
        ]);
    }

    /**
     * POST /api/products
     * Admin only
     * Body: { "name": "...", "price": 99.99 }
     * Optional file upload: prod_img
     */
    public function store(): void {
        AuthMiddleware::requireAdmin();

        // Handle both JSON and multipart/form-data
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Validation
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Product name is required';
        }
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Valid price is required (must be > 0)';
        }
        if (!empty($errors)) {
            Response::error('Validation failed', 400, $errors);
        }

        // Handle image upload
        $imgPath = null;
        if (!isset($_FILES['prod_img']) || $_FILES['prod_img']['error'] !== UPLOAD_ERR_OK) {
            Response::error('Product image is required and must be a valid upload', 400);
        }
        $imgPath = $this->handleImageUpload($_FILES['prod_img']);

        $stmt = $this->db->prepare(
            "INSERT INTO products (prod_name, prod_price, prod_img) VALUES (?, ?, ?)"
        );
        $price = (float)$data['price'];
        $stmt->bind_param("sds", $data['name'], $price, $imgPath);

        if (!$stmt->execute()) {
            Response::serverError('Failed to create product');
        }

        $productId = $stmt->insert_id;
        $stmt->close();

        Response::created([
            'id'    => $productId,
            'name'  => $data['name'],
            'price' => $price,
            'image' => $imgPath
        ]);
    }

    /**
     * PUT /api/products/{id}
     * Admin only
     */
    public function update(int $id): void {
        AuthMiddleware::requireAdmin();

        // Check product exists
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Product not found');
        }
        $stmt->close();

        // Handle both JSON and multipart/form-data
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $_POST;
        } else {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
        }

        // Validation
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Product name is required';
        }
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            $errors[] = 'Valid price is required (must be > 0)';
        }
        if (!empty($errors)) {
            Response::error('Validation failed', 400, $errors);
        }

        // Handle image upload
        $imgPath = null;
        if (isset($_FILES['prod_img']) && $_FILES['prod_img']['error'] === UPLOAD_ERR_OK) {
            $imgPath = $this->handleImageUpload($_FILES['prod_img']);
        }

        if ($imgPath !== null) {
            $stmt = $this->db->prepare(
                "UPDATE products SET prod_name = ?, prod_price = ?, prod_img = ? WHERE id = ?"
            );
            $price = (float)$data['price'];
            $stmt->bind_param("sdsi", $data['name'], $price, $imgPath, $id);
        } else {
            $stmt = $this->db->prepare(
                "UPDATE products SET prod_name = ?, prod_price = ? WHERE id = ?"
            );
            $price = (float)$data['price'];
            $stmt->bind_param("sdi", $data['name'], $price, $id);
        }

        if (!$stmt->execute()) {
            Response::serverError('Failed to update product');
        }
        $stmt->close();

        Response::success([
            'id'    => $id,
            'name'  => $data['name'],
            'price' => $price,
            'image' => $imgPath
        ]);
    }

    /**
     * DELETE /api/products/{id}
     * Admin only
     */
    public function destroy(int $id): void {
        AuthMiddleware::requireAdmin();

        $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('Product not found');
        }
        $stmt->close();

        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        Response::noContent();
    }

    /**
     * Handle image file upload with validation
     */
    private function handleImageUpload(array $file): string {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            Response::error('Invalid image type. Allowed: JPEG, PNG, GIF, WebP', 400);
        }

        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            Response::error('Image size must be less than 5MB', 400);
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . '.' . $ext;
        $uploadDir = __DIR__ . '/../../img/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            Response::serverError('Failed to upload image');
        }

        return 'img/' . $filename;
    }
}
