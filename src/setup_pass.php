<?php
require_once 'db.php';

$username = 'admin';
$plain_password = 'secret123';

// Hachage robuste du mot de passe
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
$stmt->execute([$username, $hashed_password]);

echo "Utilisateur créé avec un mot de passe haché sécurisé !";
?>