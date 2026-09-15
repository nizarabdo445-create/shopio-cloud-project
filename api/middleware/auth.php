<?php
/**
 * Authentication Middleware
 * Validates JWT token and extracts user data
 */

require_once __DIR__ . '/../helpers/JWTHelper.php';

class AuthMiddleware {

    /**
     * Require authentication — stops execution if invalid token
     * Returns decoded user payload
     */
    public static function requireAuth(): array {
        $token = JWTHelper::extractFromHeader();

        if ($token === null) {
            http_response_code(401);
            echo json_encode([
                'error' => 'Authentication required',
                'message' => 'Missing or invalid Authorization header. Use: Bearer <token>'
            ]);
            exit;
        }

        $payload = JWTHelper::validateToken($token);

        if ($payload === null) {
            http_response_code(401);
            echo json_encode([
                'error' => 'Invalid token',
                'message' => 'Token is invalid or expired. Please login again.'
            ]);
            exit;
        }

        return $payload;
    }

    /**
     * Require admin role — stops execution if not admin
     */
    public static function requireAdmin(): array {
        $user = self::requireAuth();

        if (($user['role'] ?? 0) !== 1) {
            http_response_code(403);
            echo json_encode([
                'error' => 'Forbidden',
                'message' => 'Admin access required.'
            ]);
            exit;
        }

        return $user;
    }

    /**
     * Optional auth — returns user data if token present, null otherwise
     */
    public static function optionalAuth(): ?array {
        $token = JWTHelper::extractFromHeader();
        if ($token === null) {
            return null;
        }
        return JWTHelper::validateToken($token);
    }
}
