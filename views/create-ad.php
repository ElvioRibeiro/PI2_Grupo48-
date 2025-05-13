<?php
require_once BASE_PATH . '/views/header.php';

// Ensure user is logged in to access this page
if (!isset($_SESSION['user_id'])) {
    // This check should ideally be in the controller, redirecting before view is loaded.
    // Adding a fallback here just in case.
    $_SESSION['error_message'] = "Você precisa estar logado para criar um anúncio.";
    header("Location: /?route=login");
    exit;
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Criar Novo Anúncio para Adoção</h4>
            </div>
            <div class="card-body">
                <form id="createAdForm" action="/?route=create-ad" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título do Anúncio</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="120">
                        <div class="invalid-feedback">Por favor, informe o título do anúncio.</div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição do Animal</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="5" required></textarea>
                        <div class="invalid-feedback">Por favor, forneça uma descrição.</div>
                    </div>
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto do Animal (Opcional)</label>
                        <input type="file" class="form-control" id="foto" name="foto" accept="image/jpeg, image/png, image/gif">
                        <div class="form-text">Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB (configuração do servidor).</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-info text-white">Publicar Anúncio</button>
                        <a href="/?route=ads" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/footer.php'; ?>
