<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Blog</title>
    <meta charset="UTF-8">
</head>
<body>
<nav>
    <a href="/project/index.php">Accueil</a>

    <?php if (isset($_SESSION['role'])): ?>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/project/admin/dashboard.php">Dashboard Admin</a>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'auteur'): ?>
            <a href="/project/auteur/articles/index.php">Mes articles</a>
        <?php endif; ?>

        <a href="/project/auth/logout.php">Déconnexion</a>
    <?php else: ?>
        <a href="/project/auth/login.php">Connexion</a>
    <?php endif; ?>
</nav>
<hr>
