<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Minhas tarefas';
$PAGE_SUBTITLE = 'Chamados atribuídos a você em andamento.';
$LAYOUT = 'tecnico';
$NAV_ACTIVE = 'tarefas';
$SGM_EXTRA_SCRIPTS = ['assets/js/tecnico-fila.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="h5 fw-800 mb-1 text-primary">Fila de Atendimento</h2>
                <p class="text-muted small mb-0">Gerencie e priorize suas tarefas designadas.</p>
            </div>
            <div class="bg-primary-subtle px-3 py-1 rounded-pill">
                <span class="text-primary small fw-bold" id="task-count">— Tarefas</span>
            </div>
        </div>

        <div id="fila-tarefas" class="d-flex flex-column gap-4">
            <div class="sgm-card">
                <div class="sgm-card-pad py-5 text-center">
                    <div class="spinner-border text-primary spinner-border-sm me-2" role="status"></div>
                    <span class="text-muted fw-medium">Sincronizando tarefas do banco de dados...</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="sgm-card mb-4">
            <div class="sgm-card-header">
                <span>Indicadores de Produtividade</span>
            </div>
            <div class="sgm-card-pad">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Concluídos Hoje</span>
                        <span class="fw-800 text-success" id="stat-done-today">—</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 0%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small fw-bold text-uppercase">Urgência Crítica</span>
                        <span class="fw-800 text-danger" id="stat-critical-tasks">—</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-danger" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="sgm-card bg-primary text-white border-0 overflow-hidden position-relative">
            <div class="sgm-card-pad position-relative z-1">
                <h3 class="h6 fw-800 text-uppercase mb-3 tracking-widest opacity-75">Suporte</h3>
                <p class="small mb-4 opacity-75">Problemas com o sistema ou dúvidas sobre um chamado? Entre em contato com a gestão.</p>
                <a href="#" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-primary">Falar com Gestor</a>
            </div>
            <i class="bi bi-chat-dots position-absolute end-0 bottom-0 m-n2 opacity-10" style="font-size: 5rem;"></i>
        </div>
    </div>
</div>

<!-- Modal Concluir Chamado -->
<div class="modal fade" id="modalConcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-check2-circle me-2"></i>Concluir Chamado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1 fw-semibold" id="concluirTitulo"></p>
                <p class="text-muted small mb-3" id="concluirDesc"></p>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Observação de conclusão</label>
                    <textarea id="concluirFeedback" class="form-control sgm-control" rows="3" placeholder="Descreva o que foi feito..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnConfirmarConcluir">
                    <i class="bi bi-check-lg me-1"></i> Confirmar Conclusão
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
