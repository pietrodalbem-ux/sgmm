<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Lixeira do Sistema';
$PAGE_SUBTITLE = 'Gerenciamento de registros excluídos. Você pode restaurar ou remover permanentemente.';
$LAYOUT = 'gestor';
$NAV_ACTIVE = 'lixeira'; // Preciso adicionar ao menu
$SGM_EXTRA_SCRIPTS = ['assets/js/gestor-lixeira.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="row g-4">
    <div class="col-12">
        <div class="sgm-card animate__animated animate__fadeIn">
            <div class="sgm-card-header bg-light">
                <span class="text-dark"><i class="bi bi-trash3 me-2 text-danger"></i>Itens Excluídos</span>
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="carregarLixeira()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Sincronizar
                </button>
            </div>
            
            <div class="sgm-card-pad">
                <ul class="nav nav-pills gap-2 mb-4" id="lixeiraTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-usuarios">Usuários</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-blocos">Blocos</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-ambientes">Ambientes</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link rounded-pill px-4" data-bs-toggle="pill" data-bs-target="#tab-tipos">Categorias</button>
                    </li>
                </ul>

                <div class="tab-content" id="lixeiraContent">
                    <!-- Usuários -->
                    <div class="tab-pane fade show active" id="tab-usuarios">
                        <div class="table-responsive">
                            <table class="table sgm-table">
                                <thead>
                                    <tr>
                                        <th>Nome / Email</th>
                                        <th>Excluído em</th>
                                        <th>Por</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="list-del-usuarios"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Blocos -->
                    <div class="tab-pane fade" id="tab-blocos">
                        <div class="table-responsive">
                            <table class="table sgm-table">
                                <thead>
                                    <tr>
                                        <th>Nome do Bloco</th>
                                        <th>Excluído em</th>
                                        <th>Por</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="list-del-blocos"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Ambientes -->
                    <div class="tab-pane fade" id="tab-ambientes">
                        <div class="table-responsive">
                            <table class="table sgm-table">
                                <thead>
                                    <tr>
                                        <th>Nome do Ambiente</th>
                                        <th>Excluído em</th>
                                        <th>Por</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="list-del-ambientes"></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tipos -->
                    <div class="tab-pane fade" id="tab-tipos">
                        <div class="table-responsive">
                            <table class="table sgm-table">
                                <thead>
                                    <tr>
                                        <th>Nome da Categoria</th>
                                        <th>Excluído em</th>
                                        <th>Por</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="list-del-tipos"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
