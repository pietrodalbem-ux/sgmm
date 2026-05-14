<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Visão Geral do Sistema';
$PAGE_SUBTITLE = 'Indicadores de desempenho e métricas de manutenção em tempo real.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'dashboard';
$SGM_EXTRA_SCRIPTS = [
    'https://cdn.jsdelivr.net/npm/chart.js',
    'assets/js/gestor-dashboard.js'
];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<!-- Grid de Indicadores (Stats) -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        <div class="sgm-stat-card">
            <div class="sgm-stat-icon bg-info-subtle text-info">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="sgm-stat-info">
                <span class="label">Abertos / Triagem</span>
                <div class="value" id="stat-aguardando">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="sgm-stat-card">
            <div class="sgm-stat-icon bg-warning-subtle text-warning">
                <i class="bi bi-play-circle"></i>
            </div>
            <div class="sgm-stat-info">
                <span class="label">Em atendimento</span>
                <div class="value" id="stat-em-atendimento">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="sgm-stat-card">
            <div class="sgm-stat-icon bg-success-subtle text-success">
                <i class="bi bi-check2-all"></i>
            </div>
            <div class="sgm-stat-info">
                <span class="label">Concluídos hoje</span>
                <div class="value" id="stat-concluidos">—</div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="sgm-stat-card">
            <div class="sgm-stat-icon bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="sgm-stat-info">
                <span class="label">Críticos em aberto</span>
                <div class="value" id="stat-criticos">—</div>
            </div>
        </div>
    </div>
</div>

<!-- Seção de Gráficos Reais -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span><i class="bi bi-graph-up me-2 text-primary"></i>Evolução Mensal de Chamados</span>
            </div>
            <div class="sgm-card-pad">
                <div class="chart-container">
                    <canvas id="chartEvolucao"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span><i class="bi bi-pie-chart me-2 text-primary"></i>Status dos Chamados</span>
            </div>
            <div class="sgm-card-pad">
                <div class="chart-container">
                    <canvas id="chartStatus"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span><i class="bi bi-person-badge me-2 text-primary"></i>Chamados por Técnico (Top 5)</span>
            </div>
            <div class="sgm-card-pad">
                <div class="chart-container">
                    <canvas id="chartTecnicos"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span><i class="bi bi-building-gear me-2 text-primary"></i>Chamados por Bloco</span>
            </div>
            <div class="sgm-card-pad">
                <div class="chart-container">
                    <canvas id="chartBlocos"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Links Rápidos -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-lg-3">
        <a href="gestor_chamados.php" class="text-decoration-none">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <div class="icon-box bg-primary-subtle p-3 rounded-4">
                    <i class="bi bi-ticket-perforated fs-3 text-primary"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Chamados</div>
                    <small class="text-muted">Gestão completa</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_usuarios.php" class="text-decoration-none">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <div class="icon-box bg-danger-subtle p-3 rounded-4">
                    <i class="bi bi-people fs-3 text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Usuários</div>
                    <small class="text-muted">Contas e acessos</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_ambientes.php" class="text-decoration-none">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <div class="icon-box bg-success-subtle p-3 rounded-4">
                    <i class="bi bi-building fs-3 text-success"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Ambientes</div>
                    <small class="text-muted">Salas e laboratórios</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_tipos_servico.php" class="text-decoration-none">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <div class="icon-box bg-warning-subtle p-3 rounded-4">
                    <i class="bi bi-tags fs-3 text-warning"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Serviços</div>
                    <small class="text-muted">Categorias</small>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Chamados Recentes -->
<div class="sgm-card animate__animated animate__fadeInUp">
    <div class="sgm-card-header">
        <span>Chamados Recentes</span>
        <div class="d-flex gap-3 align-items-center">
            <div class="input-group input-group-sm d-none d-md-flex" style="max-width: 250px;">
                <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="search" class="form-control bg-light border-0 rounded-end-pill" id="busca-chamados" placeholder="Filtrar chamados...">
            </div>
            <a href="gestor_chamados.php" class="btn sgm-btn-primary btn-sm rounded-pill px-4">Ver todos</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitante</th>
                    <th>Local</th>
                    <th>Assunto</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-chamados-corpo">
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        <span class="text-muted fw-medium">Sincronizando com o banco de dados...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
