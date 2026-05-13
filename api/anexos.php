<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}
$id_chamados = $_GET["id_chamado"];
if(!$id_chamados){
    echo json_encode(["success"=> false,"message"=> ""]);
    exit;
}

$sql = "SELECT chamados_anexos.caminho_arquivo, chamados_anexos.tipo_anexo, chamados.id_chamado from chamados_anexos 
    inner join chamados on chamados.id_chamado = chamados_anexos.id_chamado where chamados.id_chamado = $id_chamados";

        $res = $conn->query($sql);
        $dados = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode($dados);