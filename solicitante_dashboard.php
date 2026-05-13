<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Painel do solicitante';
$PAGE_SUBTITLE = 'Acompanhe suas solicitações de manutenção.';
$LAYOUT = 'solicitante';
$NAV_ACTIVE = 'dashboard';
$SGM_EXTRA_SCRIPTS = ['assets/js/solicitante-dashboard.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-page-heading mb-4">
    <h1>Olá, <?php echo htmlspecialchars($_SESSION['user_nome'] ?? 'Solicitante', ENT_QUOTES, 'UTF-8'); ?></h1>
    <p>Abra novos chamados ou acompanhe o andamento dos pedidos já registrados.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="sgm-card mb-4">
            <div class="sgm-card-pad d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="h6 fw-bold mb-1">Precisa de suporte?</h2>
                    <p class="small text-muted mb-0">Descreva o problema com local e, se possível, fotos.</p>
                </div>
                <a href="solicitante_abrir_chamado.php" class="btn sgm-btn-primary"><i class="bi bi-plus-lg me-1"></i> Nova solicitação</a>
            </div>
        </div>

        <div class="sgm-card">
            <div class="sgm-card-header">Minhas solicitações</div>
            <div class="table-responsive sgm-table-wrap border-0 rounded-0">
                <table class="table sgm-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Resumo</th>
                            <th>Local</th>
                            <th>Status</th>
                            <th class="text-end">Abertura</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaChamados">
                        <tr><td colspan="5" class="text-center text-muted py-4">Carregando…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="sgm-card sgm-card-pad mb-3">
            <h3 class="h6 fw-bold mb-3">Resumo</h3>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Total</span>
                <span class="fw-semibold" id="sol-res-total">—</span>
            </div>
            <div class="d-flex justify-content-between mb-2 small">
                <span class="text-muted">Em andamento</span>
                <span class="fw-semibold" id="sol-res-and">—</span>
            </div>
            <div class="d-flex justify-content-between small">
                <span class="text-muted">Concluídos / cancelados</span>
                <span class="fw-semibold" id="sol-res-fim">—</span>
            </div>
        </div>
        <div class="sgm-card sgm-card-pad">
            <h3 class="h6 fw-bold mb-2"><i class="bi bi-info-circle text-primary me-1"></i>Dica</h3>
            <p class="small text-muted mb-0">Anexe imagens nítidas do problema para agilizar o diagnóstico pelo técnico.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
