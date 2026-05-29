<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}
$id_chamado = isset($_GET['id_chamado']) ? (int)$_GET['id_chamado'] : 0;
if ($id_chamado <= 0) {
    echo json_encode(["success" => false, "message" => "Parâmetro inválido."]);
    exit;
}

$sql = "SELECT chamados_anexos.caminho_arquivo, chamados_anexos.tipo_anexo, chamados.id_chamado FROM chamados_anexos
    INNER JOIN chamados ON chamados.id_chamado = chamados_anexos.id_chamado WHERE chamados.id_chamado = $id_chamado";

$res = $conn->query($sql);
$dados = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
echo json_encode($dados);