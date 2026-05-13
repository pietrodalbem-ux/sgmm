<!DOCTYPE html>
<html lang="pt-br">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SGM • Login</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

:root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --bg: #f8fafc;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --radius: 28px;
}

*{
    font-family:'Inter', sans-serif;
    box-sizing: border-box;
}

body{
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--bg);
    background-image: 
        radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
    overflow: hidden;
    margin: 0;
}

/* Decorative Background Elements */
.blob {
    position: absolute;
    width: 500px;
    height: 500px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.1));
    filter: blur(80px);
    border-radius: 50%;
    z-index: -1;
    animation: move 20s infinite alternate;
}

.blob-1 { top: -100px; left: -100px; }
.blob-2 { bottom: -100px; right: -100px; animation-delay: -5s; }

@keyframes move {
    from { transform: translate(0, 0) scale(1); }
    to { transform: translate(50px, 50px) scale(1.1); }
}

/* Login Card */
.login-card{
    width: 100%;
    max-width: 440px;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: var(--radius);
    padding: 3rem;
    box-shadow: 
        0 20px 25px -5px rgba(0, 0, 0, 0.05),
        0 8px 10px -6px rgba(0, 0, 0, 0.05),
        inset 0 0 20px rgba(255, 255, 255, 0.5);
    animation: slideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 10;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.logo-container {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary), #3b82f6);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
    color: white;
    font-size: 1.75rem;
    font-weight: 800;
}

.header-text {
    text-align: center;
    margin-bottom: 2.5rem;
}

.header-text h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.5rem;
}

.header-text p {
    color: var(--text-muted);
    font-size: 0.95rem;
}

/* Inputs */
.input-group {
    background: white;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    margin-bottom: 1.25rem;
}

.input-group:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.input-group-icon {
    color: var(--text-muted);
    font-size: 1.2rem;
    margin-right: 0.75rem;
    display: flex;
    align-items: center;
}

.input-group input {
    border: none;
    background: none;
    width: 100%;
    padding: 0.5rem 0;
    font-size: 1rem;
    color: var(--text-main);
    outline: none;
}

.input-group input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

/* Button */
.btn-login {
    width: 100%;
    padding: 1rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 16px;
    font-size: 1rem;
    font-weight: 700;
    margin-top: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-login:hover {
    background: var(--primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
}

.btn-login:active {
    transform: translateY(0);
}

#mensagem {
    font-size: 0.85rem;
    text-align: center;
    margin-bottom: 1rem;
    min-height: 1.25rem;
}

.footer-text {
    text-align: center;
    margin-top: 2rem;
    font-size: 0.8rem;
    color: var(--text-muted);
}

</style>

</head>

<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="login-card">

    <div class="logo-container">
        SGM
    </div>

    <div class="header-text">
        <h1>Bem-vindo</h1>
        <p>Sistema de Gestão de Manutenção</p>
    </div>

    <form id="formLogin">

        <div class="input-group">
            <div class="input-group-icon">
                <i class="bi bi-envelope"></i>
            </div>
            <input
                type="email"
                id="email"
                placeholder="E-mail institucional"
                autocomplete="username"
                required
            >
        </div>

        <div class="input-group">
            <div class="input-group-icon">
                <i class="bi bi-lock"></i>
            </div>
            <input
                type="password"
                id="senha"
                placeholder="Sua senha"
                autocomplete="current-password"
                required
            >
        </div>

        <div id="mensagem" class="text-danger"></div>

        <button type="submit" class="btn-login">
            Acessar Sistema
            <i class="bi bi-arrow-right"></i>
        </button>

    </form>

    <div class="footer-text">
        Acesso restrito ao SENAI.
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/sgm-ui.js"></script>
<script src="assets/js/login.js"></script>

</body>
</html>