<?php
require_once __DIR__.'/config/database.php';
$email = 'admin@senai.br';
$sql = "SELECT id_usuario, nome, senha_hash, perfil, ativo FROM usuarios WHERE email=? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if($row = $res->fetch_assoc()){
    echo "Usuario encontrado: {$row['nome']} (perfil: {$row['perfil']})\n";
    $hash = $row['senha_hash'];
    echo "Hash: $hash\n";
    $ok = password_verify('123', $hash) ? 'OK' : 'FAIL';
    echo "Verificacao senha 123: $ok\n";
} else {
    echo "Usuario não encontrado\n";
}
?>
