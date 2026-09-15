<?php
/**
 * Database Connection Configuration
 * Reads credentials from .env file (never hardcoded)
 */

class Database {
    private static ?Database $instance = null;
    private ?mysqli $connection = null;

    private string $host;
    private int    $port;
    private string $database;
    private string $username;
    private string $password;

    private function __construct() {
        $this->loadEnv();
        $this->host     = $_ENV['DB_HOST']     ?? 'database';
        $this->port     = (int)($_ENV['DB_PORT'] ?? 3306);
        $this->database = $_ENV['DB_DATABASE'] ?? 'project';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
    }

    /**
     * Parse .env file and load variables into $_ENV
     */
    private function loadEnv(): void {
        $envFile = __DIR__ . '/../../.env';
        if (!file_exists($envFile)) {
            return;
        }
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key   = trim($parts[0]);
                $value = trim($parts[1]);
                $_ENV[$key]    = $value;
                putenv("$key=$value");
            }
        }
    }

    /**
     * Singleton: get the single Database instance
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get the mysqli connection (lazy-loaded)
     */
    public function getConnection(): mysqli {
        if ($this->connection === null) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            try {
                $this->connection = new mysqli(
                    $this->host,
                    $this->username,
                    $this->password,
                    $this->database,
                    $this->port
                );
                $this->connection->set_charset('utf8mb4');
            } catch (\Throwable $e) {
                error_log("Database connection failed: " . $e->getMessage());
                if (!headers_sent()) {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=utf-8');
                }
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Database connection failed'
                ]);
                exit;
            }
        }
        return $this->connection;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}
}
