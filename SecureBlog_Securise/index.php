<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$posts  = [];

try {
    if ($search !== '') {
        // ============================================
        // SÉCURISÉ : Requête préparée (contre SQLi)
        // ============================================
        $stmt = $pdo->prepare(
            "SELECT p.*, u.username 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.title ILIKE :search OR p.content ILIKE :search
             ORDER BY p.created_at DESC"
        );
        $stmt->execute(['search' => '%' . $search . '%']);
        $posts = $stmt->fetchAll();
    } else {
        $stmt = $pdo->query(
            "SELECT p.*, u.username 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             ORDER BY p.created_at DESC"
        );
        $posts = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    echo '<div class="alert alert-error">Une erreur est survenue.</div>';
}
?>

<h1>Articles récents</h1>

<div class="search-box card">
    <form method="GET" action="index.php">
        <input type="text" name="q" placeholder="Rechercher un article..." 
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Rechercher</button>
    </form>
    <?php if ($search !== ''): ?>
        <p class="meta" style="margin-top:10px;">
            Résultats pour : <strong><?= htmlspecialchars($search) ?></strong>
        </p>
    <?php endif; ?>
</div>

<?php if (count($posts) > 0): ?>
    <?php foreach ($posts as $post): ?>
        <article class="card">
            <h2>
                <a href="post.php?id=<?= (int)$post['id'] ?>">
                    <?= htmlspecialchars($post['title']) ?>
                </a>
            </h2>
            <p class="meta">
                Par 
                <a href="profile.php?id=<?= (int)$post['user_id'] ?>">
                    <?= htmlspecialchars($post['username']) ?>
                </a>
                • <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?>
            </p>
            <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
            <a href="post.php?id=<?= (int)$post['id'] ?>" class="btn">Lire la suite</a>
        </article>
    <?php endforeach; ?>
<?php else: ?>
    <div class="card">
        <p>Aucun article trouvé.</p>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>