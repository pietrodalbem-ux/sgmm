(function () {
    'use strict';

    var API_DASHBOARD = 'api/dashboard_gestor.php';
    var API_CHAMADOS = 'api/gestor_chamados.php';
    var charts = {};

    async function carregarDashboard() {
        var r = await SGM.fetchJson(API_DASHBOARD);
        if (r.res.ok && r.data && r.data.success) {
            atualizarIndicadores(r.data.stats);
            renderizarGraficos(r.data.charts);
        } else {
            console.error('Erro ao carregar dashboard', r.data);
            SGM.toast('Erro ao carregar indicadores.', 'error');
        }
    }

    function atualizarIndicadores(s) {
        document.getElementById('stat-aguardando').textContent = s.aguardando;
        document.getElementById('stat-em-atendimento').textContent = s.atendimento;
        document.getElementById('stat-concluidos').textContent = s.concluidos_hoje;
        document.getElementById('stat-criticos').textContent = s.criticos;
    }

    function renderizarGraficos(c) {
        // 1. Gráfico de Evolução (Linha)
        renderLinha('chartEvolucao', c.evolucao);

        // 2. Gráfico de Status (Doughnut)
        renderDoughnut('chartStatus', c.status);

        // 3. Gráfico de Técnicos (Barra Horizontal)
        renderBarraH('chartTecnicos', c.tecnicos);

        // 4. Gráfico de Blocos (Polar Area)
        renderPolar('chartBlocos', c.blocos);
    }

    function renderLinha(id, data) {
        if (charts[id]) charts[id].destroy();
        var ctx = document.getElementById(id).getContext('2d');
        charts[id] = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(i => i.mes),
                datasets: [{
                    label: 'Chamados Abertos',
                    data: data.map(i => i.count),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function renderDoughnut(id, data) {
        if (charts[id]) charts[id].destroy();
        var ctx = document.getElementById(id).getContext('2d');
        
        var labels = Object.keys(data).map(k => SGM.labelStatus(k));
        var values = Object.values(data);

        charts[id] = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: ['#0ea5e9', '#64748b', '#f59e0b', '#f59e0b', '#10b981', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                },
                cutout: '70%'
            }
        });
    }

    function renderBarraH(id, data) {
        if (charts[id]) charts[id].destroy();
        var ctx = document.getElementById(id).getContext('2d');
        charts[id] = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(i => i.nome),
                datasets: [{
                    label: 'Chamados',
                    data: data.map(i => i.count),
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    function renderPolar(id, data) {
        if (charts[id]) charts[id].destroy();
        var ctx = document.getElementById(id).getContext('2d');
        charts[id] = new Chart(ctx, {
            type: 'polarArea',
            data: {
                labels: data.map(i => i.nome),
                datasets: [{
                    data: data.map(i => i.count),
                    backgroundColor: [
                        'rgba(37, 99, 235, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(14, 165, 233, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true } }
                }
            }
        });
    }

    async function carregarRecentemente() {
        var r = await SGM.fetchJson(API_CHAMADOS);
        if (!r.res.ok || !r.data || !r.data.success) return;
        
        var chamados = r.data.data.slice(0, 5); // Apenas os 5 mais recentes
        var html = chamados.map(function (c) {
            return `
                <tr>
                    <td><span class="fw-bold">#${c.id_chamado}</span></td>
                    <td>
                        <div class="small fw-semibold text-dark">${SGM.escapeHtml(c.solicitante_nome)}</div>
                    </td>
                    <td>
                        <div class="small text-muted">${SGM.escapeHtml(c.bloco_nome)}</div>
                        <div class="small fw-medium">${SGM.escapeHtml(c.ambiente_nome)}</div>
                    </td>
                    <td><div class="text-truncate" style="max-width: 200px;">${SGM.escapeHtml(c.titulo)}</div></td>
                    <td><span class="fw-bold small ${SGM.priorClass[c.prioridade]}">${c.prioridade.toUpperCase()}</span></td>
                    <td>${SGM.badgeStatus(c.status)}</td>
                    <td class="text-end">
                        <a href="gestor_detalhes.php?id=${c.id_chamado}" class="btn btn-sm sgm-btn-outline"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
            `;
        }).join('');
        
        document.getElementById('lista-chamados-corpo').innerHTML = html || '<tr><td colspan="7" class="text-center py-4">Nenhum chamado recente.</td></tr>';
    }

    document.addEventListener('DOMContentLoaded', function () {
        carregarDashboard();
        carregarRecentemente();
        
        // Atualização automática a cada 30 segundos
        setInterval(carregarDashboard, 30000);
    });
})();
