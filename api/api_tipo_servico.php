<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $sql = "SELECT id_tipo_servico, nome, descricao FROM tipo_servico WHERE deleted_at IS NULL ORDER BY nome ASC";
        $result = $conn->query($sql);
        echo json_encode([
            "success" => true,
            "data" => $result ? $result->fetch_all(MYSQLI_ASSOC) : []
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->nome)) {
            echo json_encode(["success" => false, "message" => "Nome é obrigatório."]);
            exit;
        }
        $nome = $conn->real_escape_string(trim($data->nome));
        $desc = $conn->real_escape_string(trim($data->descricao ?? ''));

        $sql = "INSERT INTO tipo_servico (nome, descricao) VALUES ('$nome', '$desc')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Tipo de serviço criado.", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_tipo_servico) || empty($data->nome)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }
        $id = (int)$data->id_tipo_servico;
        $nome = $conn->real_escape_string(trim($data->nome));
        $desc = $conn->real_escape_string(trim($data->descricao ?? ''));

        $sql = "UPDATE tipo_servico SET nome = '$nome', descricao = '$desc' WHERE id_tipo_servico = $id AND deleted_at IS NULL";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Tipo de serviço atualizado."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_tipo_servico)) {
            echo json_encode(["success" => false, "message" => "ID não informado."]);
            exit;
        }
        $id = (int)$data->id_tipo_servico;

        $sql = "UPDATE tipo_servico SET deleted_at = NOW(), deleted_by = $user_id WHERE id_tipo_servico = $id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Tipo de serviço movido para a lixeira."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;
}