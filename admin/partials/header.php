<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'BlogCMS Admin') ?></title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">BlogCMS Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link<?= ($activePage ?? '') === 'dashboard' ? ' active' : '' ?>" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= ($activePage ?? '') === 'articles' ? ' active' : '' ?>" href="articles.php">Articles</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= ($activePage ?? '') === 'comments' ? ' active' : '' ?>" href="comments.php">Commentaires</a>
        </li>
        <li class="nav-item">
          <a class="nav-link<?= ($activePage ?? '') === 'users' ? ' active' : '' ?>" href="users.php">Utilisateurs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../blog.php" target="_blank">Voir le site</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">