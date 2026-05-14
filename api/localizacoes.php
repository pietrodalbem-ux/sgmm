<?php
// api/localizacoes.php
require_once "../config/database.php";
header('Content-Type: application/json');

$acao = $_GET['acao'] ?? '';

// Todas as consultas respeitam o Soft Delete (Backend Real)
if ($acao == 'listar_blocos') {
    $res = $conn->query("SELECT id_bloco, nome FROM blocos WHERE deleted_at IS NULL ORDER BY nome ASC");
    echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
} else if ($acao == 'listar_ambientes') {
    $id_bloco = (int)($_GET['id_bloco'] ?? 0);
    $stmt = $conn->prepare("SELECT id_ambiente, nome FROM ambientes WHERE id_bloco = ? AND deleted_at IS NULL ORDER BY nome ASC");
    $stmt->bind_param("i", $id_bloco);
    $stmt->execute();
    echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
} else if ($acao == 'listar_tipos') {
    $res = $conn->query("SELECT id_tipo_servico as id_tipo, nome FROM tipo_servico WHERE deleted_at IS NULL ORDER BY nome ASC");
    echo json_encode($res ? $res->fetch_all(MYSQLI_ASSOC) : []);
}
?>