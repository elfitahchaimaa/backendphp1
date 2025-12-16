<?php
require_once '../php/config.php';
require_once '../php/auth.php';
require_role(['auteur','admin']);

$uid = (int)$_SESSION['user']['id'];
$cats = $db->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

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
        $stmt = $db->prepare("INSERT INTO article (idutil, idca, titre, contenu, date_creation, status)
                              VALUES (:idutil,:idca,:titre,:contenu,NOW(),:status)");
        $stmt->execute([
            ':idutil' => $uid,
            ':idca' => $idca,
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':status' => $status
        ]);
        header('Location: articles.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Add Article</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,700,900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="site-wrap">
  <div class="container py-5">
    <h2 class="mb-4">Ajouter un article</h2>

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
          <option value="brouillon" <?= (($_POST['status'] ?? 'brouillon') === 'brouillon') ? 'selected' : '' ?>>brouillon</option>
          <option value="publie" <?= (($_POST['status'] ?? '') === 'publie') ? 'selected' : '' ?>>publie</option>
        </select>
      </div>

      <button class="btn btn-primary">Enregistrer</button>
      <a class="btn btn-secondary" href="articles.php">Retour</a>
    </form>
  </div>
</div>
</body>
</html>