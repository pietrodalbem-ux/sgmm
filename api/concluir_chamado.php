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

    $uid = (int) $_SESSION['user_id'];
    $perfil = $_SESSION['user_perfil'] ?? '';

    // Determine if request is JSON or FormData
    $isJson = strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false;
    
    if ($isJson) {
        $data = json_decode(file_get_contents('php://input'), true);
        $id_chamado = (int) ($data['id_chamado'] ?? 0);
        $feedback = isset($data['feedback']) ? $conn->real_escape_string(trim($data['feedback'])) : '';
        $data_conclusao = ''; // Will use NOW()
        $has_photo = false;
    } else {
        $id_chamado = (int) ($_POST['id_chamado'] ?? 0);
        $feedback = isset($_POST['feedback']) ? $conn->real_escape_string(trim($_POST['feedback'])) : '';
        $data_conclusao = isset($_POST['data_conclusao']) ? $conn->real_escape_string(trim($_POST['data_conclusao'])) : '';
        $has_photo = isset($_FILES['foto_conclusao']) && $_FILES['foto_conclusao']['error'] === UPLOAD_ERR_OK;
        
        if ($perfil === 'tecnico' && (!$data_conclusao || !$has_photo)) {
            echo json_encode(["success" => false, "message" => "Data de conclusão e foto de evidência são obrigatórias."]);
            exit;
        }
    }

    if (!$id_chamado) {
        echo json_encode(["success" => false, "message" => "Dados incompletos."]);
        exit;
    }

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

    $dateSql = $data_conclusao ? "'$data_conclusao'" : "NOW()";
    $sql = "UPDATE chamados SET
                status = 'concluido',
                data_conclusao = $dateSql,
                feedback_solicitante = " . ($feedback ? "'$feedback'" : "feedback_solicitante") . "
            WHERE id_chamado = $id_chamado AND deleted_at IS NULL";

    if ($conn->query($sql)) {
        if (!$isJson && $has_photo) {
        $diretorio = "../assets/uploads/";
        if(!is_dir($diretorio)) mkdir($diretorio, 0777, true);
        
        $extensao = strtolower(pathinfo($_FILES['foto_conclusao']['name'], PATHINFO_EXTENSION));
        $nome_arquivo = "conclusao_" . uniqid() . "." . $extensao;
        $caminho_final = $diretorio . $nome_arquivo;
        
        if(move_uploaded_file($_FILES['foto_conclusao']['tmp_name'], $caminho_final)){
            $caminho_db = "assets/uploads/" . $nome_arquivo;
            $conn->query("INSERT INTO chamados_anexos (id_chamado, caminho_arquivo, tipo_anexo) VALUES ($id_chamado, '$caminho_db', 'conclusao')");
        }
        }
        
        echo json_encode(["success" => true, "message" => "Chamado #$id_chamado concluído com sucesso!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao concluir: " . $conn->error]);
    }
} catch (Throwable $e) {
    if (ob_get_level()) ob_clean();
    header('Content-Type: application/json', true);
    echo json_encode(["success" => false, "message" => "Exceção: " . $e->getMessage()]);
}
