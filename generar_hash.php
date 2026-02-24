<?php
// Este archivo lo ejecutas una sola vez para generar los hashes correctos
echo "Hash para 'admin123': " . password_hash('admin123', PASSWORD_DEFAULT) . "\n";
echo "Hash para 'personal123': " . password_hash('personal123', PASSWORD_DEFAULT) . "\n";
?>