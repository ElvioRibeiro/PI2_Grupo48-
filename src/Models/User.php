<?php

declare(strict_types=1);

namespace Src\Models;

use PDO;
use PDOException;

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new user.
     *
     * @param string $nome
     * @param string $email
     * @param string $senha Raw password
     * @return bool True on success, false on failure.
     */
    public function create(string $nome, string $email, string $senha): bool
    {
        $hashedSenha = password_hash($senha, PASSWORD_ARGON2ID);
        if ($hashedSenha === false) {
            // Log error: password_hash failed
            error_log("User Model: password_hash failed for email: " . $email);
            return false;
        }

        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':senha', $hashedSenha, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error, e.g., duplicate email
            error_log("User Model Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds a user by email.
     *
     * @param string $email
     * @return array|null User data as an associative array, or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = :email LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("User Model FindByEmail Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Finds a user by ID.
     *
     * @param int $id
     * @return array|null User data as an associative array, or null if not found.
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT id, nome, email FROM usuarios WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("User Model FindById Error: " . $e->getMessage());
            return null;
        }
    }
}
