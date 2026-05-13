<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Gestores ou Admins
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] != 'gestor' && $_SESSION['user_perfil'] != 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $sql = "SELECT a.id_ambiente, a.nome, a.id_bloco, b.nome as nome_bloco 
                FROM ambientes a 
                LEFT JOIN blocos b ON a.id_bloco = b.id_bloco";
        $result = $conn->query($sql);
        $dados = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $dados[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $dados]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->nome) || !isset($data->id_bloco)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }
        $nome = $conn->real_escape_string($data->nome);
        $id_bloco = (int) $data->id_bloco;
        $sql = "INSERT INTO ambientes (nome, id_bloco) VALUES ('$nome', $id_bloco)";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Sucesso!", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;
    
    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_ambiente) || !isset($data->nome) || !isset($data->id_bloco)) {
            echo json_encode(["success" => false, "message"=> "Dados incompletos."]);
            exit;
        }
        $id_ambiente = (int) $data->id_ambiente;
        $nome = $conn->real_escape_string(trim($data->nome));
        $id_bloco = (int) $data->id_bloco;
        $sql = "UPDATE ambientes SET nome = '$nome', id_bloco = $id_bloco WHERE id_ambiente = $id_ambiente";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success"=> true, "message" => "Sucesso!"]);
        } else {
            echo json_encode(["success"=> false, "message"=> "Erro: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_ambiente)) {
            echo json_encode(["success" => false, "message"=> "Informe o ID."]);
            exit;
        }
        $id_ambiente = (int) $data->id_ambiente;
        $sql = "DELETE FROM ambientes WHERE id_ambiente = $id_ambiente";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success"=> true, "message" => "Sucesso!"]);
        } else {
            echo json_encode(["success"=> false, "message"=> "Erro: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success"=> false,"message"=> "Método não suportado"]);
        break;
}
?>