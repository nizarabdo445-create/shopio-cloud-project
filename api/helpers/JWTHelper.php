<?php
/**
 * JWT Token Helper
 * Simple JWT implementation using HMAC-SHA256
 * No external dependencies required
 */

class JWTHelper {

    /**
     * Generate a JWT token for a user
     */
    public static function generateToken(array $payload): string {
        $secret = self::getSecret();
        $expiry = (int)($_ENV['JWT_EXPIRY'] ?? 3600);

        $header = self::base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ]));

        $payload['iat'] = time();
        $payload['exp'] = time() + $expiry;
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));

        $signature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payloadEncoded", $secret, true)
        );

        return "$header.$payloadEncoded.$signature";
    }

    /**
     * Validate and decode a JWT token
     * Returns payload array on success, null on failure
     */
    public static function validateToken(string $token): ?array {
        $secret = self::getSecret();

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        // Verify signature
        $expectedSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        // Decode payload
        $data = json_decode(self::base64UrlDecode($payload), true);
        if ($data === null) {
            return null;
        }

        // Check expiration
        if (isset($data['exp']) && $data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    /**
     * Extract token from Authorization header
     * Expects: "Bearer <token>"
     */
    public static function extractFromHeader(): ?string {
        $headers = '';

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $headers = $requestHeaders['Authorization'] ?? '';
        }

        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get JWT secret from environment.
     * Fails securely if JWT_SECRET is missing or too short.
     */
    private static function getSecret(): string {
        $secret = $_ENV['JWT_SECRET'] ?? '';

        if (strlen($secret) < 32) {
            throw new RuntimeException(
                'JWT_SECRET must be configured and contain at least 32 characters.'
            );
        }

        return $secret;
    }

    /**
     * Encode data using Base64 URL-safe encoding
     */
    private static function base64UrlEncode(string $data): string {
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    /**
     * Decode Base64 URL-safe encoded data
     */
    private static function base64UrlDecode(string $data): string {
        return base64_decode(
            strtr($data, '-_', '+/')
        );
    }
}
