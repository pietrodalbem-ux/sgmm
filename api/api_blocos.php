<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Segurança: Gestores e Admins
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $busca = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
        $where = "WHERE deleted_at IS NULL";
        if ($busca) {
            $where .= " AND (nome LIKE '%$busca%' OR descricao LIKE '%$busca%')";
        }
        $sql = "SELECT id_bloco, nome, descricao FROM blocos $where ORDER BY nome ASC";
        $result = $conn->query($sql);
        echo json_encode([
            "success" => true,
            "data" => $result ? $result->fetch_all(MYSQLI_ASSOC) : []
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->nome)) {
            echo json_encode(["success" => false, "message" => "O nome do bloco é obrigatório."]);
            exit;
        }
        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = $conn->real_escape_string(trim($data->descricao ?? ''));

        $sql = "INSERT INTO blocos (nome, descricao) VALUES ('$nome', '$descricao')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Bloco criado com sucesso.", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar bloco: " . $conn->error]);
        }
        break;
    
    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_bloco) || empty($data->nome)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }
        $id = (int)$data->id_bloco;
        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = $conn->real_escape_string(trim($data->descricao ?? ''));

        $sql = "UPDATE blocos SET nome = '$nome', descricao = '$descricao' WHERE id_bloco = $id AND deleted_at IS NULL";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Bloco atualizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_bloco)) {
            echo json_encode(["success" => false, "message" => "ID não informado."]);
            exit;
        }
        $id = (int)$data->id_bloco;

        // SOFT DELETE
        $sql = "UPDATE blocos SET deleted_at = NOW(), deleted_by = $user_id WHERE id_bloco = $id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Bloco movido para a lixeira."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método não suportado."]);
        break;
}