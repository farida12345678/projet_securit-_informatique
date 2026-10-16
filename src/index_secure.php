<?php
session_start();
require_once 'db.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: login_secure.php');
    exit();
}

// Traitement de l'ajout d'un commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, content) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $content]);
        header('Location: index_secure.php');
        exit();
    }
}

// Récupération des commentaires avec le nom des utilisateurs
$query = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id ORDER BY comments.created_at DESC";
$stmt = $pdo->query($query);
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mini-blog - Accueil (Sécurisé)</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 600px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .logout { color: #e74c3c; text-decoration: none; font-size: 14px; }
        textarea { width: 100%; height: 80px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical; }
        button { background-color: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #2980b9; }
        .comment-box { background: #f9f9f9; border-left: 4px solid #27ae60; padding: 10px 15px; margin-top: 15px; border-radius: 4px; }
        .comment-meta { font-size: 12px; color: #7f8c8d; margin-bottom: 5px; }
        .badge { background: #27ae60; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h2>Bienvenue, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?> !</h2>
                <span class="badge">Version V2 - Sécurisée</span>
            </div>
            <a href="login.php?logout=1" class="logout">Déconnexion</a>
        </div>

        <h3>Laisser un commentaire (Espace sécurisé contre XSS)</h3>
        <form method="POST" action="">
            <div class="form-group">
                <textarea name="content" placeholder="Écrivez votre commentaire ici..." required></textarea>
            </div>
            <button type="submit">Publier</button>
        </form>

        <hr style="margin: 25px 0; border: 0; border-top: 1px solid #eee;">

        <h3>Flux des commentaires</h3>
        <?php if (empty($comments)): ?>
            <p style="color: #7f8c8d;">Aucun commentaire pour le moment.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment-box">
                    <div class="comment-meta">
                        <strong><?= htmlspecialchars($comment['username'], ENT_QUOTES, 'UTF-8'); ?></strong> 
                        le <?= $comment['created_at']; ?>
                    </div>
                    <!-- Protection XSS grâce à htmlspecialchars -->
                    <p><?= htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>