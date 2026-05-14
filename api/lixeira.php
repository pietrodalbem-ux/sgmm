<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');

// Proteção: Apenas Administradores têm acesso total à lixeira
if (!isset($_SESSION['user_id']) || $_SESSION['user_perfil'] !== 'admin') {
    echo json_encode(["success" => false, "message" => "Acesso restrito a administradores."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER["REQUEST_METHOD"];
$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

switch ($method) {
    case "GET":
        if ($acao === 'listar') {
            $data = [
                'usuarios' => [],
                'blocos' => [],
                'ambientes' => [],
                'tipos_servico' => []
            ];

            // Listar usuários excluídos
            $sql = "SELECT u.id_usuario, u.nome, u.email, u.deleted_at, ad.nome as excluido_por 
                    FROM usuarios u 
                    LEFT JOIN usuarios ad ON u.deleted_by = ad.id_usuario 
                    WHERE u.deleted_at IS NOT NULL";
            $res = $conn->query($sql);
            if ($res) $data['usuarios'] = $res->fetch_all(MYSQLI_ASSOC);

            // Listar blocos excluídos
            $sql = "SELECT b.id_bloco, b.nome, b.deleted_at, ad.nome as excluido_por 
                    FROM blocos b 
                    LEFT JOIN usuarios ad ON b.deleted_by = ad.id_usuario 
                    WHERE b.deleted_at IS NOT NULL";
            $res = $conn->query($sql);
            if ($res) $data['blocos'] = $res->fetch_all(MYSQLI_ASSOC);

            // Listar ambientes excluídos
            $sql = "SELECT a.id_ambiente, a.nome, a.deleted_at, ad.nome as excluido_por 
                    FROM ambientes a 
                    LEFT JOIN usuarios ad ON a.deleted_by = ad.id_usuario 
                    WHERE a.deleted_at IS NOT NULL";
            $res = $conn->query($sql);
            if ($res) $data['ambientes'] = $res->fetch_all(MYSQLI_ASSOC);

            // Listar tipos de serviço excluídos
            $sql = "SELECT t.id_tipo_servico, t.nome, t.deleted_at, ad.nome as excluido_por 
                    FROM tipo_servico t 
                    LEFT JOIN usuarios ad ON t.deleted_by = ad.id_usuario 
                    WHERE t.deleted_at IS NOT NULL";
            $res = $conn->query($sql);
            if ($res) $data['tipos_servico'] = $res->fetch_all(MYSQLI_ASSOC);

            echo json_encode(["success" => true, "data" => $data]);
        }
        break;

    case "POST":
        // Restaurar ou excluir permanentemente
        $input = json_decode(file_get_contents("php://input"));
        if (!isset($input->tabela) || !isset($input->id) || !isset($input->operacao)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }

        $tabela = $conn->real_escape_string($input->tabela);
        $id = (int)$input->id;
        $id_col = "";

        // Validar nome da tabela e definir coluna ID
        switch ($tabela) {
            case 'usuarios': $id_col = 'id_usuario'; break;
            case 'blocos': $id_col = 'id_bloco'; break;
            case 'ambientes': $id_col = 'id_ambiente'; break;
            case 'tipo_servico': $id_col = 'id_tipo_servico'; break;
            default:
                echo json_encode(["success" => false, "message" => "Tabela inválida."]);
                exit;
        }

        if ($input->operacao === 'restaurar') {
            $sql = "UPDATE $tabela SET deleted_at = NULL, deleted_by = NULL WHERE $id_col = $id";
            $msg = "Item restaurado com sucesso.";
        } elseif ($input->operacao === 'limpar') {
            $sql = "DELETE FROM $tabela WHERE $id_col = $id AND deleted_at IS NOT NULL";
            $msg = "Item excluído permanentemente.";
        } else {
            echo json_encode(["success" => false, "message" => "Operação inválida."]);
            exit;
        }

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "message" => $msg]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao processar: " . $conn->error]);
        }
        break;
}
