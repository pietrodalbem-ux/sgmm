<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$sql = "SELECT 
            SUM(CASE WHEN status IN ('aberto', 'triagem') THEN 1 ELSE 0 END) AS aguardando_triagem,
            SUM(CASE WHEN status IN ('em_andamento', 'aguardando_peca') THEN 1 ELSE 0 END) AS em_atendimento,
            SUM(CASE WHEN status = 'concluido' AND DATE(COALESCE(data_conclusao, data_abertura)) = CURDATE() THEN 1 ELSE 0 END) AS concluidos_hoje,
            SUM(CASE WHEN prioridade = 'critica' AND status NOT IN ('concluido', 'cancelado') THEN 1 ELSE 0 END) AS criticos_urgentes,
            COUNT(*) AS total
        FROM chamados";

$res = $conn->query($sql);
if (!$res) {
    echo json_encode([
        "success" => false,
        "message" => $conn->error,
        "aguardando_triagem" => 0,
        "em_atendimento" => 0,
        "concluidos_hoje" => 0,
        "criticos_urgentes" => 0,
        "total" => 0
    ]);
    exit;
}

$dados = $res->fetch_assoc();
foreach (['aguardando_triagem', 'em_atendimento', 'concluidos_hoje', 'criticos_urgentes', 'total'] as $k) {
    $dados[$k] = (int)($dados[$k] ?? 0);
}
$dados['success'] = true;
echo json_encode($dados);
