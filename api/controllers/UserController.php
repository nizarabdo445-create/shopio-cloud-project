<?php
/**
 * User Controller
 * Admin-only user management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/Response.php';

class UserController {

    private mysqli $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET /api/users
     * List all users (Admin only)
     * Never exposes passwords
     */
    public function index(): void {
        AuthMiddleware::requireAdmin();

        $result = $this->db->query(
            "SELECT user_id, user_name, user_email, user_role, created_at, updated_at
             FROM users ORDER BY user_id DESC"
        );

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'user_id'    => (int)$row['user_id'],
                'name'       => $row['user_name'],
                'email'      => $row['user_email'],
                'role'       => (int)$row['user_role'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            ];
        }

        Response::success($users);
    }

    /**
     * DELETE /api/users/{id}
     * Delete a user (Admin only)
     * Cannot delete self
     */
    public function destroy(int $id): void {
        $admin = AuthMiddleware::requireAdmin();

        // Prevent admin from deleting themselves
        if ($admin['user_id'] === $id) {
            Response::error('Cannot delete your own account', 400);
        }

        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            Response::notFound('User not found');
        }
        $stmt->close();

        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        Response::noContent();
    }
}
