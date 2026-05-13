(function () {
    'use strict';

    var meta = document.getElementById('sgmChamadoMeta');
    if (!meta || !window.SGM) return;

    var chamadoId = meta.getAttribute('data-id');
    var isGestor = meta.getAttribute('data-gestor') === '1';

    var modalElement = document.getElementById('modalFoto');
    var bootstrapModal = modalElement ? new bootstrap.Modal(modalElement) : null;

    window.verFoto = function (url) {
        var img = document.getElementById('imgModal');
        if (img) img.src = url;
        if (bootstrapModal) bootstrapModal.show();
    };

    async function carregarDados() {
        try {
            var select = document.getElementById('selectTecnico');
            if (isGestor && select) {
                var resTec = await fetch('api/usuarios.php');
                if (!resTec.ok) throw new Error('Erro ao carregar técnicos.');
                var tecnicos = await resTec.json();
                if (Array.isArray(tecnicos)) {
                    tecnicos.forEach(function (t) {
                        var option = document.createElement('option');
                        option.value = t.id_usuario;
                        option.textContent = t.nome;
                        select.appendChild(option);
                    });
                }
            }

            var resChamado = await fetch('api/chamado.php?id=' + encodeURIComponent(chamadoId));
            if (!resChamado.ok) throw new Error('Chamado não encontrado.');
            var c = await resChamado.json();
            if (!c || c.error) throw new Error(c.error || 'Chamado não encontrado');

            document.getElementById('detalhesChamado').innerHTML =
                '<p class="mb-2"><strong>Status:</strong> ' +
                SGM.badgeStatus(c.status || 'aberto') +
                '</p>' +
                '<p class="mb-2"><strong>Título:</strong> ' +
                SGM.escapeHtml(c.titulo || '—') +
                '</p>' +
                '<p class="mb-2"><strong>Descrição:</strong> ' +
                SGM.escapeHtml(c.descricao_problema || 'Sem descrição') +
                '</p>' +
                '<p class="mb-2"><strong>Tipo:</strong> ' +
                SGM.escapeHtml(c.tipo_nome || '') +
                '</p>' +
                '<p class="mb-2"><strong>Local:</strong> ' +
                SGM.escapeHtml(c.bloco_nome || '') +
                ' — ' +
                SGM.escapeHtml(c.ambiente_nome || '') +
                '</p>' +
                '<p class="mb-2"><strong>Solicitante:</strong> ' +
                SGM.escapeHtml(c.solicitante_nome || '') +
                '</p>' +
                '<p class="mb-0"><strong>Abertura:</strong> ' +
                (c.data_abertura ? new Date(c.data_abertura).toLocaleString('pt-BR') : 'N/A') +
                '</p>' +
                '<div id="fotosContainer" class="mt-3"></div>';

            if (isGestor && select) {
                if (c.id_tecnico) select.value = c.id_tecnico;
                var pr = document.getElementById('prioridade');
                if (pr && c.prioridade) pr.value = c.prioridade;
                var dt = document.getElementById('data_prevista');
                if (dt && c.data_previsao_conclusao) dt.value = c.data_previsao_conclusao;
            }

            var resAnexos = await fetch('api/anexos.php?id_chamado=' + encodeURIComponent(chamadoId));
            if (resAnexos.ok) {
                var anexos = await resAnexos.json();
                if (Array.isArray(anexos) && anexos.length > 0) {
                    var html =
                        '<hr class="my-3"><h6 class="fw-semibold mb-2">Evidências</h6><div class="row g-2">';
                    anexos.forEach(function (arq) {
                        var pathJs = JSON.stringify(arq.caminho_arquivo);
                        var pathUrl = encodeURI(arq.caminho_arquivo);
                        html +=
                            '<div class="col-6 col-md-3 text-center">' +
                            '<img src="' +
                            pathUrl +
                            '" class="img-fluid rounded border" style="cursor:pointer;height:100px;width:100%;object-fit:cover;" onclick="verFoto(' +
                            pathJs +
                            ')" alt="">' +
                            '<small class="text-muted d-block mt-1" style="font-size:0.7rem">' +
                            (arq.tipo_anexo === 'abertura' ? 'Abertura' : 'Conclusão') +
                            '</small></div>';
                    });
                    document.getElementById('fotosContainer').innerHTML = html + '</div>';
                }
            }
        } catch (erro) {
            console.error(erro);
            document.getElementById('detalhesChamado').innerHTML =
                '<div class="alert alert-danger mb-0">Erro ao carregar: ' +
                SGM.escapeHtml(erro.message) +
                '</div>';
        }
    }

    var formAt = document.getElementById('formAtribuir');
    if (formAt) {
        formAt.addEventListener('submit', async function (e) {
            e.preventDefault();
            var dados = {
                id_chamado: chamadoId,
                id_tecnico: document.getElementById('selectTecnico').value,
                prioridade: document.getElementById('prioridade').value,
                data_prevista: document.getElementById('data_prevista').value,
            };
            try {
                var res = await fetch('api/atribuir_chamado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(dados),
                });
                var retorno = await res.json();
                if (retorno.success) {
                    SGM.toast(retorno.message || 'Salvo com sucesso.');
                    window.location.href = isGestor ? 'gestor_chamados.php' : 'tecnico_minhas_tarefas.php';
                } else {
                    SGM.toast(retorno.message || 'Falha ao salvar.', 'error');
                }
            } catch (err) {
                SGM.toast('Erro de conexão: ' + err.message, 'error');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', carregarDados);
    } else {
        carregarDados();
    }
})();
