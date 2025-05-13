<?php require_once BASE_PATH . '/views/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <form id="loginForm" action="/?route=login" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Endereço de E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Por favor, informe um e-mail válido.</div>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                        <div class="invalid-feedback">Por favor, informe sua senha.</div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Entrar</button>
                </form>
            </div>
            <div class="card-footer text-center">
                <p class="mb-0">Não tem uma conta? <a href="/?route=register">Registre-se aqui</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/footer.php'; ?>
