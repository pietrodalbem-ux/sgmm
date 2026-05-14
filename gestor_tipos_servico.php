<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Tipos de Serviço';
$PAGE_SUBTITLE = 'Categorias utilizadas para classificar e organizar os chamados de manutenção.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'tipos';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-tipos-servico.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-card animate__animated animate__fadeIn">
    <div class="sgm-card-header">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-tags me-2 text-primary"></i>Categorias de Serviço</span>
            <span class="badge bg-light text-primary rounded-pill" id="tipo-contagem">0</span>
        </div>
        <button type="button" class="btn sgm-btn-primary btn-sm rounded-pill px-4" id="btnAbrirModalNovo">
            <i class="bi bi-plus-lg me-2"></i>Nova Categoria
        </button>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th>Nome da Categoria</th>
                    <th>Descrição</th>
                    <th class="text-end actions-column">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-tipos-corpo">
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
<div class="modal fade" id="modalTipo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Nova Categoria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTipo">
                <div class="modal-body p-4">
                    <input type="hidden" id="tipo_id">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="tipo_nome">Nome da Categoria</label>
                        <input type="text" class="form-control sgm-control" id="tipo_nome" required placeholder="Ex.: Elétrica, Hidráulica, TI, etc.">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="tipo_descricao">Descrição</label>
                        <textarea class="form-control sgm-control" id="tipo_descricao" rows="3" placeholder="Opcional: detalhes sobre o que abrange esta categoria."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn sgm-btn-primary rounded-pill px-4">
                        <i class="bi bi-check2-circle me-2"></i>Salvar Categoria
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
