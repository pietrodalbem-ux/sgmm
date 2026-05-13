<?php
// api/login.php - Versão Corrigida e Robusta
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Captura o input JSON
$dadosBrutos = file_get_contents("php://input");
$data = json_decode($dadosBrutos);

if (!$data || !isset($data->email) || !isset($data->senha)) {
    echo json_encode(["success" => false, "message" => "Preencha todos os campos corretamente."]);
    exit;
}

$email = $conn->real_escape_string(trim($data->email));
$senha = trim($data->senha);

// Busca o usuário
$sql = "SELECT id_usuario, nome, email, senha_hash, perfil, ativo 
        FROM usuarios 
        WHERE email = '$email' 
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ((int)$user['ativo'] !== 1) {
        echo json_encode(["success" => false, "message" => "Este usuário está inativo."]);
        exit;
    }

    // Verifica a senha
    if (password_verify($senha, $user['senha_hash'])) {
        // Salva TUDO na sessão para não dar erro nos outros arquivos
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['user_nome'] = $user['nome'];
        $_SESSION['user_perfil'] = $user['perfil']; // admin, gestor, tecnico ou solicitante
        $_SESSION['email'] = $user['email'];

        echo json_encode([
            "success" => true, 
            "message" => "Login realizado com sucesso!",
            "perfil" => $user['perfil']
        ]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Senha incorreta."]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Usuário não encontrado em nossa base."]);
    exit;
}
?>