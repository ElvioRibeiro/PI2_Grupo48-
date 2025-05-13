<?php require_once BASE_PATH . '/views/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar Nova Conta</h4>
            </div>
            <div class="card-body">
                <form id="registerForm" action="/?route=register" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                        <div class="invalid-feedback">Por favor, informe seu nome.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Endereço de E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required minlength="6">
                        <div class="invalid-feedback">A senha deve ter pelo menos 6 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_senha" class="form-label">Confirmar Senha</label>
                        <input type="password" class="form-control" id="confirm_senha" name="confirm_senha" required>
                        <div class="invalid-feedback">As senhas não coincidem.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrar</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Já tem uma conta? <a href="/?route=login">Faça Login</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/footer.php'; ?>
