<?php
namespace Utils;

class DB {
    private static $conn = null;

    public static function conn() {
        if (self::$conn !== null) return self::$conn;

        $host = '127.0.0.1';
        $db   = 'tracking_db';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            self::$conn = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ]);
            return self::$conn;
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "DB failed: " . $e->getMessage()
            ]);
            exit;
        }
    }
}
