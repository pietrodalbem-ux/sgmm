<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

// 1. Overview Stats
$sqlOverview = "SELECT 
            SUM(CASE WHEN status IN ('aberto', 'triagem') THEN 1 ELSE 0 END) AS aguardando_triagem,
            SUM(CASE WHEN status IN ('em_andamento', 'aguardando_peca') THEN 1 ELSE 0 END) AS em_atendimento,
            SUM(CASE WHEN status = 'concluido' AND DATE(data_conclusao) = CURDATE() THEN 1 ELSE 0 END) AS concluidos_hoje,
            SUM(CASE WHEN prioridade = 'critica' AND status NOT IN ('concluido', 'cancelado') THEN 1 ELSE 0 END) AS criticos_urgentes,
            COUNT(*) AS total
        FROM chamados WHERE deleted_at IS NULL";
$resOverview = $conn->query($sqlOverview);
$stats = $resOverview->fetch_assoc();

// 2. Status Distribution
$sqlStatus = "SELECT status, COUNT(*) as count FROM chamados WHERE deleted_at IS NULL GROUP BY status";
$resStatus = $conn->query($sqlStatus);
$statusDist = [];
while ($row = $resStatus->fetch_assoc()) {
    $statusDist[$row['status']] = (int)$row['count'];
}

// 3. Chamados por Técnico
$sqlTecnicos = "SELECT u.nome, COUNT(c.id_chamado) as count 
                FROM usuarios u 
                INNER JOIN chamados c ON u.id_usuario = c.id_tecnico 
                WHERE u.perfil = 'tecnico' AND c.deleted_at IS NULL AND u.deleted_at IS NULL
                GROUP BY u.id_usuario 
                ORDER BY count DESC 
                LIMIT 5";
$resTecnicos = $conn->query($sqlTecnicos);
$tecnicosDist = [];
while ($row = $resTecnicos->fetch_assoc()) {
    $tecnicosDist[] = $row;
}

// 4. Chamados por Bloco
$sqlBlocos = "SELECT b.nome, COUNT(c.id_chamado) as count 
              FROM blocos b 
              INNER JOIN ambientes a ON b.id_bloco = a.id_bloco 
              INNER JOIN chamados c ON a.id_ambiente = c.id_ambiente 
              WHERE c.deleted_at IS NULL AND a.deleted_at IS NULL AND b.deleted_at IS NULL
              GROUP BY b.id_bloco 
              ORDER BY count DESC";
$resBlocos = $conn->query($sqlBlocos);
$blocosDist = [];
while ($row = $resBlocos->fetch_assoc()) {
    $blocosDist[] = $row;
}

// 5. Evolução Mensal (Últimos 6 meses)
$sqlEvolucao = "SELECT DATE_FORMAT(data_abertura, '%Y-%m') as mes, COUNT(*) as count 
                FROM chamados 
                WHERE data_abertura >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND deleted_at IS NULL
                GROUP BY mes 
                ORDER BY mes ASC";
$resEvolucao = $conn->query($sqlEvolucao);
$evolucaoMensal = [];
while ($row = $resEvolucao->fetch_assoc()) {
    $evolucaoMensal[] = $row;
}

echo json_encode([
    "success" => true,
    "stats" => [
        "aguardando" => (int)($stats['aguardando_triagem'] ?? 0),
        "atendimento" => (int)($stats['em_atendimento'] ?? 0),
        "concluidos_hoje" => (int)($stats['concluidos_hoje'] ?? 0),
        "criticos" => (int)($stats['criticos_urgentes'] ?? 0),
        "total" => (int)($stats['total'] ?? 0)
    ],
    "charts" => [
        "status" => $statusDist,
        "tecnicos" => $tecnicosDist,
        "blocos" => $blocosDist,
        "evolucao" => $evolucaoMensal
    ]
]);
