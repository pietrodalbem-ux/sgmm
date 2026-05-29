<?php
ob_start();
session_start();
require_once '../config/database.php';

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json', true);
        echo json_encode(["success" => false, "message" => "Erro interno: " . $error['message']]);
    }
});

header('Content-Type: application/json');

try {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "Acesso negado."]);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id_chamado'])) {
        echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        exit;
    }

    $id_chamado = (int) $data['id_chamado'];
    $uid = (int) $_SESSION['user_id'];
    $perfil = $_SESSION['user_perfil'] ?? '';

    // Verificar permissão: técnico designado, gestor ou admin
    if ($perfil === 'tecnico') {
        $check = $conn->query("SELECT id_tecnico FROM chamados WHERE id_chamado = $id_chamado AND deleted_at IS NULL");
        if (!$check || $check->num_rows === 0) {
            echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
            exit;
        }
        $row = $check->fetch_assoc();
        if ((int)($row['id_tecnico'] ?? 0) !== $uid) {
            echo json_encode(["success" => false, "message" => "Este chamado não está designado a você."]);
            exit;
        }
    } elseif (!in_array($perfil, ['gestor', 'admin'], true)) {
        echo json_encode(["success" => false, "message" => "Acesso negado."]);
        exit;
    }

    $feedback = isset($data['feedback']) ? $conn->real_escape_string(trim($data['feedback'])) : '';

    $sql = "UPDATE chamados SET
                status = 'concluido',
                data_conclusao = NOW(),
                feedback_solicitante = " . ($feedback ? "'$feedback'" : "feedback_solicitante") . "
            WHERE id_chamado = $id_chamado AND deleted_at IS NULL";

    if ($conn->query($sql)) {
        echo json_encode(["success" => true, "message" => "Chamado #$id_chamado concluído com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao concluir: " . $conn->error]);
    }
} catch (Throwable $e) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json', true);
    echo json_encode(["success" => false, "message" => "Exceção: " . $e->getMessage()]);
}
