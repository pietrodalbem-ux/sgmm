<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Chamados';
$PAGE_SUBTITLE = 'Lista operacional com filtros por status.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'chamados';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-chamados-page.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl-3">
        <div class="sgm-stat-card">
            <div class="sgm-stat-icon bg-info-subtle text-info">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="sgm-stat-info">
                <span class="label">Abertos / Triagem</span>
                <div class="value" id="gc-stat-ab">—</div>
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
                <div class="value" id="gc-stat-em">—</div>
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
                <div class="value" id="gc-stat-ok">—</div>
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
                <div class="value" id="gc-stat-cr">—</div>
            </div>
        </div>
    </div>
</div>

<div class="sgm-card mb-4">
    <div class="sgm-card-pad py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-auto">
                <span class="text-muted small fw-bold text-uppercase tracking-wider d-block mb-3">Filtrar por Status</span>
                <div class="d-flex flex-wrap gap-2" id="filtros-status">
                    <input type="radio" class="btn-check" name="fstatus" id="fs-todos" value="" autocomplete="off" checked>
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-todos">Todos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-aberto" value="aberto" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-aberto">Abertos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-triagem" value="triagem" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-triagem">Triagem</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-and" value="em_andamento" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-and">Andamento</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-conc" value="concluido" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-conc">Concluídos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-canc" value="cancelado" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3 py-2" for="fs-canc">Cancelados</label>
                </div>
            </div>
            <div class="col-lg">
                <span class="text-muted small fw-bold text-uppercase tracking-wider d-block mb-3">Busca Rápida</span>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" class="form-control bg-light border-0 rounded-end-pill sgm-control" id="gc-busca" placeholder="ID, solicitante, local ou assunto...">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sgm-card">
    <div class="sgm-card-header">
        <span><i class="bi bi-list-task me-2 text-primary"></i>Listagem Operacional de Chamados</span>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th style="width: 80px">ID</th>
                    <th>Solicitante</th>
                    <th>Localização</th>
                    <th>Assunto / Resumo</th>
                    <th>Prioridade</th>
                    <th>Técnico</th>
                    <th>Status</th>
                    <th class="text-end actions-column">Ações</th>
                </tr>
            </thead>
            <tbody id="tabelaGeral">
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        <span class="text-muted fw-medium">Sincronizando com o banco de dados...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
