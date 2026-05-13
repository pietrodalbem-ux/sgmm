<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Visão geral';
$PAGE_SUBTITLE = 'Indicadores e chamados recentes da unidade.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'dashboard';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-dashboard.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-page-heading mb-4">
    <h1>Visão geral</h1>
    <p>Acompanhe o volume de chamados e acesse rapidamente as demandas mais recentes.</p>
</div>

<div class="sgm-stat-grid mb-4">
    <div class="sgm-stat-card">
        <div class="label">Abertos / triagem</div>
        <div class="value" id="stat-aguardando">—</div>
        <div class="sgm-progress"><span id="bar-aguardando" style="width:0%"></span></div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Em atendimento</div>
        <div class="value" id="stat-em-atendimento">—</div>
        <div class="sgm-progress"><span id="bar-em-atendimento" style="width:0%"></span></div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Concluídos hoje</div>
        <div class="value" id="stat-concluidos">—</div>
        <div class="sgm-progress"><span id="bar-concluidos" style="width:0%"></span></div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Críticos em aberto</div>
        <div class="value" id="stat-criticos">—</div>
        <div class="sgm-progress"><span id="bar-criticos" style="width:0%"></span></div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <a href="gestor_chamados.php" class="text-decoration-none d-block h-100">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <i class="bi bi-ticket-perforated fs-2 text-primary"></i>
                <div>
                    <div class="fw-semibold text-dark">Chamados</div>
                    <small class="text-muted">Lista completa e filtros</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_blocos.php" class="text-decoration-none d-block h-100">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <i class="bi bi-box-seam fs-2 text-primary"></i>
                <div>
                    <div class="fw-semibold text-dark">Blocos</div>
                    <small class="text-muted">Cadastro estrutural</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_ambientes.php" class="text-decoration-none d-block h-100">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <i class="bi bi-building fs-2 text-primary"></i>
                <div>
                    <div class="fw-semibold text-dark">Ambientes</div>
                    <small class="text-muted">Salas e laboratórios</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 col-lg-3">
        <a href="gestor_tipos_servico.php" class="text-decoration-none d-block h-100">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <i class="bi bi-tags fs-2 text-primary"></i>
                <div>
                    <div class="fw-semibold text-dark">Tipos de serviço</div>
                    <small class="text-muted">Categorias de manutenção</small>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="sgm-card">
    <div class="sgm-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Chamados recentes</span>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <input type="search" class="form-control sgm-control" id="busca-chamados" placeholder="Buscar na tabela…" style="max-width:220px">
            <a href="gestor_chamados.php" class="btn btn-sm sgm-btn-primary">Ver todos</a>
        </div>
    </div>
    <div class="table-responsive sgm-table-wrap border-0 rounded-0">
        <table class="table sgm-table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitante</th>
                    <th>Local</th>
                    <th>Assunto</th>
                    <th>Prioridade</th>
                    <th>Status</th>
                    <th class="text-end">Ação</th>
                </tr>
            </thead>
            <tbody id="lista-chamados-corpo">
                <tr><td colspan="7" class="text-center text-muted py-4">Carregando…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
