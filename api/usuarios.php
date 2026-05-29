<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Segurança: Apenas Gestores e Admins podem gerenciar usuários
if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso Negado."]);
    exit;
}

$user_id_sessao = $_SESSION['user_id'];
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $acao = $_GET['acao'] ?? '';
        $busca = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($acao === 'listar_departamentos') {
            $sql = "SELECT id_departamento, nome FROM departamentos WHERE deleted_at IS NULL ORDER BY nome ASC";
            $result = $conn->query($sql);
            echo json_encode(["success" => true, "data" => $result ? $result->fetch_all(MYSQLI_ASSOC) : []]);
            break;
        }

        if ($id > 0) {
            $where = "WHERE u.id_usuario = $id AND u.deleted_at IS NULL";
        } else {
            $where = "WHERE u.deleted_at IS NULL";
            if ($busca) {
                $where .= " AND (u.nome LIKE '%$busca%' OR u.email LIKE '%$busca%' OR u.cpf LIKE '%$busca%' OR d.nome LIKE '%$busca%')";
            }
            $perfilFiltro = isset($_GET['perfil']) ? $conn->real_escape_string($_GET['perfil']) : '';
            if ($perfilFiltro) {
                $where .= " AND u.perfil = '$perfilFiltro'";
            }
        }

        $sql = "SELECT u.id_usuario, u.nome, u.email, u.cpf, u.telefone, u.perfil, u.ativo, u.id_departamento, d.nome as departamento_nome 
                FROM usuarios u 
                LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento 
                $where
                ORDER BY u.nome ASC";
        
        $result = $conn->query($sql);
        echo json_encode(["success" => true, "data" => $result ? $result->fetch_all(MYSQLI_ASSOC) : []]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->nome) || empty($data->email) || empty($data->senha) || empty($data->perfil)) {
            echo json_encode(["success" => false, "message" => "Dados obrigatórios faltando."]);
            exit;
        }

        $nome = $conn->real_escape_string(trim($data->nome));
        $email = $conn->real_escape_string(trim($data->email));
        $cpf = !empty($data->cpf) ? $conn->real_escape_string(trim($data->cpf)) : null;
        $telefone = !empty($data->telefone) ? $conn->real_escape_string(trim($data->telefone)) : null;
        $perfil = $conn->real_escape_string($data->perfil);
        $id_dept = !empty($data->id_departamento) ? (int)$data->id_departamento : 'NULL';
        $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);

        // Validar se email já existe
        $check = $conn->query("SELECT id_usuario FROM usuarios WHERE email = '$email'");
        if ($check->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Este e-mail já está cadastrado no sistema."]);
            exit;
        }

        $sql = "INSERT INTO usuarios (nome, email, cpf, telefone, senha_hash, perfil, id_departamento, ativo) 
                VALUES ('$nome', '$email', " . ($cpf ? "'$cpf'" : "NULL") . ", " . ($telefone ? "'$telefone'" : "NULL") . ", '$senha_hash', '$perfil', $id_dept, 1)";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Usuário criado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao criar: " . $conn->error]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_usuario)) {
            echo json_encode(["success" => false, "message" => "ID não informado."]);
            exit;
        }

        $id = (int)$data->id_usuario;
        $sets = [];

        if (!empty($data->nome)) {
            $nome = $conn->real_escape_string(trim($data->nome));
            $sets[] = "nome = '$nome'";
        }
        if (!empty($data->email)) {
            $email = $conn->real_escape_string(trim($data->email));
            $check = $conn->query("SELECT id_usuario FROM usuarios WHERE email = '$email' AND id_usuario != $id");
            if ($check->num_rows > 0) {
                echo json_encode(["success" => false, "message" => "Este e-mail já pertence a outro usuário."]);
                exit;
            }
            $sets[] = "email = '$email'";
        }
        if (isset($data->cpf)) {
            $cpf = !empty($data->cpf) ? "'" . $conn->real_escape_string(trim($data->cpf)) . "'" : "NULL";
            $sets[] = "cpf = $cpf";
        }
        if (isset($data->telefone)) {
            $telefone = !empty($data->telefone) ? "'" . $conn->real_escape_string(trim($data->telefone)) . "'" : "NULL";
            $sets[] = "telefone = $telefone";
        }
        if (!empty($data->perfil)) {
            $perfil = $conn->real_escape_string($data->perfil);
            $sets[] = "perfil = '$perfil'";
        }
        if (isset($data->id_departamento)) {
            $id_dept = !empty($data->id_departamento) ? (int)$data->id_departamento : 'NULL';
            $sets[] = "id_departamento = $id_dept";
        }
        if (isset($data->ativo)) {
            $ativo = (int)$data->ativo;
            $sets[] = "ativo = $ativo";
        }
        if (!empty($data->senha)) {
            $senha_hash = password_hash($data->senha, PASSWORD_DEFAULT);
            $sets[] = "senha_hash = '$senha_hash'";
        }

        if (empty($sets)) {
            echo json_encode(["success" => false, "message" => "Nenhum campo para atualizar."]);
            exit;
        }

        $sql = "UPDATE usuarios SET " . implode(', ', $sets) . " WHERE id_usuario = $id AND deleted_at IS NULL";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Dados atualizados com sucesso."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
        }
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->id_usuario)) {
            echo json_encode(["success" => false, "message" => "ID não informado."]);
            exit;
        }
        $id = (int)$data->id_usuario;

        if ($id === (int)$_SESSION['user_id']) {
            echo json_encode(["success" => false, "message" => "Você não pode excluir sua própria conta."]);
            exit;
        }

        // SOFT DELETE
        $sql = "UPDATE usuarios SET deleted_at = NOW(), deleted_by = $user_id_sessao, ativo = 0 WHERE id_usuario = $id";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => "Usuário movido para a lixeira."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $conn->error]);
        }
        break;
}