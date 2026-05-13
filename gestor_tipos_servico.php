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

<div class="sgm-page-heading mb-4">
    <h1>Tipos de serviço</h1>
    <p>Defina as categorias de manutenção (elétrica, hidráulica, predial, informática etc.).</p>
</div>

<div id="painelEdicao" class="sgm-card sgm-card-pad sgm-edit-panel d-none mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h6 mb-0 fw-semibold">Editar tipo</h2>
        <button type="button" class="btn btn-sm sgm-btn-outline" id="btnFecharEdicao">Fechar</button>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="sgm-form-label" for="edit_tipo_nome">Nome</label>
            <input type="text" class="form-control sgm-control" id="edit_tipo_nome" maxlength="120">
        </div>
        <div class="col-md-5">
            <label class="sgm-form-label" for="edit_tipo_desc">Descrição</label>
            <input type="text" class="form-control sgm-control" id="edit_tipo_desc" maxlength="255">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn sgm-btn-primary w-100" id="btnSalvarEdicaoTipo">Salvar</button>
        </div>
    </div>
</div>

<div class="sgm-card mb-4">
    <div class="sgm-card-header">Novo tipo</div>
    <div class="sgm-card-pad">
        <form id="formNovoTipo" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="sgm-form-label" for="tipo_nome">Nome</label>
                <input type="text" class="form-control sgm-control" id="tipo_nome" required placeholder="Ex.: Elétrica">
            </div>
            <div class="col-md-5">
                <label class="sgm-form-label" for="tipo_descricao">Descrição</label>
                <input type="text" class="form-control sgm-control" id="tipo_descricao" placeholder="Opcional">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn sgm-btn-primary w-100">Adicionar</button>
            </div>
        </form>
    </div>
</div>

<div class="sgm-card">
    <div class="sgm-card-header d-flex justify-content-between align-items-center">
        <span>Tipos cadastrados</span>
        <span class="small text-muted" id="tipo-contagem"></span>
    </div>
    <div class="table-responsive sgm-table-wrap border-0 rounded-0">
        <table class="table sgm-table mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th class="text-end" style="width:8rem">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-tipos-corpo">
                <tr><td colspan="3" class="text-center text-muted py-4">Carregando…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
