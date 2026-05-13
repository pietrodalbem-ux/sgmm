<?php
/**
 * Layout principal SGM — defina antes de incluir:
 * @var string $PAGE_TITLE
 * @var string $LAYOUT        'gestor' | 'solicitante' | 'tecnico'
 * @var string $NAV_ACTIVE    chave do item ativo no menu
 * @var string|null $PAGE_SUBTITLE opcional — linha abaixo do título na topbar
 */
if (!isset($PAGE_TITLE)) {
    $PAGE_TITLE = 'SGM';
}
$LAYOUT = $LAYOUT ?? 'gestor';
$NAV_ACTIVE = $NAV_ACTIVE ?? '';
$PAGE_SUBTITLE = $PAGE_SUBTITLE ?? null;
$rawPerfil = $_SESSION['user_perfil'] ?? '';

/* Admin: identidade genérica “Admin”, sem nome pessoal na interface */
if ($rawPerfil === 'admin') {
    $userNome = 'Admin';
    $userPerfil = '';
    $avatarName = urlencode('Admin');
} else {
    $userNome = $_SESSION['user_nome'] ?? 'Usuário';
    $userPerfil = $rawPerfil;
    $avatarName = urlencode($_SESSION['user_nome'] ?? 'U');
}

$navGestor = [
    ['key' => 'dashboard', 'label' => 'Visão geral', 'href' => 'gestor_dashboard.php', 'icon' => 'bi-speedometer2'],
    ['key' => 'chamados', 'label' => 'Chamados', 'href' => 'gestor_chamados.php', 'icon' => 'bi-ticket-perforated'],
    ['key' => 'blocos', 'label' => 'Blocos', 'href' => 'gestor_blocos.php', 'icon' => 'bi-box-seam'],
    ['key' => 'ambientes', 'label' => 'Ambientes', 'href' => 'gestor_ambientes.php', 'icon' => 'bi-building'],
    ['key' => 'tipos', 'label' => 'Tipos de serviço', 'href' => 'gestor_tipos_servico.php', 'icon' => 'bi-tags'],
];

$navSolicitante = [
    ['key' => 'dashboard', 'label' => 'Painel', 'href' => 'solicitante_dashboard.php', 'icon' => 'bi-grid-1x2'],
    ['key' => 'novo', 'label' => 'Nova solicitação', 'href' => 'solicitante_abrir_chamado.php', 'icon' => 'bi-plus-circle'],
];

$navTecnico = [
    ['key' => 'tarefas', 'label' => 'Minhas tarefas', 'href' => 'tecnico_minhas_tarefas.php', 'icon' => 'bi-wrench-adjustable'],
];

switch ($LAYOUT) {
    case 'solicitante':
        $navItems = $navSolicitante;
        $areaLabel = 'Área do solicitante';
        break;
    case 'tecnico':
        $navItems = $navTecnico;
        $areaLabel = 'Área do técnico';
        break;
    default:
        $navItems = $navGestor;
        $areaLabel = ($rawPerfil === 'admin') ? 'Admin' : 'Gestão da manutenção';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($PAGE_TITLE, ENT_QUOTES, 'UTF-8'); ?> — SGM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/sgm-institutional.css">
</head>
<body class="sgm-app">
<div id="toastStack" class="sgm-toast-stack" aria-live="polite"></div>
<div class="sgm-sidebar-backdrop" id="sgmSidebarBackdrop" aria-hidden="true"></div>
<div class="sgm-layout">
    <aside class="sgm-sidebar" id="sgmSidebar">
        <div class="sgm-sidebar-brand">
            <div class="sgm-logo">SG<span>M</span></div>
            <small><?php echo htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8'); ?></small>
        </div>
        <nav class="sgm-sidebar-nav">
            <?php foreach ($navItems as $item) :
                $active = ($NAV_ACTIVE === $item['key']) ? ' active' : '';
                ?>
                <a class="sgm-nav-link<?php echo $active; ?>" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <i class="bi <?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                    <?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sgm-sidebar-footer">
            <div class="sgm-user-chip">
                <img src="https://ui-avatars.com/api/?name=<?php echo $avatarName; ?>&background=0d3b66&color=fff" width="40" height="40" alt="">
                <div class="sgm-user-meta">
                    <div class="name"><?php echo htmlspecialchars($userNome, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php if ($userPerfil !== '') : ?>
                        <div class="role"><?php echo htmlspecialchars($userPerfil, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>
                <a href="api/logout.php" class="btn sgm-btn-ghost text-danger" title="Sair"><i class="bi bi-box-arrow-right fs-5"></i></a>
            </div>
        </div>
    </aside>
    <div class="sgm-content-wrap">
        <header class="sgm-topbar">
            <div class="d-flex align-items-center gap-2 min-w-0">
                <button type="button" class="sgm-menu-toggle" id="sgmSidebarToggle" aria-label="Abrir menu">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="min-w-0">
                    <h1 class="sgm-topbar-title text-truncate"><?php echo htmlspecialchars($PAGE_TITLE, ENT_QUOTES, 'UTF-8'); ?></h1>
                    <?php if ($PAGE_SUBTITLE) : ?>
                        <p class="sgm-topbar-sub text-truncate"><?php echo htmlspecialchars($PAGE_SUBTITLE, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <main class="sgm-main">
