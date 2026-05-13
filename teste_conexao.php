<?php
require_once "config/database.php";

if ($conn) {
    echo "<h1>✅ Conexão estabelecida com sucesso!</h1>";
    
    // Testa se a tabela de usuários existe e tem dados
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total de usuários encontrados no banco: <strong>" . $row['total'] . "</strong></p>";
    } else {
        echo "<p style='color:red'>❌ Erro ao acessar a tabela 'usuarios': " . $conn->error . "</p>";
    }
}
?>
