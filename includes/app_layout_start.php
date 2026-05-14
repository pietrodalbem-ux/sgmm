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

/* Mapeamento de rótulos de conta institucionais */
$labelsContas = [
    'admin'       => 'Conta Administrativa',
    'gestor'      => 'Conta Gestora',
    'tecnico'     => 'Conta Técnica',
    'solicitante' => 'Conta Solicitante'
];
$labelExibir = $labelsContas[$rawPerfil] ?? 'Conta Usuário';

$avatarName = urlencode($_SESSION['user_nome'] ?? 'U');

$navGestor = [
    ['key' => 'dashboard', 'label' => 'Visão geral', 'href' => 'gestor_dashboard.php', 'icon' => 'bi-speedometer2'],
    ['key' => 'chamados', 'label' => 'Chamados', 'href' => 'gestor_chamados.php', 'icon' => 'bi-ticket-perforated'],
    ['key' => 'usuarios', 'label' => 'Usuários', 'href' => 'gestor_usuarios.php', 'icon' => 'bi-people'],
    ['key' => 'blocos', 'label' => 'Blocos', 'href' => 'gestor_blocos.php', 'icon' => 'bi-box-seam'],
    ['key' => 'ambientes', 'label' => 'Ambientes', 'href' => 'gestor_ambientes.php', 'icon' => 'bi-building'],
    ['key' => 'tipos', 'label' => 'Tipos de serviço', 'href' => 'gestor_tipos_servico.php', 'icon' => 'bi-tags'],
];

if ($rawPerfil === 'admin') {
    $navGestor[] = ['key' => 'lixeira', 'label' => 'Lixeira', 'href' => 'gestor_lixeira.php', 'icon' => 'bi-trash3'];
}

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/sgm-institutional.css">
</head>
<body class="sgm-app">
<div id="toastStack" class="sgm-toast-stack" aria-live="polite"></div>
<div class="sgm-sidebar-backdrop" id="sgmSidebarBackdrop" aria-hidden="true"></div>
<div class="sgm-layout">
    <aside class="sgm-sidebar" id="sgmSidebar">
        <div class="sgm-sidebar-brand">
            <div class="sgm-logo">SGM</div>
            <small><?php echo htmlspecialchars($areaLabel, ENT_QUOTES, 'UTF-8'); ?></small>
        </div>
        <nav class="sgm-sidebar-nav">
            <?php foreach ($navItems as $item) :
                $active = ($NAV_ACTIVE === $item['key']) ? ' active' : '';
                ?>
                <a class="sgm-nav-link<?php echo $active; ?>" href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>">
                    <i class="bi <?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                    <span><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sgm-sidebar-footer">
            <div class="sgm-user-chip">
                <img src="https://ui-avatars.com/api/?name=<?php echo $avatarName; ?>&background=2563eb&color=fff&bold=true" width="38" height="38" alt="">
                <div class="sgm-user-meta">
                    <div class="name" style="font-size: 0.85rem; letter-spacing: 0.01em;"><?php echo htmlspecialchars($labelExibir, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <a href="api/logout.php" class="btn text-danger ms-auto p-0" title="Sair do sistema"><i class="bi bi-box-arrow-right fs-5"></i></a>
            </div>
        </div>
    </aside>
    <div class="sgm-content-wrap">
        <header class="sgm-topbar">
            <button type="button" class="sgm-menu-toggle" id="sgmSidebarToggle" aria-label="Abrir menu">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex flex-column">
                <h1 class="sgm-topbar-title"><?php echo htmlspecialchars($PAGE_TITLE, ENT_QUOTES, 'UTF-8'); ?></h1>
                <?php if ($PAGE_SUBTITLE) : ?>
                    <p class="sgm-topbar-sub d-none d-md-block"><?php echo htmlspecialchars($PAGE_SUBTITLE, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
            </div>
        </header>
        <main class="sgm-main">

