<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Tous les champs sont obligatoires.';
    } else {
        // ============================================
        // SÉCURISÉ : Requête préparée (contre SQLi)
        // ============================================
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Vérification du mot de passe avec password_verify()
        // (pour l'instant les mots de passe en base sont encore en clair,
        //  on les mettra à jour juste après)
        if ($user && (
            password_verify($password, $user['password'])   // version hashée
            || $password === $user['password']              // temporaire (compatibilité)
        )) {
            // Régénération de l'ID de session (contre Session Fixation)
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Bienvenue ' . htmlspecialchars($user['username']) . ' !'
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 420px; margin: 40px auto;">
    <h2>Connexion (Version Sécurisée)</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" id="username" name="username" required autofocus>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Se connecter</button>
    </form>

    <p style="margin-top: 15px; font-size: 0.9rem;">
        Pas encore de compte ? <a href="register.php">S'inscrire</a>
    </p>

    <div class="alert alert-info" style="margin-top: 20px; font-size: 0.85rem;">
        <strong>Comptes de test :</strong><br>
        admin / admin123<br>
        alice / alice2024<br>
        bob / password
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>