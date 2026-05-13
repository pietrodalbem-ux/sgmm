<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Blocos';
$PAGE_SUBTITLE = 'Cadastro de setores físicos vinculados aos ambientes.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'blocos';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-blocos.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div id="painelEdicao" class="sgm-card mb-4 d-none border-primary">
    <div class="sgm-card-header bg-primary text-white">
        <span>Editar Bloco</span>
        <button type="button" class="btn btn-sm btn-close btn-close-white" id="btnFecharEdicao"></button>
    </div>
    <div class="sgm-card-pad">
        <div class="row g-4">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_bloco_nome">Nome do Bloco</label>
                <input type="text" class="form-control sgm-control" id="edit_bloco_nome" maxlength="120">
            </div>
            <div class="col-md-7">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_bloco_desc">Descrição / Observações</label>
                <input type="text" class="form-control sgm-control" id="edit_bloco_desc" maxlength="255">
            </div>
            <div class="col-12">
                <button type="button" class="btn sgm-btn-primary px-4" id="btnSalvarEdicaoBloco">
                    <i class="bi bi-check2-circle me-2"></i>Salvar Alterações
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span>Novo Bloco</span>
            </div>
            <div class="sgm-card-pad">
                <form id="formNovoBloco">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="bloco_nome">Nome</label>
                        <input type="text" class="form-control sgm-control" id="bloco_nome" required placeholder="Ex.: Bloco A">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="bloco_descricao">Descrição</label>
                        <textarea class="form-control sgm-control" id="bloco_descricao" rows="3" placeholder="Opcional"></textarea>
                    </div>
                    <button type="submit" class="btn sgm-btn-primary w-100 py-3">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Bloco
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span>Blocos Cadastrados</span>
                <span class="badge bg-light text-primary rounded-pill" id="blocos-contagem">0</span>
            </div>
            <div class="table-responsive">
                <table class="table sgm-table align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th class="text-end" style="width:120px">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="lista-blocos-corpo">
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                Carregando dados...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
