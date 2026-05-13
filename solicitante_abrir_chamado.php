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
    <div class="col-lg-10 col-xl-9">
        <div class="sgm-card">
            <div class="sgm-card-header">
                <span>Nova Solicitação de Manutenção</span>
            </div>
            <div class="sgm-card-pad">
                <form id="formChamado">
                    <!-- Passo 1: Localização -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <div class="pe-md-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem; font-weight: 800;">1</div>
                                    <h2 class="h6 fw-800 mb-0 text-uppercase tracking-wider">Localização</h2>
                                </div>
                                <p class="text-muted small">Identifique onde o problema está ocorrendo para que nossa equipe possa chegar rapidamente.</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase" for="selectBloco">Bloco / Setor</label>
                                    <select class="form-select sgm-control" id="selectBloco" required>
                                        <option value="" disabled selected>Selecione o bloco...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted text-uppercase" for="selectAmbiente">Ambiente / Sala</label>
                                    <select class="form-select sgm-control" id="selectAmbiente" disabled required>
                                        <option value="">Aguardando bloco...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="opacity-10 mb-5">

                    <!-- Passo 2: Detalhes -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <div class="pe-md-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem; font-weight: 800;">2</div>
                                    <h2 class="h6 fw-800 mb-0 text-uppercase tracking-wider">O Problema</h2>
                                </div>
                                <p class="text-muted small">Seja o mais específico possível na descrição para agilizar a triagem.</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase" for="selectTipo">Tipo de Serviço</label>
                                    <select class="form-select sgm-control" id="selectTipo" required>
                                        <option value="" disabled selected>Selecione a categoria...</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted text-uppercase" for="descricao">Descrição Detalhada</label>
                                    <textarea id="descricao" class="form-control sgm-control" rows="4" required placeholder="Ex: A lâmpada está piscando, o ar-condicionado não gela, etc."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="opacity-10 mb-5">

                    <!-- Passo 3: Foto -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <div class="pe-md-4">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.8rem; font-weight: 800;">3</div>
                                    <h2 class="h6 fw-800 mb-0 text-uppercase tracking-wider">Evidências</h2>
                                </div>
                                <p class="text-muted small">Fotos ajudam o técnico a levar as ferramentas e peças corretas.</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="upload-area bg-light rounded-4 border-2 border-dashed p-4 text-center border-primary-subtle">
                                <input type="file" id="foto" class="d-none" accept="image/*">
                                <div id="upload-prompt">
                                    <i class="bi bi-camera fs-1 text-primary opacity-50 mb-3 d-block"></i>
                                    <label for="foto" class="btn sgm-btn-outline rounded-pill px-4">Selecionar Imagem</label>
                                    <p class="small text-muted mt-3 mb-0">Você pode anexar uma foto do local ou do defeito.</p>
                                </div>
                                <div id="preview-container" class="mt-2 d-none">
                                    <div class="position-relative d-inline-block">
                                        <img id="img-preview" src="" class="rounded-4 border shadow-lg" style="max-height: 200px; max-width: 100%;" alt="">
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-n2 shadow" onclick="document.getElementById('foto').value=''; document.getElementById('preview-container').classList.add('d-none'); document.getElementById('upload-prompt').classList.remove('d-none');">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5">
                        <button type="reset" class="btn btn-light rounded-pill px-5 fw-bold text-muted">Limpar</button>
                        <button type="submit" class="btn sgm-btn-primary rounded-pill px-5 py-3 fw-800">
                            Enviar Solicitação <i class="bi bi-send-fill ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
