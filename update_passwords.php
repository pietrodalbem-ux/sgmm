<?php
// update_passwords.php – Atualiza a senha de todos os usuários para '123'
// Executar acessando http://localhost/2025/projetos_senai/sgm-g/update_passwords.php

require_once '../config/database.php';

// Gerar novo hash (bcrypt) para a senha '123'
$newHash = password_hash('123', PASSWORD_BCRYPT);

// Atualiza todas as linhas da tabela usuarios
$sql = "UPDATE usuarios SET senha_hash = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Erro ao preparar a query: ' . $conn->error);
}
$stmt->bind_param('s', $newHash);
if ($stmt->execute()) {
    echo "Senhas atualizadas com sucesso. Novo hash usado: $newHash";
} else {
    echo "Falha ao atualizar senhas: " . $stmt->error;
}
?>
