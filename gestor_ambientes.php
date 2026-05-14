<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Ambientes';
$PAGE_SUBTITLE = 'Gestão de salas, laboratórios e espaços vinculados a cada bloco.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'ambientes';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-ambientes.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-card animate__animated animate__fadeIn">
    <div class="sgm-card-header">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-building me-2 text-primary"></i>Ambientes Cadastrados</span>
            <span class="badge bg-light text-primary rounded-pill" id="amb-contagem">0</span>
        </div>
        <div class="ms-auto d-flex gap-2">
            <div class="input-group input-group-sm" style="max-width: 250px;">
                <span class="input-group-text bg-light border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="search" class="form-control bg-light border-0" id="busca-ambientes" placeholder="Filtrar ambientes...">
            </div>
            <button type="button" class="btn sgm-btn-primary btn-sm rounded-pill px-4" id="btnAbrirModalNovo">
                <i class="bi bi-plus-lg me-2"></i>Adicionar Ambiente
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th>Nome do Ambiente</th>
                    <th>Bloco Vinculado</th>
                    <th class="text-end actions-column">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-ambientes-corpo">
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        <span class="text-muted fw-medium">Sincronizando banco de dados...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Cadastro/Edição -->
<div class="modal fade" id="modalAmbiente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Novo Ambiente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAmbiente">
                <div class="modal-body p-4">
                    <input type="hidden" id="amb_id">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="amb_nome">Nome da Sala/Laboratório</label>
                        <input type="text" class="form-control sgm-control" id="amb_nome" required placeholder="Ex.: Laboratório de Redes, Sala 102">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="amb_bloco">Bloco</label>
                        <select class="form-select sgm-control" id="amb_bloco" required>
                            <option value="" disabled selected>Selecione um bloco...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn sgm-btn-primary rounded-pill px-4">
                        <i class="bi bi-check2-circle me-2"></i>Salvar Ambiente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
