<?php
require_once '../php/config.php';

$sql = "SELECT a.id, a.titre, a.date_creation, a.status,
               u.nom AS auteur, c.nom AS categorie
        FROM article a
        JOIN utilisateur u ON a.idutil = u.id
        JOIN categories c ON a.idca = c.id
        ORDER BY a.date_creation DESC";
$stmt = $db->query($sql);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin - Articles</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body class="p-4">
  <div class="container">
    <h1 class="mb-4">Gestion des articles</h1>
    <a href="article_add.php" class="btn btn-primary mb-3">Ajouter un article</a>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Titre</th>
          <th>Auteur</th>
          <th>Catégorie</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($articles as $article): ?>
        <tr>
          <td><?= htmlspecialchars($article['id']) ?></td>
          <td><?= htmlspecialchars($article['titre']) ?></td>
          <td><?= htmlspecialchars($article['auteur']) ?></td>
          <td><?= htmlspecialchars($article['categorie']) ?></td>
          <td><?= htmlspecialchars($article['date_creation']) ?></td>
          <td><?= htmlspecialchars($article['status']) ?></td>
          <td>
            <a href="article_edit.php?id=<?= (int)$article['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
            <a href="article_delete.php?id=<?= (int)$article['id'] ?>"
               class="btn btn-sm btn-danger"
               onclick="return confirm('Supprimer cet article ?');">
               Supprimer
            </a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>