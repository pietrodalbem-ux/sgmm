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

<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Abertos / Triagem</div>
            <div class="value" id="stat-aguardando">—</div>
            <div class="sgm-progress"><span id="bar-aguardando" style="width:0%"></span></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Em atendimento</div>
            <div class="value" id="stat-em-atendimento">—</div>
            <div class="sgm-progress"><span id="bar-em-atendimento" style="width:0%"></span></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Concluídos hoje</div>
            <div class="value" id="stat-concluidos">—</div>
            <div class="sgm-progress"><span id="bar-concluidos" style="width:0%"></span></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Críticos em aberto</div>
            <div class="value" id="stat-criticos">—</div>
            <div class="sgm-progress"><span id="bar-criticos" style="width:0%"></span></div>
        </div>
    </div>
</div>

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
        <a href="gestor_blocos.php" class="text-decoration-none">
            <div class="sgm-card sgm-card-pad h-100 d-flex align-items-center gap-3">
                <div class="icon-box bg-info-subtle p-3 rounded-4">
                    <i class="bi bi-box-seam fs-3 text-info"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark">Blocos</div>
                    <small class="text-muted">Infraestrutura</small>
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

<div class="sgm-card">
    <div class="sgm-card-header">
        <span>Chamados Recentes</span>
        <div class="d-flex gap-3 align-items-center">
            <div class="input-group input-group-sm" style="max-width: 250px;">
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
