<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Gestão de Usuários';
$PAGE_SUBTITLE = 'Controle de contas, permissões e departamentos do sistema.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'usuarios';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-usuarios.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-card animate__animated animate__fadeIn">
    <div class="sgm-card-header">
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-people me-2 text-primary"></i>Usuários Cadastrados</span>
            <span class="badge bg-light text-primary rounded-pill" id="user-contagem">0</span>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <div class="input-group input-group-sm d-none d-md-flex" style="max-width: 250px;">
                <span class="input-group-text bg-light border-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" class="form-control bg-light border-0 rounded-end-pill" id="busca-usuarios" placeholder="Buscar usuário...">
            </div>
            <button type="button" class="btn sgm-btn-primary btn-sm rounded-pill px-4" id="btnAbrirModalNovo">
                <i class="bi bi-person-plus me-2"></i>Novo Usuário
            </button>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table sgm-table align-middle">
            <thead>
                <tr>
                    <th>Usuário</th>
                    <th>Perfil / Acesso</th>
                    <th>Departamento</th>
                    <th>Status</th>
                    <th class="text-end actions-column">Ações</th>
                </tr>
            </thead>
            <tbody id="lista-usuarios-corpo">
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="spinner-border text-primary spinner-border-sm me-2"></div>
                        <span class="text-muted fw-medium">Sincronizando banco de dados...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Cadastro/Edição -->
<div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="modalTitle">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formUsuario">
                <div class="modal-body p-4">
                    <input type="hidden" id="user_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_nome">Nome Completo</label>
                            <input type="text" class="form-control sgm-control" id="user_nome" required placeholder="Digite o nome completo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_email">E-mail Institucional</label>
                            <input type="email" class="form-control sgm-control" id="user_email" required placeholder="email@senai.br">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_perfil">Perfil de Acesso</label>
                            <select class="form-select sgm-control" id="user_perfil" required>
                                <option value="solicitante">Solicitante</option>
                                <option value="tecnico">Técnico</option>
                                <option value="gestor">Gestor</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_departamento">Departamento</label>
                            <select class="form-select sgm-control" id="user_departamento">
                                <option value="">Nenhum / Geral</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_senha">Senha <span id="pass-hint" class="d-none text-lowercase">(deixe em branco para manter)</span></label>
                            <input type="password" class="form-control sgm-control" id="user_senha" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase" for="user_ativo">Status da Conta</label>
                            <select class="form-select sgm-control" id="user_ativo">
                                <option value="1">Ativa / Habilitada</option>
                                <option value="0">Inativa / Desabilitada</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn sgm-btn-primary rounded-pill px-4">
                        <i class="bi bi-person-check me-2"></i>Salvar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
