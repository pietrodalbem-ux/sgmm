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
        $busca = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
        $where = "WHERE a.deleted_at IS NULL AND b.deleted_at IS NULL";
        if ($busca) {
            $where .= " AND (a.nome LIKE '%$busca%' OR b.nome LIKE '%$busca%')";
        }
        $sql = "SELECT a.id_ambiente, a.nome, a.id_bloco, b.nome as bloco_nome 
                FROM ambientes a
                JOIN blocos b ON a.id_bloco = b.id_bloco
                $where
                ORDER BY b.nome ASC, a.nome ASC";
        $result = $conn->query($sql);
        echo json_encode([
            "success" => true,
            "data" => $result ? $result->fetch_all(MYSQLI_ASSOC) : []
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->nome) || empty($data->id_bloco)) {
            echo json_encode(["success" => false, "message" => "Dados obrigatórios faltando."]);
            exit;
        }
        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int)$data->id_bloco;

        $sql = "INSERT INTO ambientes (id_bloco, nome) VALUES ($id_bloco, '$nome')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Ambiente criado.", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_ambiente) || empty($data->nome)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }
        $id = (int)$data->id_ambiente;
        $id_bloco = (int)$data->id_bloco;
        $nome = $conn->real_escape_string(trim($data->nome));

        $sql = "UPDATE ambientes SET nome = '$nome', id_bloco = $id_bloco WHERE id_ambiente = $id AND deleted_at IS NULL";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Ambiente atualizado."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_ambiente)) {
            echo json_encode(["success" => false, "message" => "ID não informado."]);
            exit;
        }
        $id = (int)$data->id_ambiente;

        $sql = "UPDATE ambientes SET deleted_at = NOW(), deleted_by = $user_id WHERE id_ambiente = $id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Ambiente movido para a lixeira."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;
}