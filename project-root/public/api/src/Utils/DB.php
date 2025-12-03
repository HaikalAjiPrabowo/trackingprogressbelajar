<?php
namespace Utils;

use PDO;
use PDOException;

class DB {

    private static $instance = null;

    public static function conn() {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = "localhost";
        $db   = "tracking";
        $user = "root";
        $pass = "";
        $charset = "utf8mb4";

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        try {
            self::$instance = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            return self::$instance;

        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }
}
