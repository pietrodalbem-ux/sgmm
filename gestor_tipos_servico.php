<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Tipos de serviço';
$PAGE_SUBTITLE = 'Categorias utilizadas na abertura e classificação dos chamados.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'tipos';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-tipos-servico.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div id="painelEdicao" class="sgm-card mb-4 d-none border-primary">
    <div class="sgm-card-header bg-primary text-white">
        <span>Editar Categoria</span>
        <button type="button" class="btn btn-sm btn-close btn-close-white" id="btnFecharEdicao"></button>
    </div>
    <div class="sgm-card-pad">
        <div class="row g-4 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_tipo_nome">Nome da Categoria</label>
                <input type="text" class="form-control sgm-control" id="edit_tipo_nome" maxlength="120">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_tipo_desc">Descrição</label>
                <input type="text" class="form-control sgm-control" id="edit_tipo_desc" maxlength="255">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn sgm-btn-primary w-100" id="btnSalvarEdicaoTipo">
                    <i class="bi bi-check2-circle me-2"></i>Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span>Nova Categoria</span>
            </div>
            <div class="sgm-card-pad">
                <form id="formNovoTipo">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="tipo_nome">Nome (ex: Elétrica)</label>
                        <input type="text" class="form-control sgm-control" id="tipo_nome" required placeholder="Ex.: Elétrica">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="tipo_descricao">Descrição da Categoria</label>
                        <textarea class="form-control sgm-control" id="tipo_descricao" rows="3" placeholder="Opcional"></textarea>
                    </div>
                    <button type="submit" class="btn sgm-btn-primary w-100 py-3">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Categoria
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span>Categorias Cadastradas</span>
                <span class="badge bg-light text-primary rounded-pill" id="tipo-contagem">0</span>
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
                    <tbody id="lista-tipos-corpo">
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
