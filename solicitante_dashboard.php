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

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <!-- Banner de Boas-vindas -->
        <div class="sgm-card mb-4 overflow-hidden border-0 bg-primary position-relative" style="min-height: 160px;">
            <div class="sgm-card-pad position-relative z-1 d-flex flex-column justify-content-center h-100 text-white">
                <h2 class="fw-800 mb-2">Olá, <?php echo explode(' ', htmlspecialchars($_SESSION['user_nome'] ?? 'Solicitante', ENT_QUOTES, 'UTF-8'))[0]; ?>!</h2>
                <p class="opacity-75 mb-4">Como podemos ajudar com a manutenção hoje?</p>
                <div>
                    <a href="solicitante_abrir_chamado.php" class="btn btn-light rounded-pill px-4 fw-bold text-primary">
                        <i class="bi bi-plus-lg me-2"></i>Nova Solicitação
                    </a>
                </div>
            </div>
            <!-- Decoração visual -->
            <i class="bi bi-ticket-perforated position-absolute end-0 bottom-0 m-n3 opacity-10" style="font-size: 10rem;"></i>
        </div>

        <!-- Tabela de Chamados -->
        <div class="sgm-card">
            <div class="sgm-card-header">
                <span>Minhas Solicitações</span>
            </div>
            <div class="table-responsive">
                <table class="table sgm-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px">ID</th>
                            <th>Assunto</th>
                            <th>Localização</th>
                            <th>Status</th>
                            <th class="text-end">Data</th>
                        </tr>
                    </thead>
                    <tbody id="tabelaChamados">
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                Buscando seus chamados...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Card de Resumo -->
        <div class="sgm-card mb-4">
            <div class="sgm-card-header">
                <span>Resumo da Conta</span>
            </div>
            <div class="sgm-card-pad">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle p-2 rounded-3">
                            <i class="bi bi-collection text-primary"></i>
                        </div>
                        <span class="text-muted fw-medium">Total Geral</span>
                    </div>
                    <span class="h5 mb-0 fw-bold text-primary" id="sol-res-total">—</span>
                </div>
                
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning-subtle p-2 rounded-3">
                            <i class="bi bi-clock-history text-warning"></i>
                        </div>
                        <span class="text-muted fw-medium">Em Aberto</span>
                    </div>
                    <span class="h5 mb-0 fw-bold text-warning" id="sol-res-and">—</span>
                </div>
                
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success-subtle p-2 rounded-3">
                            <i class="bi bi-check-all text-success"></i>
                        </div>
                        <span class="text-muted fw-medium">Finalizados</span>
                    </div>
                    <span class="h5 mb-0 fw-bold text-success" id="sol-res-fim">—</span>
                </div>
            </div>
        </div>

        <!-- Card de Dica -->
        <div class="sgm-card bg-info-subtle border-info-subtle">
            <div class="sgm-card-pad d-flex gap-3">
                <i class="bi bi-lightbulb fs-3 text-info"></i>
                <div>
                    <h4 class="h6 fw-bold text-info-emphasis">Dica Profissional</h4>
                    <p class="small text-info-emphasis mb-0 opacity-75">
                        Anexe fotos nítidas e detalhadas. Isso ajuda nossa equipe técnica a identificar as ferramentas necessárias antes mesmo de chegar ao local.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
