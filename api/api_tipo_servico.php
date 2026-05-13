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
        $sql = "SELECT id_tipo_servico as id_tipo, nome, descricao FROM tipo_servico";
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
        if (!isset($data->nome) || !isset($data->descricao)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }
        $nome = $conn->real_escape_string($data->nome);
        $descricao = $conn->real_escape_string($data->descricao);
        $sql = "INSERT INTO tipo_servico (nome, descricao) VALUES ('$nome', '$descricao')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Tipo de serviço criado com sucesso.", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro: " . $conn->error]);
        }
        break;
    
    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_tipo) || !isset($data->nome) || !isset($data->descricao)) {
            echo json_encode(["success" => false, "message"=> "Dados incompletos."]);
            exit;
        }
        $id_tipo = (int) $data->id_tipo;
        $nome = $conn->real_escape_string(trim($data->nome));
        $descricao = $conn->real_escape_string($data->descricao);
        $sql = "UPDATE tipo_servico SET nome = '$nome', descricao = '$descricao' WHERE id_tipo_servico = $id_tipo";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success"=> true, "message" => "Atualizado com sucesso."]);
        } else {
            echo json_encode(["success"=> false, "message"=> "Erro: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->id_tipo)) {
            echo json_encode(["success" => false, "message"=> "Informe o ID."]);
            exit;
        }
        $id_tipo = (int) $data->id_tipo;
        $sql = "DELETE FROM tipo_servico WHERE id_tipo_servico = $id_tipo";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success"=> true, "message" => "Deletado com sucesso."]);
        } else {
            echo json_encode(["success"=> false, "message"=> "Erro: " . $conn->error]);
        }
        break;

    default:
        echo json_encode(["success"=> false,"message"=> "Método não suportado"]);
        break;
}
?>