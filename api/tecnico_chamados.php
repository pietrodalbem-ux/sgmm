<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'tecnico') {
    echo json_encode(['success' => false, 'message' => 'Acesso negado.']);
    exit;
}

$uid = (int) $_SESSION['user_id'];

$sql = "SELECT c.id_chamado, c.titulo, c.descricao_problema, c.status, c.prioridade, c.data_abertura,
               a.nome AS ambiente_nome, b.nome AS bloco_nome,
               u.nome AS solicitante_nome
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        JOIN usuarios u ON c.id_solicitante = u.id_usuario
        WHERE c.id_tecnico = $uid
          AND c.status NOT IN ('concluido', 'cancelado')
        ORDER BY CASE WHEN c.prioridade = 'critica' THEN 1
                      WHEN c.prioridade = 'alta' THEN 2
                      WHEN c.prioridade = 'media' THEN 3
                      ELSE 4 END,
                 c.data_abertura ASC";

$res = $conn->query($sql);
if (!$res) {
    echo json_encode(['success' => false, 'message' => $conn->error]);
    exit;
}

echo json_encode(['success' => true, 'data' => $res->fetch_all(MYSQLI_ASSOC)]);
