<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$uid = (int) $_SESSION['user_id'];

// 1. Buscar a lista de tarefas ativas (Backend Real)
$sql = "SELECT c.id_chamado, c.titulo, c.descricao_problema, c.status, c.prioridade, c.data_abertura,
               a.nome AS ambiente_nome, b.nome AS bloco_nome,
               u.nome AS solicitante_nome
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        JOIN usuarios u ON c.id_solicitante = u.id_usuario
        WHERE c.id_tecnico = $uid
          AND c.status NOT IN ('concluido', 'cancelado')
          AND c.deleted_at IS NULL
        ORDER BY CASE WHEN c.prioridade = 'critica' THEN 1
                      WHEN c.prioridade = 'alta' THEN 2
                      WHEN c.prioridade = 'media' THEN 3
                      ELSE 4 END,
                 c.data_abertura ASC";

$res = $conn->query($sql);
$chamados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

// 2. Buscar estatísticas do técnico via SQL
$sqlStats = "SELECT 
                COUNT(*) AS total_ativa,
                SUM(CASE WHEN prioridade = 'critica' THEN 1 ELSE 0 END) AS total_critica,
                (SELECT COUNT(*) FROM chamados WHERE id_tecnico = $uid AND status = 'concluido' AND DATE(data_conclusao) = CURDATE() AND deleted_at IS NULL) AS concluidos_hoje
             FROM chamados 
             WHERE id_tecnico = $uid AND status NOT IN ('concluido', 'cancelado') AND deleted_at IS NULL";

$resStats = $conn->query($sqlStats);
$stats = $resStats ? $resStats->fetch_assoc() : ['total_ativa' => 0, 'total_critica' => 0, 'concluidos_hoje' => 0];

echo json_encode([
    'success' => true, 
    'data' => $chamados,
    'stats' => [
        'total_ativa' => (int)($stats['total_ativa'] ?? 0),
        'total_critica' => (int)($stats['total_critica'] ?? 0),
        'concluidos_hoje' => (int)($stats['concluidos_hoje'] ?? 0)
    ]
]);
