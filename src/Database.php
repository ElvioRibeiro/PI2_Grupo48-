<?php

namespace Src;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $instance = null;
    private static array $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // The constructor is private to prevent direct creation of object
    private function __construct()
    {
    }

    // The clone method is private to prevent cloning of the instance
    private function __clone()
    {
    }

    // The wakeup method is private to prevent unserialization of the instance
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function get(): PDO
    {
        if (self::$instance === null) {
            $dbHost = getenv('DB_HOST') ?: 'db'; // Default to 'db' if not set
            $dbName = getenv('DB_NAME') ?: 'doapet'; // Default to 'doapet'
            $dbUser = getenv('DB_USER') ?: 'root';   // Default to 'root'
            $dbPass = getenv('DB_PASS') ?: 'secret'; // Default to 'secret'
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$dbHost};dbname={$dbName};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $dbUser, $dbPass, self::$options);
            } catch (PDOException $e) {
                // In a real application, you would log this error and possibly throw a custom exception
                error_log("Database Connection Error: " . $e->getMessage());
                // For PoC, rethrow or die with a more user-friendly message if needed outside of index.php
                // Since index.php already has a try-catch for Database::get(), this will be caught there.
                throw $e; 
            }
        }
        return self::$instance;
    }
}
