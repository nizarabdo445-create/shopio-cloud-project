<?php
/**
 * Auth Controller
 * Handles: POST /api/auth/register, POST /api/auth/login
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/JWTHelper.php';
require_once __DIR__ . '/../helpers/Response.php';

class AuthController {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * POST /api/auth/register
     * Body: { "name": "...", "email": "...", "password": "..." }
     */
    public function register(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        $errors = [];
        if (empty($data['name']) || strlen($data['name']) < 3) {
            $errors[] = 'Name must be at least 3 characters';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }
        if (empty($data['password']) || strlen($data['password']) < 7) {
            $errors[] = 'Password must be at least 7 characters';
        } elseif (!preg_match('/[a-z]/i', $data['password']) ||
                  !preg_match('/[0-9]/', $data['password']) ||
                  !preg_match('/[\W_]/', $data['password'])) {
            $errors[] = 'Password must contain letters, numbers, and special characters';
        }

        if (!empty($errors)) {
            Response::error('Validation failed', 400, $errors);
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            Response::error('Email already registered', 400);
        }
        $stmt->close();

        // Hash password and insert
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $this->db->prepare(
            "INSERT INTO users (user_name, user_email, user_pass, user_role) VALUES (?, ?, ?, 0)"
        );
        $stmt->bind_param("sss", $data['name'], $data['email'], $hashedPassword);

        if (!$stmt->execute()) {
            Response::serverError('Failed to create account');
        }

        $userId = $stmt->insert_id;
        $stmt->close();

        // Generate JWT
        $token = JWTHelper::generateToken([
            'user_id' => $userId,
            'email'   => $data['email'],
            'name'    => $data['name'],
            'role'    => 0
        ]);

        // Save token to database
        $stmt = $this->db->prepare("UPDATE users SET api_token = ? WHERE user_id = ?");
        $stmt->bind_param("si", $token, $userId);
        $stmt->execute();
        $stmt->close();

        Response::created([
            'user_id' => $userId,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'token'   => $token
        ]);
    }

    /**
     * POST /api/auth/login
     * Body: { "email": "...", "password": "..." }
     */
    public function login(): void {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation
        if (empty($data['email']) || empty($data['password'])) {
            Response::error('Email and password are required', 400);
        }

        // Find user
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_email = ?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            Response::error('Invalid email or password', 401);
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify password
        if (!password_verify($data['password'], $user['user_pass'])) {
            Response::error('Invalid email or password', 401);
        }

        // Generate JWT
        $token = JWTHelper::generateToken([
            'user_id' => $user['user_id'],
            'email'   => $user['user_email'],
            'name'    => $user['user_name'],
            'role'    => (int)$user['user_role']
        ]);

        // Save token
        $stmt = $this->db->prepare("UPDATE users SET api_token = ? WHERE user_id = ?");
        $stmt->bind_param("si", $token, $user['user_id']);
        $stmt->execute();
        $stmt->close();

        Response::success([
            'user_id' => $user['user_id'],
            'name'    => $user['user_name'],
            'email'   => $user['user_email'],
            'role'    => (int)$user['user_role'],
            'token'   => $token
        ]);
    }
}
