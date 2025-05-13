<?php

declare(strict_types=1);

namespace Src\Controllers;

use PDO;
use Src\Models\User;

class UserController
{
    private PDO $db;
    private User $userModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->userModel = new User($this->db);
    }

    private function sanitize(string $data): string
    {
        return htmlspecialchars(strip_tags($data), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function redirect(string $url): void
    {
        header("Location: " . $url);
        exit;
    }

    public function showRegistrationForm(): void
    {
        // Clear any previous error/success messages
        unset($_SESSION['error_message']);
        unset($_SESSION['success_message']);
        require_once BASE_PATH . '/views/register.php';
    }

    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/?route=register');
        }

        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'] ?? ''; // Raw password, model will hash
        $confirm_senha = $_POST['confirm_senha'] ?? '';

        // Basic Validations
        if (empty($nome) || empty($email) || empty($senha) || empty($confirm_senha)) {
            $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
            $this->redirect('/?route=register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Formato de e-mail inválido.";
            $this->redirect('/?route=register');
        }

        if (strlen($senha) < 6) {
            $_SESSION['error_message'] = "A senha deve ter pelo menos 6 caracteres.";
            $this->redirect('/?route=register');
        }

        if ($senha !== $confirm_senha) {
            $_SESSION['error_message'] = "As senhas não coincidem.";
            $this->redirect('/?route=register');
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error_message'] = "Este e-mail já está cadastrado.";
            $this->redirect('/?route=register');
        }

        // Attempt to create user
        if ($this->userModel->create($nome, $email, $senha)) {
            $_SESSION['success_message'] = "Cadastro realizado com sucesso! Faça o login.";
            $this->redirect('/?route=login');
        } else {
            $_SESSION['error_message'] = "Erro ao realizar o cadastro. Tente novamente.";
            $this->redirect('/?route=register');
        }
    }

    public function showLoginForm(): void
    {
        // Clear any previous error/success messages from other pages if needed
        // unset($_SESSION['error_message']); // Keep error from failed login attempt
        // unset($_SESSION['success_message']); // Keep success from registration
        require_once BASE_PATH . '/views/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/?route=login');
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            $_SESSION['error_message'] = "E-mail e senha são obrigatórios.";
            $this->redirect('/?route=login');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error_message'] = "Formato de e-mail inválido.";
            $this->redirect('/?route=login');
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($senha, $user['senha'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome']; // Store user's name for display
            $_SESSION['success_message'] = "Login realizado com sucesso!";
            $this->redirect('/?route=ads'); // Or dashboard
        } else {
            $_SESSION['error_message'] = "E-mail ou senha inválidos.";
            $this->redirect('/?route=login');
        }
    }

    public function logout(): void
    {
        session_unset(); // Remove all session variables
        session_destroy(); // Destroy the session
        // Optional: set a logged out message if desired, but usually just redirect
        // $_SESSION['success_message'] = "Você foi desconectado.";
        $this->redirect('/?route=login');
    }
}
