<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'solicitante') {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Nova solicitação';
$PAGE_SUBTITLE = 'Informe local, tipo de serviço e descrição do problema.';
$LAYOUT = 'solicitante';
$NAV_ACTIVE = 'novo';
$SGM_EXTRA_SCRIPTS = ['assets/js/solicitante-chamado-form.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="sgm-card">
            <div class="sgm-card-header">Formulário de abertura</div>
            <div class="sgm-card-pad">
                <form id="formChamado">
                    <div class="border-bottom pb-3 mb-4">
                        <span class="badge text-bg-primary mb-2">1</span>
                        <h2 class="h6 fw-bold">Localização</h2>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="sgm-form-label" for="selectBloco">Bloco / setor</label>
                            <select class="form-select sgm-control" id="selectBloco" required>
                                <option value="" disabled selected>Selecione o bloco…</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="sgm-form-label" for="selectAmbiente">Ambiente / sala</label>
                            <select class="form-select sgm-control" id="selectAmbiente" disabled required>
                                <option value="">Aguardando bloco…</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-bottom pb-3 mb-4">
                        <span class="badge text-bg-primary mb-2">2</span>
                        <h2 class="h6 fw-bold">Descrição do problema</h2>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="sgm-form-label" for="selectTipo">Categoria do serviço</label>
                            <select class="form-select sgm-control" id="selectTipo" required>
                                <option value="" disabled selected>Selecione a categoria…</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="sgm-form-label" for="descricao">Descrição detalhada</label>
                            <textarea id="descricao" class="form-control sgm-control" rows="4" required placeholder="Explique o que ocorreu, desde quando e se há risco à segurança."></textarea>
                        </div>
                    </div>

                    <div class="border-bottom pb-3 mb-4">
                        <span class="badge text-bg-primary mb-2">3</span>
                        <h2 class="h6 fw-bold">Evidência (opcional)</h2>
                    </div>
                    <div class="mb-4">
                        <div class="border border-2 border-dashed rounded-3 p-4 text-center bg-light">
                            <i class="bi bi-camera fs-3 text-muted d-block mb-2"></i>
                            <label for="foto" class="btn btn-sm sgm-btn-outline mb-0">Selecionar imagem</label>
                            <input type="file" id="foto" class="d-none" accept="image/*">
                            <p class="small text-muted mt-2 mb-0">Formatos de imagem comuns (JPG, PNG).</p>
                            <div id="preview-container" class="mt-3 d-none">
                                <img id="img-preview" src="" class="rounded-3 border shadow-sm" style="max-height:160px" alt="">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn sgm-btn-primary w-100 py-2">
                        <i class="bi bi-send-fill me-2"></i>Enviar solicitação
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
