<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    header('Location: login.php');
    exit;
}
$PAGE_TITLE = 'Minhas tarefas';
$PAGE_SUBTITLE = 'Chamados atribuídos a você em andamento.';
$LAYOUT = 'tecnico';
$NAV_ACTIVE = 'tarefas';
$SGM_EXTRA_SCRIPTS = ['assets/js/tecnico-fila.js'];
require_once __DIR__ . '/includes/app_layout_start.php';
?>

<div class="sgm-page-heading mb-4">
    <h1>Minhas tarefas</h1>
    <p>Priorize atendimentos conforme criticidade e prazo de abertura.</p>
</div>

<div id="fila-tarefas" class="d-flex flex-column gap-3">
    <div class="text-muted py-5 text-center">Carregando fila…</div>
</div>

<?php require_once __DIR__ . '/includes/app_layout_end.php'; ?>
