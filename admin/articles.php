<?php
require_once '../php/config.php';

$sql = "SELECT a.id, a.titre, a.date_creation, a.status,
               u.nom AS auteur, c.nom AS categorie
        FROM article a
        JOIN utilisateur u ON a.idutil = u.id
        JOIN categories c ON a.idca = c.id
        ORDER BY a.date_creation DESC";
$articles = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Articles';
$activePage = 'articles';
require 'partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">Articles</h1>
  <a href="article_add.php" class="btn btn-primary">Ajouter</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped table-hover mb-0">
      <thead class="table-dark">
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
      <?php foreach ($articles as $a): ?>
        <tr>
          <td><?= (int)$a['id'] ?></td>
          <td><?= htmlspecialchars($a['titre']) ?></td>
          <td><?= htmlspecialchars($a['auteur']) ?></td>
          <td><?= htmlspecialchars($a['categorie']) ?></td>
          <td><?= htmlspecialchars($a['date_creation']) ?></td>
          <td><?= htmlspecialchars($a['status']) ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="article_edit.php?id=<?= (int)$a['id'] ?>">Modifier</a>
            <a class="btn btn-sm btn-danger" href="article_delete.php?id=<?= (int)$a['id'] ?>" onclick="return confirm('Supprimer ?');">Supprimer</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'partials/footer.php'; ?>