<?php
require_once '../php/config.php';
require_once '../php/auth.php';
require_role(['auteur','admin']);

$uid = (int)$_SESSION['user']['id'];

$sql = "SELECT a.id, a.titre, a.date_creation, a.status, c.nom AS categorie
        FROM article a
        JOIN categories c ON a.idca = c.id
        WHERE a.idutil = :uid
        ORDER BY a.date_creation DESC";
$stmt = $db->prepare($sql);
$stmt->execute([':uid' => $uid]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <title>My Articles</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,700,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../fonts/icomoon/style.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="site-wrap">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Mes Articles</h2>
      <a href="article_add.php" class="btn btn-primary">Ajouter</a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered bg-white">
        <thead>
          <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
          <tr>
            <td><?= (int)$a['id'] ?></td>
            <td><?= htmlspecialchars($a['titre']) ?></td>
            <td><?= htmlspecialchars($a['categorie']) ?></td>
            <td><?= htmlspecialchars($a['status']) ?></td>
            <td><?= htmlspecialchars($a['date_creation']) ?></td>
            <td>
              <a class="btn btn-sm btn-warning" href="article_edit.php?id=<?= (int)$a['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-danger" href="article_delete.php?id=<?= (int)$a['id'] ?>" onclick="return confirm('Supprimer ?')">Delete</a>
              <a class="btn btn-sm btn-info" href="../single.php?id=<?= (int)$a['id'] ?>" target="_blank">Voir</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>