<?php
// generate_hash.php – gera hash bcrypt para a senha "123"
$hash = password_hash('123', PASSWORD_BCRYPT);
echo $hash, PHP_EOL;
?>
