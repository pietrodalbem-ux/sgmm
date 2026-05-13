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

<div class="sgm-page-heading mb-3">
    <h1>Chamados</h1>
    <p>Gerencie todas as solicitações da unidade. Os indicadores abaixo refletem o mesmo painel da visão geral.</p>
</div>

<div class="sgm-stat-grid mb-4">
    <div class="sgm-stat-card">
        <div class="label">Abertos / triagem</div>
        <div class="value" id="gc-stat-ab">—</div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Em atendimento</div>
        <div class="value" id="gc-stat-em">—</div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Concluídos hoje</div>
        <div class="value" id="gc-stat-ok">—</div>
    </div>
    <div class="sgm-stat-card">
        <div class="label">Críticos em aberto</div>
        <div class="value" id="gc-stat-cr">—</div>
    </div>
</div>

<div class="sgm-card mb-3">
    <div class="sgm-card-pad py-3">
        <div class="d-flex flex-wrap align-items-center gap-2 sgm-filter-pills" id="filtros-status">
            <input type="radio" class="btn-check" name="fstatus" id="fs-todos" value="" autocomplete="off" checked>
            <label class="btn sgm-btn-outline" for="fs-todos">Todos</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-aberto" value="aberto" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-aberto">Aberto</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-triagem" value="triagem" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-triagem">Triagem</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-and" value="em_andamento" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-and">Em andamento</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-peca" value="aguardando_peca" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-peca">Aguardando peça</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-conc" value="concluido" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-conc">Concluído</label>
            <input type="radio" class="btn-check" name="fstatus" id="fs-canc" value="cancelado" autocomplete="off">
            <label class="btn sgm-btn-outline" for="fs-canc">Cancelado</label>
        </div>
    </div>
</div>

<div class="sgm-card">
    <div class="sgm-card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Registros</span>
        <input type="search" class="form-control sgm-control" id="gc-busca" placeholder="Buscar…" style="max-width:260px">
    </div>
    <div class="table-responsive sgm-table-wrap border-0 rounded-0">
        <table class="table sgm-table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Solicitante</th>
                    <th>Local</th>
                    <th>Resumo</th>
                    <th>Prioridade</th>
                    <th>Técnico</th>
                    <th>Status</th>
                    <th class="text-end">Ação</th>
                </tr>
            </thead>
            <tbody id="tabelaGeral">
                <tr><td colspan="8" class="text-center text-muted py-4">Carregando…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
