<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — SGM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/sgm-institutional.css">
</head>
<body class="sgm-login">

<div id="toastStack" class="sgm-toast-stack"></div>

<div class="sgm-login-card animate-fade">
    <div class="sgm-login-brand">
        <div class="sgm-logo">SG<span>M</span></div>
        <p class="text-muted small mb-0">Sistema de Gestão da Manutenção — SENAI</p>
    </div>

    <h1 class="h5 fw-bold text-center mb-4">Acesso ao portal</h1>

    <form id="formLogin">
        <div class="mb-3 text-start">
            <label class="sgm-form-label" for="email">E-mail institucional</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                <input type="email" id="email" class="form-control sgm-control border-start-0" placeholder="nome@senai.br" required autocomplete="username">
            </div>
        </div>
        <div class="mb-3 text-start">
            <label class="sgm-form-label" for="senha">Senha</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                <input type="password" id="senha" class="form-control sgm-control border-start-0" placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>
        <div id="mensagem" class="small text-danger mb-3 min-h" role="alert"></div>
        <button type="submit" class="btn sgm-btn-primary w-100 py-2">
            Entrar <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <p class="text-center text-muted small mt-4 mb-0">Uso restrito a usuários autorizados da instituição.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sgm-ui.js"></script>
<script src="assets/js/login.js"></script>
</body>
</html>
