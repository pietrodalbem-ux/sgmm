<?php
session_start(); // Session start deve vir antes de quase tudo
require_once '../config/database.php'; // Verifique se a pasta é models ou config!
header('Content-Type: application/json');   

if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso Negado"]);
    exit;
}

$sql = "SELECT id_usuario, nome FROM usuarios WHERE perfil = 'tecnico' AND ativo = 1 ORDER BY nome ASC";
$result = $conn->query($sql);

if ($result) {
    $tecnicos = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($tecnicos);
} else {
    echo json_encode([]); // Retorna array vazio se der erro na query
}
exit;