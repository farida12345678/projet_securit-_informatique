# 🛡️ Projet de Sécurité Informatique : Mini-Blog Sécurisé

Ce projet a été réalisé dans le cadre du module de **Sécurité des Applications Web** (3ème Année Licence Génie Logiciel / BTS SIO SLAM).

L'objectif de ce travail est de concevoir un mini-blog interactif conteneurisé, de mettre en évidence deux vulnérabilités critiques de l'**OWASP Top 10** sur une première version (V1), puis d'implémenter les correctifs de sécurité nécessaires sur une seconde version (V2).

---

## 🛠️ Stack Technique & Architecture

* **Environnement :** Docker Multi-conteneurs (Docker Compose)
* **Serveur Web :** Apache / PHP 8.2
* **Base de données :** MySQL 8.0
* **Gestionnaire SGBD :** phpMyAdmin
* **Langages :** PHP, SQL, HTML5 / CSS3

---

## 📁 Structure du Dépôt

```text
.
├── docker-compose.yml   # Configuration des services Docker (web, db, phpmyadmin)
├── db.php               # Connexion PDO à la base de données
├── login_v1.php         # Authentification vulnérable (Injection SQL)
├── index_v1.php         # Espace commentaires vulnérable (XSS Stockée)
├── setup_pass.php       # Script d'initialisation des utilisateurs avec mot de passe haché
├── login_secure.php     # Authentification sécurisée (PDO Prepared Statements + Bcrypt)
└── index_secure.php     # Espace commentaires sécurisé (Neutralisation XSS avec htmlspecialchars)