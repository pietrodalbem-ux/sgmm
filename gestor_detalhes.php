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

<div class="mb-4">
    <a href="<?php echo htmlspecialchars($voltarHref, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-light rounded-pill px-4 text-muted fw-bold">
        <i class="bi bi-arrow-left me-2"></i>Voltar para a lista
    </a>
</div>

<div class="row g-4">
    <div class="<?php echo $isGestor ? 'col-lg-8' : 'col-12'; ?>">
        <div class="sgm-card mb-4">
            <div class="sgm-card-header">
                <span>Detalhes da Solicitação</span>
            </div>
            <div class="sgm-card-pad" id="detalhesChamado">
                <!-- Skeleton Loader -->
                <div class="py-5 text-center">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-3 fw-medium">Obtendo informações do chamado...</p>
                </div>
            </div>
        </div>
        <div id="areaFechamento"></div>
    </div>

    <?php if ($isGestor) : ?>
    <div class="col-lg-4">
        <div class="sgm-card sticky-top" style="top: 100px; z-index: 800;">
            <div class="sgm-card-header">
                <span>Gerenciamento</span>
            </div>
            <div class="sgm-card-pad">
                <form id="formAtribuir">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="selectTecnico">Técnico Responsável</label>
                        <select id="selectTecnico" class="form-select sgm-control" required>
                            <option value="">Selecione um técnico...</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="prioridade">Nível de Prioridade</label>
                        <select id="prioridade" class="form-select sgm-control" required>
                            <option value="baixa">Baixa</option>
                            <option value="media">Média</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase" for="data_prevista">Prazo de Conclusão</label>
                        <input type="datetime-local" id="data_prevista" class="form-control sgm-control">
                    </div>
                    <button type="submit" class="btn sgm-btn-primary w-100 py-3 rounded-pill fw-bold">
                        Atualizar Gestão <i class="bi bi-save2 ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div id="sgmChamadoMeta" class="d-none" data-id="<?php echo (int) $id; ?>" data-gestor="<?php echo $isGestor ? '1' : '0'; ?>"></div>

<div class="modal fade" id="modalFoto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-2xl">
            <div class="modal-body p-0 bg-dark position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-lg" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                <img id="imgModal" class="img-fluid w-100" src="" alt="Evidência do chamado">
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
