<?php

declare(strict_types=1);

namespace Src\Controllers;

use PDO;
use Src\Models\Ad;

class AdController
{
    private PDO $db;
    private Ad $adModel;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->adModel = new Ad($this->db);
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

    private function ensureLoggedIn(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = "Você precisa estar logado para acessar esta página.";
            $this->redirect('/?route=login');
        }
    }

    public function listAds(): void
    {
        $ads = $this->adModel->all();
        // Clear any previous error/success messages from other operations
        // unset($_SESSION['error_message']); // Keep if redirected with error
        // unset($_SESSION['success_message']); // Keep if redirected with success
        require_once BASE_PATH . '/views/ad-list.php';
    }

    public function showCreateAdForm(): void
    {
        $this->ensureLoggedIn();
        unset($_SESSION['error_message']); // Clear previous form errors
        require_once BASE_PATH . '/views/create-ad.php';
    }

    public function createAd(): void
    {
        $this->ensureLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/?route=create-ad');
        }

        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        $usuario_id = $_SESSION['user_id']; // Assuming user_id is stored in session

        // Basic Validations
        if (empty($titulo) || empty($descricao)) {
            $_SESSION['error_message'] = "Título e descrição são obrigatórios.";
            $this->redirect('/?route=create-ad');
        }
        
        $foto_path = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto_path = $this->adModel->handlePhotoUpload($_FILES['foto']);
            if ($foto_path === null && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) { // Check if it was an actual upload error
                $_SESSION['error_message'] = "Erro ao fazer upload da foto. Verifique o tipo ou tamanho do arquivo.";
                $this->redirect('/?route=create-ad');
            }
        } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other upload errors specifically if needed
             $_SESSION['error_message'] = "Ocorreu um erro com o upload da foto. Código: " . $_FILES['foto']['error'];
             $this->redirect('/?route=create-ad');
        }


        if ($this->adModel->create($usuario_id, $titulo, $descricao, $foto_path)) {
            $_SESSION['success_message'] = "Anúncio criado com sucesso!";
            $this->redirect('/?route=ads');
        } else {
            $_SESSION['error_message'] = "Erro ao criar o anúncio. Tente novamente.";
            // If photo was uploaded but DB failed, we might want to delete the orphaned photo
            if ($foto_path && file_exists(BASE_PATH . '/public/uploads/' . $foto_path)) {
                unlink(BASE_PATH . '/public/uploads/' . $foto_path);
            }
            $this->redirect('/?route=create-ad');
        }
    }

    public function showEditAdForm(int $adId): void
    {
        $this->ensureLoggedIn();
        $ad = $this->adModel->findById($adId);

        if (!$ad) {
            $_SESSION['error_message'] = "Anúncio não encontrado.";
            $this->redirect('/?route=ads');
        }

        // Authorization: Check if the logged-in user is the owner of the ad
        if ($ad['usuario_id'] !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Você não tem permissão para editar este anúncio.";
            $this->redirect('/?route=ads');
        }
        unset($_SESSION['error_message']); // Clear previous form errors
        require_once BASE_PATH . '/views/edit-ad.php';
    }

    public function editAd(int $adId): void
    {
        $this->ensureLoggedIn();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/?route=edit-ad&id=' . $adId);
        }

        $ad = $this->adModel->findById($adId);
        if (!$ad) {
            $_SESSION['error_message'] = "Anúncio não encontrado para edição.";
            $this->redirect('/?route=ads');
        }
        if ($ad['usuario_id'] !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Você não tem permissão para editar este anúncio.";
            $this->redirect('/?route=ads');
        }

        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (empty($titulo) || empty($descricao)) {
            $_SESSION['error_message'] = "Título e descrição são obrigatórios.";
            // Pass ad data back to form for pre-filling
            $_SESSION['form_data'] = ['titulo' => $titulo, 'descricao' => $descricao, 'foto' => $ad['foto']];
            $this->redirect('/?route=edit-ad&id=' . $adId);
        }

        $current_foto_path = $ad['foto'];
        $new_foto_path = $current_foto_path; // Assume photo doesn't change unless new one is uploaded

        // Handle photo upload if a new photo is provided
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploaded_path = $this->adModel->handlePhotoUpload($_FILES['foto']);
            if ($uploaded_path) {
                $new_foto_path = $uploaded_path;
                // Delete old photo if it exists and is different from new one
                if ($current_foto_path && $current_foto_path !== $new_foto_path && file_exists(BASE_PATH . '/public/uploads/' . $current_foto_path)) {
                    unlink(BASE_PATH . '/public/uploads/' . $current_foto_path);
                }
            } elseif ($_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                 $_SESSION['error_message'] = "Erro ao fazer upload da nova foto. O anúncio não foi atualizado.";
                 $this->redirect('/?route=edit-ad&id=' . $adId);
            }
        } elseif (isset($_POST['remove_foto']) && $_POST['remove_foto'] == '1') {
            // If user explicitly wants to remove the photo
            if ($current_foto_path && file_exists(BASE_PATH . '/public/uploads/' . $current_foto_path)) {
                unlink(BASE_PATH . '/public/uploads/' . $current_foto_path);
            }
            $new_foto_path = null;
        }


        if ($this->adModel->update($adId, $titulo, $descricao, $new_foto_path)) {
            $_SESSION['success_message'] = "Anúncio atualizado com sucesso!";
            unset($_SESSION['form_data']);
            $this->redirect('/?route=ads');
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar o anúncio. Tente novamente.";
            // If a new photo was uploaded but DB update failed, delete the newly uploaded photo to prevent orphans
            if ($new_foto_path && $new_foto_path !== $current_foto_path && file_exists(BASE_PATH . '/public/uploads/' . $new_foto_path)) {
                 unlink(BASE_PATH . '/public/uploads/' . $new_foto_path);
            }
            $_SESSION['form_data'] = ['titulo' => $titulo, 'descricao' => $descricao, 'foto' => $current_foto_path]; // Keep current photo if new one failed
            $this->redirect('/?route=edit-ad&id=' . $adId);
        }
    }

    public function deleteAd(int $adId): void
    {
        $this->ensureLoggedIn();
        $ad = $this->adModel->findById($adId);

        if (!$ad) {
            $_SESSION['error_message'] = "Anúncio não encontrado para exclusão.";
            $this->redirect('/?route=ads');
        }

        if ($ad['usuario_id'] !== $_SESSION['user_id']) {
            $_SESSION['error_message'] = "Você não tem permissão para excluir este anúncio.";
            $this->redirect('/?route=ads');
        }

        if ($this->adModel->delete($adId)) {
            $_SESSION['success_message'] = "Anúncio excluído com sucesso!";
        } else {
            $_SESSION['error_message'] = "Erro ao excluir o anúncio. Tente novamente.";
        }
        $this->redirect('/?route=ads');
    }
}
