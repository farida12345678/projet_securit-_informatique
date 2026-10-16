<?php
session_start();
include 'db.php';

// Redirection vers le login si non connecté
if (!isset($_SESSION['user'])) {
    header("Location: login_v1.php");
    exit();
}

$message_error = "";

// Gestion de l'ajout d'un commentaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['content'])) {
    $content = $_POST['content']; // VULNERABILITE XSS : stocké tel quel sans échappement
    $username = $_SESSION['user'];

    // Récupérer l'id de l'utilisateur connecté
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        $user_id = $user['id'];
        // Insertion dans la table comments
        $insert = $pdo->prepare("INSERT INTO comments (user_id, content) VALUES (?, ?)");
        $insert->execute([$user_id, $content]);
        header("Location: index_v1.php");
        exit();
    }
}

// Récupération de tous les commentaires avec le nom des auteurs
$sql = "SELECT comments.content, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.id DESC";
$comments = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mini-Blog - Version Vulnérable</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        textarea { width: 100%; height: 80px; padding: 8px; margin: 10px 0; }
        button { background: #3498db; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
        button:hover { background: #2980b9; }
        .comment { background: #ecf0f1; padding: 10px; margin: 10px 0; border-radius: 4px; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .logout { color: #e74c3c; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['user']); ?> !</h2>
            <a href="login.php" class="logout">Déconnexion</a>
        </div>

        <h3>Laisser un commentaire (Espace vulnérable XSS)</h3>
        <form method="POST">
            <textarea name="content" placeholder="Écrivez votre commentaire ici..." required></textarea><br>
            <button type="submit">Publier</button>
        </form>

        <h3>Flux des commentaires</h3>
        <hr>
        <?php if (empty($comments)): ?>
            <p>Aucun commentaire pour le moment.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <strong><?php echo htmlspecialchars($comment['username']); ?> :</strong>
                    <!-- VULNERABILITE XSS : Affichage brut sans htmlspecialchars -->
                    <p><?php echo $comment['content']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>