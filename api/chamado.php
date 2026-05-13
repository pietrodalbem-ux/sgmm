<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$id_chamado = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_chamado > 0) {
    // Ajustado para bater com o novo esquema do bd.sql
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome, 
                   u.nome as solicitante_nome, t.nome as tipo_nome
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            JOIN usuarios u ON c.id_solicitante = u.id_usuario
            JOIN tipo_servico t ON c.id_tipo_servico = t.id_tipo_servico
            WHERE c.id_chamado = $id_chamado";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $chamado = $result->fetch_assoc();
        $perfil = $_SESSION['user_perfil'] ?? '';
        $uid = (int) $_SESSION['user_id'];
        if ($perfil === 'tecnico' && (int) ($chamado['id_tecnico'] ?? 0) !== $uid) {
            http_response_code(403);
            echo json_encode(["error" => "Acesso negado a este chamado."]);
            exit;
        }
        if ($perfil === 'solicitante' && (int) ($chamado['id_solicitante'] ?? 0) !== $uid) {
            http_response_code(403);
            echo json_encode(["error" => "Acesso negado a este chamado."]);
            exit;
        }
        echo json_encode($chamado);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Chamado não encontrado"]);
    }
    exit;
} else {
    // Listar todos os chamados do solicitante logado se não passar ID
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT c.*, a.nome as ambiente_nome, b.nome as bloco_nome
            FROM chamados c
            JOIN ambientes a ON c.id_ambiente = a.id_ambiente
            JOIN blocos b ON a.id_bloco = b.id_bloco
            WHERE c.id_solicitante = $user_id
            ORDER BY c.data_abertura DESC";
    $result = $conn->query($sql);
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}
?>