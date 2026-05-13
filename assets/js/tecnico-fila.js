(function () {
    'use strict';

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

    async function carregar() {
        var wrap = document.getElementById('fila-tarefas');
        var r = await SGM.fetchJson('api/tecnico_chamados.php', 'GET');
        
        if (!r.res.ok || !r.data || !r.data.success) {
            wrap.innerHTML = '<div class="alert alert-danger">Não foi possível carregar a fila.</div>';
            return;
        }

        var list = r.data.data || [];
        var stats = r.data.stats || { total_ativa: 0, total_critica: 0, concluidos_hoje: 0 };

        // Atualizar indicadores
        if (document.getElementById('task-count')) {
            document.getElementById('task-count').textContent = stats.total_ativa + (stats.total_ativa === 1 ? ' Tarefa' : ' Tarefas');
        }
        if (document.getElementById('stat-done-today')) {
            document.getElementById('stat-done-today').textContent = stats.concluidos_hoje;
        }
        if (document.getElementById('stat-critical-tasks')) {
            document.getElementById('stat-critical-tasks').textContent = stats.total_critica;
        }

        // Barras de progresso (exemplo simples de lógica visual)
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
                if (resumo.length > 120) resumo = resumo.substring(0, 120) + '…';
                return (
                    '<div class="sgm-task-card ' +
                    priorClass(pr) +
                    '">' +
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
                    ' — ' +
                    SGM.escapeHtml(c.ambiente_nome || '') +
                    '</p>' +
                    '<p class="small mb-2">Solicitante: <strong>' +
                    SGM.escapeHtml(c.solicitante_nome || '') +
                    '</strong></p>' +
                    '<a class="btn btn-sm sgm-btn-primary" href="gestor_detalhes.php?id=' +
                    encodeURIComponent(c.id_chamado) +
                    '">Abrir chamado</a>' +
                    '</div></div></div>'
                );
            })
            .join('');
    }

    document.addEventListener('DOMContentLoaded', carregar);
})();
