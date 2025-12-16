<?php
require_once '../php/config.php';
require_once '../php/auth.php';
require_role(['auteur','admin']);

$uid = (int)$_SESSION['user']['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: articles.php'); exit; }

$cats = $db->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM article WHERE id = :id AND idutil = :uid");
$stmt->execute([':id' => $id, ':uid' => $uid]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) { http_response_code(403); exit('Accès refusé'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $idca = (int)($_POST['idca'] ?? 0);
    $status = trim($_POST['status'] ?? 'brouillon');

    if ($titre === '') $errors[] = 'Titre obligatoire';
    if ($contenu === '') $errors[] = 'Contenu obligatoire';
    if ($idca <= 0) $errors[] = 'Catégorie obligatoire';

    if (!$errors) {
        $up = $db->prepare("UPDATE article
                            SET idca=:idca, titre=:titre, contenu=:contenu, status=:status
                            WHERE id=:id AND idutil=:uid");
        $up->execute([
            ':idca' => $idca,
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':status' => $status,
            ':id' => $id,
            ':uid' => $uid
        ]);
        header('Location: articles.php');
        exit;
    }
} else {
    $_POST = $article;
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Edit Article</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="site-wrap">
  <div class="container py-5">
    <h2 class="mb-4">Modifier l’article</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
    <?php endif; ?>

    <form method="post" class="bg-white p-4 border">
      <div class="form-group">
        <label>Titre</label>
        <input name="titre" class="form-control" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label>Contenu</label>
        <textarea name="contenu" rows="8" class="form-control"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Catégorie</label>
        <select name="idca" class="form-control">
          <option value="">-- choisir --</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= (($_POST['idca'] ?? '') == $c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <option value="brouillon" <?= (($_POST['status'] ?? '') === 'brouillon') ? 'selected' : '' ?>>brouillon</option>
          <option value="publie" <?= (($_POST['status'] ?? '') === 'publie') ? 'selected' : '' ?>>publie</option>
        </select>
      </div>

      <button class="btn btn-primary">Mettre à jour</button>
      <a class="btn btn-secondary" href="articles.php">Retour</a>
    </form>
  </div>
</div>
</body>
</html>