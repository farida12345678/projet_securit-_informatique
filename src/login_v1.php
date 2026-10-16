<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db.php';

/** @var PDO $pdo */ // <-- Cette ligne aide VS Code à comprendre que $pdo vient de PDO

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    
    try {
        $result = $pdo->query($sql);
        $row = $result->fetch();

        if ($row) {
            $_SESSION['user'] = $row['username'];
            header("Location: index.php");
            exit();
        } else {
            $message = "Identifiants incorrects.";
        }
    } catch (PDOException $e) {
        $message = "Erreur SQL : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Mini Blog</title>
</head>
<body>
    <h2>Connexion</h2>
    <?php if (!empty($message)) { echo "<p style='color:red;'>$message</p>"; } ?>
    <form method="POST" action="">
        <label>Nom d'utilisateur :</label><br>
        <input type="text" name="username" required><br><br>
        <label>Mot de passe :</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>