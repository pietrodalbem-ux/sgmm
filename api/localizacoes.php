<?php
// api/localizacoes.php
require_once "../config/database.php";
header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

if ($acao == 'listar_blocos') {
    $res = $conn->query("SELECT id_bloco, nome FROM blocos");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
} else if ($acao == 'listar_ambientes') {
    $id_bloco = (int)($_GET['id_bloco'] ?? 0);
    $stmt = $conn->prepare("SELECT id_ambiente, nome FROM ambientes WHERE id_bloco = ?");
    $stmt->bind_param("i", $id_bloco);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
} else if ($acao == 'listar_tipos') {
    // Ajustado para o novo banco de dados: tabela 'tipo_servico' e coluna 'id_tipo_servico'
    $res = $conn->query("SELECT id_tipo_servico as id_tipo, nome FROM tipo_servico");
    echo json_encode($res->fetch_all(MYSQLI_ASSOC));
}
?>