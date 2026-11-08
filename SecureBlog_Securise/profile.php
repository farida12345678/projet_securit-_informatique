<?php
require_once 'includes/config.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// ============================================
// SÉCURISÉ : On récupère le profil demandé
// ============================================
$stmt = $pdo->prepare(
    "SELECT id, username, email, created_at 
     FROM users 
     WHERE id = :id"
);
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Utilisateur introuvable.'];
    header('Location: index.php');
    exit;
}

// ============================================
// PROTECTION IDOR :
// On n'affiche l'email QUE si c'est le propriétaire du profil
// ou si l'utilisateur n'est pas connecté (on peut aussi tout cacher)
// ============================================
$isOwner = isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$user['id'];

// Récupération des articles de l'utilisateur
$stmt = $pdo->prepare(
    "SELECT id, title, created_at 
     FROM posts 
     WHERE user_id = :user_id 
     ORDER BY created_at DESC"
);
$stmt->execute(['user_id' => $user_id]);
$posts = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="card">
    <h2>Profil de <?= htmlspecialchars($user['username']) ?></h2>

    <div class="profile-info" style="margin-top: 15px;">
        <p><strong>ID :</strong> <?= (int)$user['id'] ?></p>
        <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']) ?></p>

        <?php if ($isOwner): ?>
            <!-- L'email n'est visible que par le propriétaire -->
            <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <?php else: ?>
            <p><strong>Email :</strong> <em>Masqué (privé)</em></p>
        <?php endif; ?>

        <p><strong>Membre depuis :</strong> <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>
    </div>
</div>

<div class="card">
    <h3>Articles publiés</h3>
    <?php if (count($posts) > 0): ?>
        <ul style="list-style: none; padding: 0;">
            <?php foreach ($posts as $p): ?>
                <li style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                    <a href="post.php?id=<?= (int)$p['id'] ?>">
                        <?= htmlspecialchars($p['title']) ?>
                    </a>
                    <span class="meta"> – <?= date('d/m/Y', strtotime($p['created_at'])) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="meta">Aucun article publié.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>