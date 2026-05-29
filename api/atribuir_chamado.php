<?php
ob_start(); // Bufferiza TUDO, inclusive erros fatais do PHP
session_start();
require_once '../config/database.php';

// Converte warnings/notices em exceções
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});
// Captura erros fatais e retorna como JSON
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
    if (!isset($_SESSION['user_id']) || ($_SESSION['user_perfil'] !== 'gestor' && $_SESSION['user_perfil'] !== 'admin')) {
        echo json_encode(["success" => false, "message" => "Acesso negado."]);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || empty($data['id_chamado'])) {
        echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        exit;
    }

    $id_chamado = (int) $data['id_chamado'];
    $id_tecnico = isset($data['id_tecnico']) ? (int) $data['id_tecnico'] : 0;
    $prioridade = isset($data['prioridade']) ? trim($data['prioridade']) : 'media';
    $allowedPrior = ['baixa', 'media', 'alta', 'critica'];
    if (!in_array($prioridade, $allowedPrior, true)) {
        $prioridade = 'media';
    }

    if ($id_tecnico <= 0) {
        echo json_encode(["success" => false, "message" => "Selecione um técnico responsável."]);
        exit;
    }

    $prioridadeEsc = $conn->real_escape_string($prioridade);
    $rawPrevista = isset($data['data_prevista']) ? trim($data['data_prevista']) : '';
    if ($rawPrevista !== '') {
        $dtClean = str_replace('T', ' ', $rawPrevista);
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $dtClean)) {
            $dtClean .= ':00';
        }
        $dataPrevista = "'" . $conn->real_escape_string($dtClean) . "'";
    } else {
        $dataPrevista = 'NULL';
    }

    $sql = "UPDATE chamados SET
                id_tecnico = $id_tecnico,
                prioridade = '$prioridadeEsc',
                status = CASE WHEN status IN ('aberto', 'triagem') THEN 'em_andamento' ELSE status END,
                data_inicio_atendimento = COALESCE(data_inicio_atendimento, NOW()),
                data_previsao_conclusao = $dataPrevista
            WHERE id_chamado = $id_chamado";

    if ($conn->query($sql)) {
        if ($conn->affected_rows >= 0) {
            echo json_encode(["success" => true, "message" => "Chamado atualizado com sucesso."]);
        } else {
            echo json_encode(["success" => false, "message" => "Chamado não encontrado."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao salvar: " . $conn->error]);
    }
} catch (Throwable $e) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json', true);
    echo json_encode(["success" => false, "message" => "Exceção: " . $e->getMessage()]);
}
