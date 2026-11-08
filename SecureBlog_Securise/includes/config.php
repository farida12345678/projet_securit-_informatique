<?php
/**
 * SecureBlog - Version SÉCURISÉE
 * Configuration PostgreSQL + sécurité de base
 */

// ======================
// Configuration de session sécurisée
// ======================
ini_set('session.cookie_httponly', 1);     // Empêche l'accès au cookie via JavaScript
ini_set('session.cookie_secure', 0);       // Mettre à 1 si tu es en HTTPS
ini_set('session.use_strict_mode', 1);     // Empêche l'utilisation d'IDs de session non générés
ini_set('session.cookie_samesite', 'Lax'); // Protection CSRF basique

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ======================
// Connexion PostgreSQL
// ======================
$host = 'db'; // ou 'localhost' selon votre configuration Docker/locale
$port = '5432';
$name = 'secureblog_vuln'; // Forcer explicitement la bonne base
$user = 'secureblog';        // Utiliser l'admin 'postgres' pour écarter tout problème de privilèges
$pass = 'secureblog123'; // Le mot de passe de votre serveur PostgreSQL

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$name";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion à PostgreSQL : " . $e->getMessage());
}
?>