document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const senha = document.getElementById('senha').value;
    const msg = document.getElementById('mensagem');

    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, senha: senha })
        });

        // Debug: Veja o que o PHP está retornando no console do navegador (F12)
        const textoRetorno = await response.text();
        console.log("Resposta do Servidor:", textoRetorno);
        
        const result = JSON.parse(textoRetorno);

        if (result.success) {
            window.location.href = 'dashboard.php';
        } else {
            const m = result.message || 'Credenciais inválidas.';
            msg.innerText = m;
            if (window.SGM && SGM.toast) SGM.toast(m, 'error');
        }
    } catch (error) {
        console.error("Erro na requisição:", error);
        const m = "Erro ao conectar com o servidor.";
        msg.innerText = m;
        if (window.SGM && SGM.toast) SGM.toast(m, 'error');
    }
});



function confirmarLogout() {
    return confirm("Você realmente deseja sair?");
}
