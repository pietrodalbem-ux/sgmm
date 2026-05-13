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
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Abertos / Triagem</div>
            <div class="value" id="gc-stat-ab">—</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Em atendimento</div>
            <div class="value" id="gc-stat-em">—</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Concluídos hoje</div>
            <div class="value" id="gc-stat-ok">—</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sgm-stat-card">
            <div class="label">Críticos em aberto</div>
            <div class="value" id="gc-stat-cr">—</div>
        </div>
    </div>
</div>

<div class="sgm-card mb-4">
    <div class="sgm-card-pad py-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-auto">
                <span class="text-muted small fw-bold text-uppercase tracking-wider d-block mb-2">Filtrar por Status</span>
                <div class="d-flex flex-wrap gap-2" id="filtros-status">
                    <input type="radio" class="btn-check" name="fstatus" id="fs-todos" value="" autocomplete="off" checked>
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-todos">Todos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-aberto" value="aberto" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-aberto">Abertos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-triagem" value="triagem" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-triagem">Triagem</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-and" value="em_andamento" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-and">Em Andamento</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-conc" value="concluido" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-conc">Concluídos</label>
                    
                    <input type="radio" class="btn-check" name="fstatus" id="fs-canc" value="cancelado" autocomplete="off">
                    <label class="btn sgm-btn-outline rounded-pill px-3" for="fs-canc">Cancelados</label>
                </div>
            </div>
            <div class="col-lg ms-lg-auto">
                <span class="text-muted small fw-bold text-uppercase tracking-wider d-block mb-2">Busca Rápida</span>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-4 ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" class="form-control bg-light border-0 rounded-end-4 sgm-control" id="gc-busca" placeholder="ID, solicitante, local ou assunto...">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sgm-card">
    <div class="sgm-card-header">
        <span>Listagem Operacional</span>
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
                    <th class="text-end">Ação</th>
                </tr>
            </thead>
            <tbody id="tabelaGeral">
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                        <span class="text-muted">Carregando registros...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
