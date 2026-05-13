(function () {
    'use strict';

    document.getElementById('foto').addEventListener('change', function (e) {
        var container = document.getElementById('preview-container');
        var img = document.getElementById('img-preview');
        var file = e.target.files[0];
        if (file) {
            img.src = URL.createObjectURL(file);
            container.classList.remove('d-none');
        }
    });

    async function iniciar() {
        try {
            var resB = await fetch('api/localizacoes.php?acao=listar_blocos');
            var blocos = await resB.json();
            var selB = document.getElementById('selectBloco');
            if (Array.isArray(blocos)) {
                blocos.forEach(function (b) {
                    selB.innerHTML += '<option value="' + b.id_bloco + '">' + SGM.escapeHtml(b.nome) + '</option>';
                });
            }
            var resT = await fetch('api/localizacoes.php?acao=listar_tipos');
            var tipos = await resT.json();
            var selT = document.getElementById('selectTipo');
            if (Array.isArray(tipos)) {
                tipos.forEach(function (t) {
                    selT.innerHTML +=
                        '<option value="' + t.id_tipo + '">' + SGM.escapeHtml(t.nome) + '</option>';
                });
            }
        } catch (err) {
            SGM.toast('Erro ao carregar listas iniciais.', 'error');
        }
    }

    async function carregarAmbientes(id_bloco) {
        var selA = document.getElementById('selectAmbiente');
        if (!id_bloco) {
            selA.disabled = true;
            return;
        }
        var res = await fetch('api/localizacoes.php?acao=listar_ambientes&id_bloco=' + encodeURIComponent(id_bloco));
        var ambientes = await res.json();
        selA.innerHTML = '<option value="" disabled selected>Escolha o ambiente…</option>';
        if (Array.isArray(ambientes)) {
            ambientes.forEach(function (a) {
                selA.innerHTML +=
                    '<option value="' + a.id_ambiente + '">' + SGM.escapeHtml(a.nome) + '</option>';
            });
        }
        selA.disabled = false;
    }

    document.getElementById('selectBloco').addEventListener('change', function () {
        carregarAmbientes(this.value);
    });

    document.getElementById('formChamado').addEventListener('submit', async function (e) {
        e.preventDefault();
        var fd = new FormData();
        fd.append('id_ambiente', document.getElementById('selectAmbiente').value);
        fd.append('id_tipo', document.getElementById('selectTipo').value);
        fd.append('descricao', document.getElementById('descricao').value);
        var foto = document.getElementById('foto').files[0];
        if (foto) fd.append('foto', foto);
        try {
            var response = await fetch('api/salvar_chamado.php', { method: 'POST', body: fd });
            var result = await response.json();
            if (result.success) {
                SGM.toast(result.message || 'Solicitação registrada.');
                window.location.href = 'solicitante_dashboard.php';
            } else {
                SGM.toast(result.message || 'Erro ao enviar.', 'error');
            }
        } catch (err) {
            SGM.toast('Falha na conexão.', 'error');
        }
    });

    document.addEventListener('DOMContentLoaded', iniciar);
})();
