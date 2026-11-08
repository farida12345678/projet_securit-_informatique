<?php
require_once 'includes/config.php';

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Vous devez être connecté pour publier.'];
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $error = 'Titre et contenu obligatoires.';
    } else {
        // ============================================
        // SÉCURISÉ : Requête préparée
        // ============================================
        $stmt = $pdo->prepare(
            "INSERT INTO posts (user_id, title, content) 
             VALUES (:user_id, :title, :content)"
        );

        try {
            $stmt->execute([
                'user_id' => $_SESSION['user_id'],
                'title'   => $title,
                'content' => $content
            ]);

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Article publié avec succès !'
            ];
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Erreur lors de la publication.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="card">
    <h2>Nouvel article</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="new_post.php">
        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="content">Contenu</label>
            <textarea id="content" name="content" required></textarea>
        </div>
        <button type="submit">Publier</button>
        <a href="index.php" class="btn" style="background:#718096; margin-left:8px;">Annuler</a>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>