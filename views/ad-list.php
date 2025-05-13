<?php
require_once BASE_PATH . '/views/header.php';

// This view expects $ads to be set by the controller
if (!isset($ads) || !is_array($ads)) {
    // Fallback if $ads is not set, though controller should handle this
    $ads = []; // Initialize as empty array to prevent errors
    // Optionally, display an error message if appropriate here,
    // but usually, the controller would handle "no ads found" logic.
}

$currentUserId = $_SESSION['user_id'] ?? null;
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Animais para Adoção</h2>
        <?php if ($currentUserId): ?>
            <a href="/?route=create-ad" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill"></i> Novo Anúncio
            </a>
        <?php endif; ?>
    </div>

    <?php if (empty($ads)): ?>
        <div class="alert alert-info text-center" role="alert">
            <h4 class="alert-heading">Nenhum anúncio encontrado!</h4>
            <p>Ainda não há animais cadastrados para adoção. <?php if ($currentUserId): ?>Que tal <a href="/?route=create-ad" class="alert-link">criar o primeiro anúncio</a>?<?php else: ?>Volte mais tarde ou <a href="/?route=register" class="alert-link">crie uma conta</a> para anunciar.<?php endif; ?></p>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($ads as $ad): ?>
                <?php
                $adId = htmlspecialchars((string)($ad['id'] ?? ''), ENT_QUOTES, 'UTF-8');
                $titulo = htmlspecialchars($ad['titulo'] ?? 'Sem título', ENT_QUOTES, 'UTF-8');
                $descricao = htmlspecialchars($ad['descricao'] ?? 'Sem descrição', ENT_QUOTES, 'UTF-8');
                $foto = $ad['foto'] ? htmlspecialchars($ad['foto'], ENT_QUOTES, 'UTF-8') : null;
                $usuarioNome = htmlspecialchars($ad['usuario_nome'] ?? 'Doador anônimo', ENT_QUOTES, 'UTF-8');
                $dataCriacao = isset($ad['criado_em']) ? date_format(date_create($ad['criado_em']), 'd/m/Y H:i') : 'Data indisponível';
                $isOwner = $currentUserId && isset($ad['usuario_id']) && $currentUserId == $ad['usuario_id'];
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <?php if ($foto): ?>
                            <img src="/uploads/<?= $foto ?>" class="card-img-top card-img-top-anuncios" alt="<?= $titulo ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200.png?text=Sem+Foto" class="card-img-top card-img-top-anuncios" alt="Sem foto">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= $titulo ?></h5>
                            <p class="card-text flex-grow-1"><?= nl2br(mb_strimwidth($descricao, 0, 150, "...")) ?></p>
                            <p class="card-text"><small class="text-muted">Publicado por: <?= $usuarioNome ?></small></p>
                            <p class="card-text"><small class="text-muted">Em: <?= $dataCriacao ?></small></p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <?php if ($isOwner): ?>
                                <div class="btn-group w-100" role="group">
                                    <a href="/?route=edit-ad&id=<?= $adId ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <a href="/?route=delete-ad&id=<?= $adId ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir este anúncio? Esta ação não pode ser desfeita.');">
                                        <i class="bi bi-trash-fill"></i> Excluir
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Placeholder for contact button or more info for non-owners -->
                                <a href="#" class="btn btn-sm btn-outline-primary w-100 disabled">Ver Detalhes (em breve)</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/views/footer.php'; ?>
