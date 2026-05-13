<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['id_chamado'])) {
    echo json_encode(["success" => false, "message" => "Dados incompletos."]);
    exit;
}

$id_chamado = (int) $data['id_chamado'];
$id_tecnico = isset($data['id_tecnico']) ? (int) $data['id_tecnico'] : 0;
$prioridade = isset($data['prioridade']) ? trim($data['prioridade']) : 'media';
$allowedPrior = ['baixa', 'media', 'alta', 'critica'];
if (!in_array($prioridade, $allowedPrior, true)) {
    $prioridade = 'media';
}

if ($id_tecnico <= 0) {
    echo json_encode(["success" => false, "message" => "Selecione um técnico responsável."]);
    exit;
}

$prioridadeEsc = $conn->real_escape_string($prioridade);

$sql = "UPDATE chamados SET
            id_tecnico = $id_tecnico,
            prioridade = '$prioridadeEsc',
            status = CASE WHEN status IN ('aberto', 'triagem') THEN 'em_andamento' ELSE status END,
            data_inicio_atendimento = COALESCE(data_inicio_atendimento, NOW())
        WHERE id_chamado = $id_chamado";

if ($conn->query($sql)) {
    if ($conn->affected_rows >= 0) {
        echo json_encode(["success" => true, "message" => "Chamado atualizado com sucesso."]);
    } else {
        echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Erro ao salvar: " . $conn->error]);
}
