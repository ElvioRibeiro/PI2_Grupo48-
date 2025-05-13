<?php
require_once BASE_PATH . '/views/header.php';

// This view expects $ad to be set by the controller
if (!isset($ad) || !is_array($ad)) {
    // Fallback if $ad is not set, though controller should handle this
    $_SESSION['error_message'] = "Anúncio não encontrado ou dados inválidos para edição.";
    header("Location: /?route=ads");
    exit;
}

// Ensure user is logged in and is the owner
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== $ad['usuario_id']) {
    $_SESSION['error_message'] = "Você não tem permissão para editar este anúncio.";
    header("Location: /?route=ads");
    exit;
}

// Retrieve form data from session if validation failed previously
$form_data = $_SESSION['form_data'] ?? null;
unset($_SESSION['form_data']);

$titulo = htmlspecialchars($form_data['titulo'] ?? $ad['titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$descricao = htmlspecialchars($form_data['descricao'] ?? $ad['descricao'] ?? '', ENT_QUOTES, 'UTF-8');
$current_foto = $ad['foto'] ?? null; // Path relative to uploads folder
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Editar Anúncio</h4>
            </div>
            <div class="card-body">
                <form id="editAdForm" action="/?route=edit-ad&id=<?= htmlspecialchars($ad['id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Anúncio</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?= $titulo ?>" required maxlength="120">
                        <div class="invalid-feedback">Por favor, informe o título do anúncio.</div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição do Animal</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="5" required><?= $descricao ?></textarea>
                        <div class="invalid-feedback">Por favor, forneça uma descrição.</div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Alterar Foto do Animal (Opcional)</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg, image/png, image/gif">
                        <div class="form-text">Deixe em branco para manter a foto atual. Formatos: JPG, PNG, GIF.</div>
                        <?php if ($current_foto): ?>
                            <div class="mt-2">
                                <p>Foto Atual:</p>
                                <img src="/uploads/<?= htmlspecialchars($current_foto, ENT_QUOTES, 'UTF-8') ?>" alt="Foto atual do animal" style="max-width: 200px; max-height: 200px; border-radius: 0.25rem;">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_foto" name="remove_foto">
                                    <label class="form-check-label" for="remove_foto">
                                        Remover foto atual (deixe o campo "Alterar Foto" vazio)
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning text-dark">Salvar Alterações</button>
                        <a href="/?route=ads" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/footer.php'; ?>
