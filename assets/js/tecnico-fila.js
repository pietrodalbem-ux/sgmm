(function () {
    'use strict';

    var concluirId = null;

    function priorClass(p) {
        var m = { critica: 'p-critica', alta: 'p-alta', media: 'p-media', baixa: 'p-baixa' };
        return m[p] || 'p-media';
    }

    function fmtData(s) {
        if (!s) return '';
        try {
            var d = new Date(s);
            return d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
        } catch (e) {
            return '';
        }
    }

    function abrirModal(id, titulo) {
        concluirId = id;
        document.getElementById('concluirTitulo').textContent = '#' + id + ' — ' + titulo;
        document.getElementById('concluirDesc').textContent = 'Confirme a conclusão deste chamado. Opcionalmente, adicione uma observação.';
        document.getElementById('concluirFeedback').value = '';
        var modal = new bootstrap.Modal(document.getElementById('modalConcluir'));
        modal.show();
    }

    async function carregar() {
        var wrap = document.getElementById('fila-tarefas');
        var r = await SGM.fetchJson('api/tecnico_chamados.php', 'GET');
        
        if (!r.res.ok || !r.data || !r.data.success) {
            wrap.innerHTML = '<div class="alert alert-danger">Não foi possível carregar a fila.</div>';
            return;
        }

        var list = r.data.data || [];
        var stats = r.data.stats || { total_ativa: 0, total_critica: 0, concluidos_hoje: 0 };

        if (document.getElementById('task-count')) {
            document.getElementById('task-count').textContent = stats.total_ativa + (stats.total_ativa === 1 ? ' Tarefa' : ' Tarefas');
        }
        if (document.getElementById('stat-done-today')) {
            document.getElementById('stat-done-today').textContent = stats.concluidos_hoje;
        }
        if (document.getElementById('stat-critical-tasks')) {
            document.getElementById('stat-critical-tasks').textContent = stats.total_critica;
        }

        var bars = document.querySelectorAll('.progress-bar');
        if (bars.length >= 2) {
            bars[0].style.width = Math.min(100, stats.concluidos_hoje * 10) + '%';
            bars[1].style.width = Math.min(100, stats.total_critica * 20) + '%';
        }

        if (!list.length) {
            wrap.innerHTML = '<div class="sgm-card sgm-card-pad text-center text-muted">Nenhuma tarefa ativa no momento.</div>';
            return;
        }
        wrap.innerHTML = list
            .map(function (c) {
                var pr = (c.prioridade || 'media').toString();
                var resumo = (c.titulo || c.descricao_problema || '').toString();
                if (resumo.length > 120) resumo = resumo.substring(0, 120) + '\u2026';
                var idEnc = encodeURIComponent(c.id_chamado);
                return (
                    '<div class="sgm-task-card ' +
                    priorClass(pr) +
                    '" data-id="' + idEnc + '">' +
                    '<div class="row align-items-center g-3">' +
                    '<div class="col-md-8">' +
                    '<div class="d-flex flex-wrap align-items-center gap-2 mb-2">' +
                    '<span class="badge text-bg-light border">#' +
                    SGM.escapeHtml(String(c.id_chamado)) +
                    '</span>' +
                    '<span class="badge sgm-badge text-bg-secondary text-uppercase">' +
                    SGM.escapeHtml(pr) +
                    '</span>' +
                    '<span class="small text-muted">' +
                    SGM.escapeHtml(fmtData(c.data_abertura)) +
                    '</span></div>' +
                    '<h2 class="h6 fw-bold mb-1">' +
                    SGM.escapeHtml(c.titulo || 'Chamado') +
                    '</h2>' +
                    '<p class="small text-muted mb-0">' +
                    SGM.escapeHtml(resumo) +
                    '</p></div>' +
                    '<div class="col-md-4 text-md-end">' +
                    '<p class="small text-muted mb-2"><i class="bi bi-geo-alt me-1"></i>' +
                    SGM.escapeHtml(c.bloco_nome || '') +
                    ' \u2014 ' +
                    SGM.escapeHtml(c.ambiente_nome || '') +
                    '</p>' +
                    '<p class="small mb-2">Solicitante: <strong>' +
                    SGM.escapeHtml(c.solicitante_nome || '') +
                    '</strong></p>' +
                    '<div class="d-flex gap-2 justify-content-md-end">' +
                    '<a class="btn btn-sm sgm-btn-primary" href="gestor_detalhes.php?id=' +
                    idEnc +
                    '">Abrir</a>' +
                    '<button class="btn btn-sm btn-success rounded-pill fw-semibold btn-concluir" data-id="' +
                    idEnc +
                    '" data-titulo="' +
                    SGM.escapeHtml(c.titulo || 'Chamado') +
                    '"><i class="bi bi-check2-circle me-1"></i>Concluir</button>' +
                    '</div>' +
                    '</div></div></div>'
                );
            })
            .join('');

        document.querySelectorAll('.btn-concluir').forEach(function (btn) {
            btn.addEventListener('click', function () {
                abrirModal(
                    parseInt(this.getAttribute('data-id'), 10),
                    this.getAttribute('data-titulo')
                );
            });
        });
    }

    document.getElementById('btnConfirmarConcluir').addEventListener('click', async function () {
        if (!concluirId) return;
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

        try {
            var r = await SGM.fetchJson('api/concluir_chamado.php', 'POST', {
                id_chamado: concluirId,
                feedback: document.getElementById('concluirFeedback').value,
            });
            if (r.data && r.data.success) {
                SGM.toast(r.data.message || 'Conclu\u00eddo com sucesso.');
                bootstrap.Modal.getInstance(document.getElementById('modalConcluir')).hide();
                carregar();
            } else {
                SGM.toast(r.data.message || 'Falha ao concluir.', 'error');
            }
        } catch (err) {
            SGM.toast('Erro: ' + err.message, 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Confirmar Conclus\u00e3o';
        }
    });

    document.addEventListener('DOMContentLoaded', carregar);
})();
