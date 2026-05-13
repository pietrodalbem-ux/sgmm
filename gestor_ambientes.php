<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Ambientes';
$PAGE_SUBTITLE = 'Salas e espaços vinculados a cada bloco.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'ambientes';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-ambientes.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div id="painelEdicao" class="sgm-card mb-4 d-none border-primary">
    <div class="sgm-card-header bg-primary text-white">
        <span>Editar Ambiente</span>
        <button type="button" class="btn btn-sm btn-close btn-close-white" id="btnFecharEdicao"></button>
    </div>
    <div class="sgm-card-pad">
        <div class="row g-4 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_amb_nome">Nome do Ambiente</label>
                <input type="text" class="form-control sgm-control" id="edit_amb_nome" maxlength="150">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted text-uppercase" for="edit_amb_bloco">Bloco Vinculado</label>
                <select class="form-select sgm-control" id="edit_amb_bloco"></select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn sgm-btn-primary w-100" id="btnSalvarEdicaoAmb">
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
                <span>Novo Ambiente</span>
            </div>
            <div class="sgm-card-pad">
                <form id="formNovoAmbiente">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="ambiente_nome">Nome da Sala/Laboratório</label>
                        <input type="text" class="form-control sgm-control" id="ambiente_nome" required placeholder="Ex.: Laboratório 101">
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="selectBlocoAmbiente">Bloco</label>
                        <select class="form-select sgm-control" id="selectBlocoAmbiente" required>
                            <option value="" disabled selected>Selecione um bloco...</option>
                        </select>
                    </div>
                    <button type="submit" class="btn sgm-btn-primary w-100 py-3">
                        <i class="bi bi-plus-lg me-2"></i>Adicionar Ambiente
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="sgm-card h-100">
            <div class="sgm-card-header">
                <span>Ambientes Cadastrados</span>
                <span class="badge bg-light text-primary rounded-pill" id="amb-contagem">0</span>
            </div>
            <div class="table-responsive">
                <table class="table sgm-table align-middle">
                    <thead>
                        <tr>
                            <th>Nome do Ambiente</th>
                            <th>Bloco</th>
                            <th class="text-end" style="width:120px">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="lista-ambientes-corpo">
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
