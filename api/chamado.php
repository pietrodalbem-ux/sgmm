<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_chamado > 0) {
    // Detalhes de um chamado específico
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, 
                   u.nome as solicitante_nome, t.nome as tipo_nome
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            JOIN usuarios u ON c.id_solicitante = u.id_usuario
            JOIN tipo_servico t ON c.id_tipo_servico = t.id_tipo_servico
            WHERE c.id_chamado = $id_chamado AND c.deleted_at IS NULL";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $chamado = $result->fetch_assoc();
        $perfil = $_SESSION['user_perfil'] ?? '';
        $uid = (int) $_SESSION['user_id'];
        
        // Verificação de permissão
        if ($perfil === 'tecnico' && (int) ($chamado['id_tecnico'] ?? 0) !== $uid) {
            http_response_code(403);
            echo json_encode(["error" => "Acesso negado."]);
            exit;
        }
        if ($perfil === 'solicitante' && (int) ($chamado['id_solicitante'] ?? 0) !== $uid) {
            http_response_code(403);
            echo json_encode(["error" => "Acesso negado."]);
            exit;
        }
        echo json_encode(["success" => true, "data" => $chamado]);
    } else {
        http_response_code(404);
        echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
    }
    exit;
} else {
    // Listagem para o solicitante logado + Estatísticas Reais via SQL
    $sql_list = "SELECT c.id_chamado, c.titulo, c.descricao_problema, c.status, c.data_abertura, 
                       a.nome as ambiente_nome, b.nome as bloco_nome
                FROM chamados c
                JOIN ambientes a ON c.id_ambiente = a.id_ambiente
                JOIN blocos b ON a.id_bloco = b.id_bloco
                WHERE c.id_solicitante = $user_id AND c.deleted_at IS NULL
                ORDER BY c.data_abertura DESC";
    
    $sql_stats = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status NOT IN ('concluido', 'cancelado') THEN 1 ELSE 0 END) as em_atendimento,
                    SUM(CASE WHEN status IN ('concluido', 'cancelado') THEN 1 ELSE 0 END) as finalizados
                  FROM chamados 
                  WHERE id_solicitante = $user_id AND deleted_at IS NULL";
    
    $res_list = $conn->query($sql_list);
    $res_stats = $conn->query($sql_stats);
    
    echo json_encode([
        "success" => true,
        "data" => $res_list ? $res_list->fetch_all(MYSQLI_ASSOC) : [],
        "stats" => $res_stats ? $res_stats->fetch_assoc() : ["total" => 0, "em_atendimento" => 0, "finalizados" => 0]
    ]);
}