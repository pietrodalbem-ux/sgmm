<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Blocos';
$PAGE_SUBTITLE = 'Gerencie os setores físicos da unidade para organização dos ambientes.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'blocos';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-blocos.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-card animate__animated animate__fadeIn">
    <div class="sgm-card-header">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-box-seam me-2 text-primary"></i>Blocos Cadastrados</span>
            <span class="badge bg-light text-primary rounded-pill" id="blocos-contagem">0</span>
        </div>
        <div class="ms-auto d-flex gap-2">
            <div class="input-group input-group-sm" style="max-width: 250px;">
                <span class="input-group-text bg-light border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="search" class="form-control bg-light border-0" id="busca-blocos" placeholder="Filtrar blocos...">
            </div>
            <button type="button" class="btn sgm-btn-primary btn-sm rounded-pill px-4" id="btnAbrirModalNovo">
                <i class="bi bi-plus-lg me-2"></i>Adicionar Bloco
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th>Nome do Bloco</th>
                    <th>Descrição / Observações</th>
                    <th class="text-end actions-column">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-blocos-corpo">
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
<div class="modal fade" id="modalBloco" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Novo Bloco</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBloco">
                <div class="modal-body p-4">
                    <input type="hidden" id="bloco_id">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="bloco_nome">Nome do Bloco</label>
                        <input type="text" class="form-control sgm-control" id="bloco_nome" required placeholder="Ex.: Bloco A, Oficinas, etc.">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="bloco_descricao">Descrição</label>
                        <textarea class="form-control sgm-control" id="bloco_descricao" rows="3" placeholder="Opcional: detalhes sobre a localização ou uso do bloco."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn sgm-btn-primary rounded-pill px-4">
                        <i class="bi bi-check2-circle me-2"></i>Salvar Bloco
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
