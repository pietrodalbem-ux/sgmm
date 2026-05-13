(function () {
    'use strict';

    var API_STATS = 'api/dashboard_gestor.php';
    var API_CH = 'api/gestor_chamados.php';

    var chamadosLista = [];

    async function loadStats() {
        var r = await SGM.fetchJson(API_STATS, 'GET');
        if (!r.res.ok || !r.data || r.data.success === false) {
            SGM.toast((r.data && r.data.message) || 'Não foi possível carregar indicadores.', 'error');
            return;
        }
        var d = r.data;
        var total = Math.max(1, d.total || 1);
        function pct(n) {
            return Math.min(100, Math.round(((n || 0) / total) * 100));
        }
        document.getElementById('stat-aguardando').textContent = d.aguardando_triagem ?? 0;
        document.getElementById('stat-em-atendimento').textContent = d.em_atendimento ?? 0;
        document.getElementById('stat-concluidos').textContent = d.concluidos_hoje ?? 0;
        document.getElementById('stat-criticos').textContent = d.criticos_urgentes ?? 0;
        document.getElementById('bar-aguardando').style.width = pct(d.aguardando_triagem) + '%';
        document.getElementById('bar-em-atendimento').style.width = pct(d.em_atendimento) + '%';
        document.getElementById('bar-concluidos').style.width = pct(d.concluidos_hoje) + '%';
        document.getElementById('bar-criticos').style.width = pct(d.criticos_urgentes) + '%';
    }

    function renderChamados(list) {
        var tbody = document.getElementById('lista-chamados-corpo');
        if (!list.length) {
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center text-muted py-4">Nenhum chamado encontrado.</td></tr>';
            return;
        }
        tbody.innerHTML = list
            .map(function (c) {
                var pr = (c.prioridade || '').toString();
                var st = (c.status || '').toString();
                var tit = (c.titulo || c.descricao_problema || '').toString();
                if (tit.length > 48) tit = tit.substring(0, 48) + '…';
                return (
                    '<tr>' +
                    '<td><span class="badge text-bg-light border">' +
                    SGM.escapeHtml(String(c.id_chamado)) +
                    '</span></td>' +
                    '<td class="fw-medium">' +
                    SGM.escapeHtml(c.solicitante_nome || '') +
                    '</td>' +
                    '<td><span class="small text-muted">' +
                    SGM.escapeHtml(c.bloco_nome || '') +
                    '</span><br><span class="fw-medium">' +
                    SGM.escapeHtml(c.ambiente_nome || '') +
                    '</span></td>' +
                    '<td class="small">' +
                    SGM.escapeHtml(tit) +
                    '</td>' +
                    '<td><span class="' +
                    (SGM.priorClass[pr] || 'text-secondary') +
                    ' fw-semibold small">' +
                    SGM.escapeHtml(pr.toUpperCase() || '-') +
                    '</span></td>' +
                    '<td>' +
                    SGM.badgeStatus(st) +
                    '</td>' +
                    '<td class="text-end"><a class="btn btn-sm sgm-btn-outline" href="gestor_detalhes.php?id=' +
                    encodeURIComponent(c.id_chamado) +
                    '">Detalhes</a></td>' +
                    '</tr>'
                );
            })
            .join('');
    }

    async function loadChamados() {
        var r = await SGM.fetchJson(API_CH + '?status=', 'GET');
        var tbody = document.getElementById('lista-chamados-corpo');
        if (!r.res.ok || !Array.isArray(r.data)) {
            tbody.innerHTML =
                '<tr><td colspan="7" class="text-center text-danger py-4">Erro ao carregar chamados.</td></tr>';
            return;
        }
        chamadosLista = r.data.slice(0, 15);
        renderChamados(chamadosLista);
    }

    function setupBusca() {
        var inp = document.getElementById('busca-chamados');
        if (!inp) return;
        inp.addEventListener('input', function () {
            var q = inp.value.trim().toLowerCase();
            if (!q) {
                renderChamados(chamadosLista);
                return;
            }
            var filtered = chamadosLista.filter(function (c) {
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
            });
            renderChamados(filtered);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        loadChamados();
        setupBusca();
    });
})();
