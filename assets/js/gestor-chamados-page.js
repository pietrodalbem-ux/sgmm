(function () {
    'use strict';

    var API_CH = 'api/gestor_chamados.php';
    var API_ST = 'api/dashboard_gestor.php';
    var searchTimeout = null;

    async function loadStats() {
        var r = await SGM.fetchJson(API_ST, 'GET');
        if (!r.res.ok || !r.data || r.data.success === false) return;
        
        var s = r.data.stats;
        document.getElementById('gc-stat-ab').textContent = s.aguardando ?? 0;
        document.getElementById('gc-stat-em').textContent = s.atendimento ?? 0;
        document.getElementById('gc-stat-ok').textContent = s.concluidos_hoje ?? 0;
        document.getElementById('gc-stat-cr').textContent = s.criticos ?? 0;
    }

    function renderRows(list) {
        var body = document.getElementById('tabelaGeral');
        if (!list.length) {
            body.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-5">Nenhum registro encontrado no banco de dados.</td></tr>';
            return;
        }
        body.innerHTML = list.map(function (c) {
            var pr = (c.prioridade || '').toString();
            var st = (c.status || '').toString();
            var resumo = (c.titulo || c.descricao_problema || '').toString();
            if (resumo.length > 56) resumo = resumo.substring(0, 56) + '…';
            
            return `
                <tr>
                    <td><span class="badge text-bg-light border px-2">#${SGM.escapeHtml(String(c.id_chamado))}</span></td>
                    <td class="fw-bold text-dark">${SGM.escapeHtml(c.solicitante_nome || '')}</td>
                    <td>
                        <div class="small text-muted">${SGM.escapeHtml(c.bloco_nome || '')}</div>
                        <div class="fw-medium small">${SGM.escapeHtml(c.ambiente_nome || '')}</div>
                    </td>
                    <td class="small">${SGM.escapeHtml(resumo)}</td>
                    <td><span class="${SGM.priorClass[pr] || 'text-secondary'} fw-bold small">${pr.toUpperCase()}</span></td>
                    <td class="small">${c.tecnico_nome ? SGM.escapeHtml(c.tecnico_nome) : '<span class="text-muted">—</span>'}</td>
                    <td>${SGM.badgeStatus(st)}</td>
                    <td class="text-end actions-column">
                        <div class="btn-actions-group">
                            <a class="btn btn-sm sgm-btn-primary" href="gestor_detalhes.php?id=${encodeURIComponent(c.id_chamado)}" title="Ver detalhes">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function carregarChamados() {
        var q = document.getElementById('gc-busca').value.trim();
        var status = '';
        var radios = document.getElementsByName('fstatus');
        for(var i=0; i<radios.length; i++) {
            if(radios[i].checked) { status = radios[i].value; break; }
        }

        var url = API_CH + '?q=' + encodeURIComponent(q) + '&status=' + encodeURIComponent(status);
        
        // Loader visual
        document.getElementById('tabelaGeral').innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Consultando banco...</td></tr>';

        var r = await SGM.fetchJson(url);
        if (r.res.ok && r.data && r.data.success) {
            renderRows(r.data.data);
        } else {
            document.getElementById('tabelaGeral').innerHTML = '<tr><td colspan="8" class="text-center text-danger py-4">Erro na consulta ao servidor.</td></tr>';
        }
    }

    function debounceBusca() {
        if (searchTimeout) clearTimeout(searchTimeout);
        searchTimeout = setTimeout(carregarChamados, 400);
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadStats();
        carregarChamados();
        
        document.getElementById('filtros-status').addEventListener('change', function (e) {
            if (e.target.name === 'fstatus') carregarChamados();
        });
        
        document.getElementById('gc-busca').addEventListener('input', debounceBusca);
    });
})();
