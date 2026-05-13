<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_perfil'], ['gestor', 'admin', 'tecnico'], true)) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$id = (int) $_GET['id'];
$perfil = $_SESSION['user_perfil'];
$isGestor = in_array($perfil, ['gestor', 'admin'], true);
$voltarHref = $perfil === 'tecnico' ? 'tecnico_minhas_tarefas.php' : 'gestor_chamados.php';

$PAGE_TITLE = 'Chamado #' . $id;
$PAGE_SUBTITLE = 'Detalhes da solicitação e ações de gestão.';
$LAYOUT = $perfil === 'tecnico' ? 'tecnico' : 'gestor';
$NAV_ACTIVE = $perfil === 'tecnico' ? 'tarefas' : 'chamados';
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-detalhes.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="mb-3">
    <a href="<?php echo htmlspecialchars($voltarHref, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm sgm-btn-outline">
        <i class="bi bi-arrow-left me-1"></i> Voltar
    </a>
</div>

<div class="row g-4">
    <div class="<?php echo $isGestor ? 'col-lg-8' : 'col-12'; ?>">
        <div class="sgm-card mb-3">
            <div class="sgm-card-header">Dados da solicitação</div>
            <div class="sgm-card-pad" id="detalhesChamado">
                <div class="d-flex align-items-center gap-3 py-4 text-muted">
                    <div class="sgm-skeleton" style="width:2rem;height:2rem;border-radius:50%"></div>
                    <div>
                        <div class="sgm-skeleton mb-2" style="width:180px;height:12px"></div>
                        <div class="sgm-skeleton" style="width:240px;height:10px"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="areaFechamento"></div>
    </div>

    <?php if ($isGestor) : ?>
    <div class="col-lg-4">
        <div class="sgm-card">
            <div class="sgm-card-header">Gerenciar chamado</div>
            <div class="sgm-card-pad">
                <form id="formAtribuir">
                    <div class="mb-3">
                        <label class="sgm-form-label" for="selectTecnico">Técnico responsável</label>
                        <select id="selectTecnico" class="form-select sgm-control" required>
                            <option value="">Selecione…</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="sgm-form-label" for="prioridade">Prioridade</label>
                        <select id="prioridade" class="form-select sgm-control" required>
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="sgm-form-label" for="data_prevista">Data prevista</label>
                        <input type="date" id="data_prevista" class="form-control sgm-control">
                    </div>
                    <button type="submit" class="btn sgm-btn-primary w-100">Salvar alterações</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="sgmChamadoMeta" class="d-none" data-id="<?php echo (int) $id; ?>" data-gestor="<?php echo $isGestor ? '1' : '0'; ?>"></div>

<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-0 bg-dark">
                <img id="imgModal" class="img-fluid" src="" alt="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn sgm-btn-outline" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
