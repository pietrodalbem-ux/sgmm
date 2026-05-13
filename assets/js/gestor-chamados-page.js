(function () {
    'use strict';

    var API_CH = 'api/gestor_chamados.php';
    var API_ST = 'api/dashboard_gestor.php';
    var listaCompleta = [];
    var filtroStatus = '';

    async function loadStats() {
        var r = await SGM.fetchJson(API_ST, 'GET');
        if (!r.res.ok || !r.data || r.data.success === false) return;
        document.getElementById('gc-stat-ab').textContent = r.data.aguardando_triagem ?? 0;
        document.getElementById('gc-stat-em').textContent = r.data.em_atendimento ?? 0;
        document.getElementById('gc-stat-ok').textContent = r.data.concluidos_hoje ?? 0;
        document.getElementById('gc-stat-cr').textContent = r.data.criticos_urgentes ?? 0;
    }

    function renderRows(list) {
        var body = document.getElementById('tabelaGeral');
        if (!list.length) {
            body.innerHTML =
                '<tr><td colspan="8" class="text-center text-muted py-4">Nenhum chamado para este filtro.</td></tr>';
            return;
        }
        body.innerHTML = list
            .map(function (c) {
                var pr = (c.prioridade || '').toString();
                var st = (c.status || '').toString();
                var resumo = (c.titulo || c.descricao_problema || '').toString();
                if (resumo.length > 56) resumo = resumo.substring(0, 56) + '…';
                return (
                    '<tr>' +
                    '<td><span class="badge text-bg-light border">' +
                    SGM.escapeHtml(String(c.id_chamado)) +
                    '</span></td>' +
                    '<td class="fw-medium">' +
                    SGM.escapeHtml(c.solicitante_nome || '') +
                    '</td>' +
                    '<td><span class="small text-muted d-block">' +
                    SGM.escapeHtml(c.bloco_nome || '') +
                    '</span><span class="fw-medium">' +
                    SGM.escapeHtml(c.ambiente_nome || '') +
                    '</span></td>' +
                    '<td class="small">' +
                    SGM.escapeHtml(resumo) +
                    '</td>' +
                    '<td><span class="' +
                    (SGM.priorClass[pr] || 'text-secondary') +
                    ' fw-semibold small">' +
                    SGM.escapeHtml(pr.toUpperCase() || '-') +
                    '</span></td>' +
                    '<td class="small">' +
                    (c.tecnico_nome
                        ? SGM.escapeHtml(c.tecnico_nome)
                        : '<span class="text-muted">—</span>') +
                    '</td>' +
                    '<td>' +
                    SGM.badgeStatus(st) +
                    '</td>' +
                    '<td class="text-end"><a class="btn btn-sm sgm-btn-primary" href="gestor_detalhes.php?id=' +
                    encodeURIComponent(c.id_chamado) +
                    '">Abrir</a></td>' +
                    '</tr>'
                );
            })
            .join('');
    }

    function aplicarBusca() {
        var q = document.getElementById('gc-busca').value.trim().toLowerCase();
        var base = listaCompleta;
        if (filtroStatus) {
            base = base.filter(function (c) {
                return (c.status || '') === filtroStatus;
            });
        }
        if (!q) {
            renderRows(base);
            return;
        }
        renderRows(
            base.filter(function (c) {
                return [
                    c.id_chamado,
                    c.solicitante_nome,
                    c.bloco_nome,
                    c.ambiente_nome,
                    c.status,
                    c.prioridade,
                    c.titulo,
                    c.descricao_problema,
                    c.tecnico_nome,
                ]
                    .join(' ')
                    .toLowerCase()
                    .indexOf(q) !== -1;
            })
        );
    }

    async function carregarChamados() {
        var r = await SGM.fetchJson(API_CH + '?status=', 'GET');
        var body = document.getElementById('tabelaGeral');
        if (!r.res.ok || !Array.isArray(r.data)) {
            body.innerHTML =
                '<tr><td colspan="8" class="text-center text-danger py-4">Não foi possível carregar os chamados.</td></tr>';
            SGM.toast('Erro ao carregar chamados.', 'error');
            return;
        }
        listaCompleta = r.data;
        aplicarBusca();
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        carregarChamados();
        document.getElementById('filtros-status').addEventListener('change', function (e) {
            if (e.target.name !== 'fstatus') return;
            filtroStatus = e.target.value || '';
            aplicarBusca();
        });
        document.getElementById('gc-busca').addEventListener('input', aplicarBusca);
    });
})();
