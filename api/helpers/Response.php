<?php
/**
 * JSON Response Helper
 * Standardizes all API responses
 */

class Response {

    public static function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public static function success(mixed $data, int $code = 200): void {
        self::json(['status' => 'success', 'data' => $data], $code);
    }

    public static function created(mixed $data): void {
        self::json(['status' => 'success', 'data' => $data], 201);
    }

    public static function noContent(): void {
        http_response_code(204);
        exit;
    }

    public static function error(string $message, int $code = 400, ?array $errors = null): void {
        $response = ['status' => 'error', 'message' => $message];
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        self::json($response, $code);
    }

    public static function notFound(string $message = 'Resource not found'): void {
        self::error($message, 404);
    }

    public static function serverError(string $message = 'Internal server error'): void {
        self::error($message, 500);
    }
}
