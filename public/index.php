<?php

declare(strict_types=1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base path for includes
define('BASE_PATH', dirname(__DIR__));

// Autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Helper functions (optional, if you create a helpers.php)
// require_once BASE_PATH . '/src/helpers.php';

// Load environment variables (simple approach for PoC)
// In a real app, use a library like phpdotenv
// For Docker, these might be passed as environment variables to the container
// and getenv() will work directly.
// Ensure your .env file is loaded or variables are set in docker-compose.yml for the web service.
// The Database.php relies on getenv().

use Src\Controllers\UserController;
use Src\Controllers\AdController;
use Src\Database;

// Basic Router
$route = $_GET['route'] ?? 'home'; // Default route
$method = $_SERVER['REQUEST_METHOD'];

// Initialize PDO connection (or get instance)
try {
    $pdo = Database::get();
} catch (\PDOException $e) {
    // In a real app, log this error and show a user-friendly error page
    error_log("Database Connection Error: " . $e->getMessage());
    die("Error connecting to the database. Please try again later. Details: " . $e->getMessage());
}

$userController = new UserController($pdo);
$adController = new AdController($pdo);

// Public routes (accessible without login)
if ($route === 'home' && $method === 'GET') {
    $adController->listAds();
} elseif ($route === 'register' && $method === 'GET') {
    $userController->showRegistrationForm();
} elseif ($route === 'register' && $method === 'POST') {
    $userController->register();
} elseif ($route === 'login' && $method === 'GET') {
    $userController->showLoginForm();
} elseif ($route === 'login' && $method === 'POST') {
    $userController->login();
} elseif ($route === 'logout' && $method === 'GET') {
    $userController->logout();
}
// Routes requiring authentication
// A simple check for a session variable.
// In a more robust app, you might have a middleware or a more sophisticated auth check.
elseif (isset($_SESSION['user_id'])) {
    if ($route === 'dashboard' && $method === 'GET') { // Alias for ad list or a dedicated dashboard
        $adController->listAds();
    } elseif ($route === 'ads' && $method === 'GET') { // Explicit route for listing ads
        $adController->listAds();
    } elseif ($route === 'create-ad' && $method === 'GET') {
        $adController->showCreateAdForm();
    } elseif ($route === 'create-ad' && $method === 'POST') {
        $adController->createAd();
    } elseif ($route === 'edit-ad' && $method === 'GET') {
        $adId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($adId) {
            $adController->showEditAdForm($adId);
        } else {
            // Handle error: ID not provided or invalid
            $_SESSION['error_message'] = "ID do anúncio inválido.";
            header("Location: /?route=ads");
            exit;
        }
    } elseif ($route === 'edit-ad' && $method === 'POST') {
        $adId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Or from POST data if preferred
        if ($adId) {
            $adController->editAd($adId);
        } else {
            $_SESSION['error_message'] = "ID do anúncio inválido para edição.";
            header("Location: /?route=ads");
            exit;
        }
    } elseif ($route === 'delete-ad' && $method === 'GET') {
        $adId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($adId) {
            $adController->deleteAd($adId);
        } else {
            $_SESSION['error_message'] = "ID do anúncio inválido para exclusão.";
            header("Location: /?route=ads");
            exit;
        }
    } else {
        // Authenticated user, but route not found or not allowed
        http_response_code(404);
        // You can include a specific 404 view for authenticated users
        // For simplicity, redirect to their dashboard/ad list
        $_SESSION['error_message'] = "Página não encontrada ou acesso não permitido.";
        header("Location: /?route=ads");
        exit;
    }
}
// If not logged in and trying to access a protected route (or any other unhandled route)
else {
    // If the route is one that requires login, redirect to login.
    // Otherwise, it could be a 404 for non-logged-in users.
    // For this PoC, any route not explicitly public and not handled by logged-in logic
    // will redirect to login or show a generic error/home.
    // Let's default to redirecting to login for simplicity if it's not a known public route.
    $publicRoutes = ['home', 'register', 'login'];
    if (!in_array($route, $publicRoutes)) {
        $_SESSION['error_message'] = "Você precisa estar logado para acessar esta página.";
        header("Location: /?route=login");
        exit;
    } else {
        // This case should ideally be handled by the public routes section
        // If it reaches here, it's an unknown public route or an issue.
        http_response_code(404);
        echo "<h1>404 - Página Não Encontrada</h1>";
        echo "<p>A rota solicitada '{$route}' não foi encontrada.</p>";
        echo "<a href='/?route=home'>Voltar para Home</a>";
        exit;
    }
}

?>
