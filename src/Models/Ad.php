<?php

declare(strict_types=1);

namespace Src\Models;

use PDO;
use PDOException;

class Ad
{
    private PDO $db;
    private const UPLOADS_DIR = BASE_PATH . '/public/uploads/';

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Creates a new ad.
     *
     * @param int $usuario_id
     * @param string $titulo
     * @param string $descricao
     * @param string|null $foto_path Relative path to the photo from public/uploads, or null.
     * @return bool True on success, false on failure.
     */
    public function create(int $usuario_id, string $titulo, string $descricao, ?string $foto_path): bool
    {
        $sql = "INSERT INTO anuncios (usuario_id, titulo, descricao, foto) 
                VALUES (:usuario_id, :titulo, :descricao, :foto)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            if ($foto_path === null) {
                $stmt->bindParam(':foto', $foto_path, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':foto', $foto_path, PDO::PARAM_STR);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Ad Model Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all ads.
     *
     * @return array List of ads.
     */
    public function all(): array
    {
        // Fetch ads along with user's name for display
        $sql = "SELECT a.*, u.nome as usuario_nome 
                FROM anuncios a
                JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.criado_em DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ad Model All Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Finds an ad by its ID.
     *
     * @param int $id
     * @return array|null Ad data or null if not found.
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT a.*, u.nome as usuario_nome
                FROM anuncios a 
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $ad = $stmt->fetch(PDO::FETCH_ASSOC);
            return $ad ?: null;
        } catch (PDOException $e) {
            error_log("Ad Model FindById Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves all ads by a specific user.
     *
     * @param int $usuario_id
     * @return array List of ads for the user.
     */
    public function findByUser(int $usuario_id): array
    {
        $sql = "SELECT * FROM anuncios WHERE usuario_id = :usuario_id ORDER BY criado_em DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Ad Model FindByUser Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Updates an existing ad.
     *
     * @param int $id Ad ID.
     * @param string $titulo
     * @param string $descricao
     * @param string|null $foto_path New photo path, or null if not changed or removed.
     *                                If an existing photo needs to be kept, this should be its current path.
     *                                If a new photo is uploaded, this is the new path.
     *                                If photo is removed, this should be null.
     * @return bool True on success, false on failure.
     */
    public function update(int $id, string $titulo, string $descricao, ?string $foto_path): bool
    {
        // If $foto_path is explicitly set (even to null for removal), update the foto column.
        // If $foto_path is not part of the update (e.g. photo not changed), we might omit it from SQL.
        // For simplicity, this method assumes $foto_path always reflects the desired state.
        $sql = "UPDATE anuncios SET titulo = :titulo, descricao = :descricao, foto = :foto 
                WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
            if ($foto_path === null) {
                $stmt->bindParam(':foto', $foto_path, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':foto', $foto_path, PDO::PARAM_STR);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Ad Model Update Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an ad. Also attempts to delete its associated photo if it exists.
     *
     * @param int $id Ad ID.
     * @return bool True on success, false on failure.
     */
    public function delete(int $id): bool
    {
        // First, get the ad to find its photo path for deletion
        $ad = $this->findById($id);

        if ($ad && !empty($ad['foto'])) {
            $photoFullPath = self::UPLOADS_DIR . basename($ad['foto']); // basename to prevent path traversal
            if (file_exists($photoFullPath)) {
                @unlink($photoFullPath); // Suppress errors if unlink fails, but ideally log them
            }
        }

        $sql = "DELETE FROM anuncios WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Ad Model Delete Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handles photo upload.
     *
     * @param array $fileInfo The $_FILES['foto'] array.
     * @return string|null The relative path to the uploaded file (e.g., 'unique_name.jpg') on success, or null on failure/no file.
     */
    public function handlePhotoUpload(array $fileInfo): ?string
    {
        if (isset($fileInfo['error']) && $fileInfo['error'] === UPLOAD_ERR_OK) {
            if (!is_dir(self::UPLOADS_DIR) && !mkdir(self::UPLOADS_DIR, 0775, true)) {
                error_log("Ad Model: Failed to create uploads directory: " . self::UPLOADS_DIR);
                return null; // Failed to create directory
            }

            $fileName = basename($fileInfo['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedExtensions)) {
                error_log("Ad Model: Invalid file type: " . $fileExtension);
                return null; // Invalid file type
            }

            // Sanitize filename and make it unique
            $safeFileName = preg_replace("/[^a-zA-Z0-9-_\.]/", "", str_replace(" ", "_", $fileName));
            $uniqueFileName = uniqid('', true) . '_' . $safeFileName;
            $destination = self::UPLOADS_DIR . $uniqueFileName;

            if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
                return $uniqueFileName; // Return only the filename for DB storage (relative to uploads dir)
            } else {
                error_log("Ad Model: Failed to move uploaded file to: " . $destination);
                return null; // Failed to move file
            }
        } elseif (isset($fileInfo['error']) && $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file was uploaded, which is fine.
        } else {
            // Some other upload error occurred
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
                UPLOAD_ERR_FORM_SIZE  => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
                UPLOAD_ERR_PARTIAL    => "The uploaded file was only partially uploaded.",
                UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
                UPLOAD_ERR_EXTENSION  => "A PHP extension stopped the file upload.",
            ];
            $errorCode = $fileInfo['error'] ?? 'unknown';
            $errorMessage = $uploadErrors[$errorCode] ?? "An unknown error occurred during file upload.";
            error_log("Ad Model Photo Upload Error: " . $errorMessage . " (Code: {$errorCode})");
            return null;
        }
    }
}
