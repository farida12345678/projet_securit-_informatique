<?php
require_once 'includes/config.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    try {
        $stmt = $pdo->query($sql);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Bienvenue ' . $user['username'] . ' !'
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Identifiants incorrects.';
        }
    } catch (PDOException $e) {
        $error = 'Erreur SQL : ' . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<div class="card" style="max-width: 420px; margin: 40px auto;">
    <h2>Connexion</h2>

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

    
</div>

<?php require_once 'includes/footer.php'; ?>