<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Segurança: Gestores e Admins apenas
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

// Parâmetros de Filtro/Busca (Backend Real)
$status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';
$busca = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

$where = "WHERE c.deleted_at IS NULL";

if ($status) {
    $where .= " AND c.status = '$status'";
}

if ($busca) {
    $where .= " AND (
        c.id_chamado LIKE '%$busca%' OR 
        c.titulo LIKE '%$busca%' OR 
        u.nome LIKE '%$busca%' OR 
        a.nome LIKE '%$busca%' OR 
        b.nome LIKE '%$busca%' OR
        t.nome LIKE '%$busca%'
    )";
}

$sql = "SELECT c.id_chamado, c.titulo, c.descricao_problema, c.status, c.prioridade, 
               c.data_abertura, a.nome as ambiente_nome, b.nome as bloco_nome,
               u.nome as solicitante_nome, t.nome as tecnico_nome
        FROM chamados c
        JOIN ambientes a ON c.id_ambiente = a.id_ambiente
        JOIN blocos b ON a.id_bloco = b.id_bloco
        JOIN usuarios u ON c.id_solicitante = u.id_usuario
        LEFT JOIN usuarios t ON c.id_tecnico = t.id_usuario
        $where
        ORDER BY c.data_abertura DESC";

$result = $conn->query($sql);
if ($result) {
    echo json_encode([
        "success" => true,
        "data" => $result->fetch_all(MYSQLI_ASSOC)
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erro ao processar consulta no banco: " . $conn->error
    ]);
}