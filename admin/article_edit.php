<?php
require_once '../php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: articles.php');
    exit;
}

$sqlUsers = "SELECT id, nom FROM utilisateur ORDER BY nom";
$users = $db->query($sqlUsers)->fetchAll(PDO::FETCH_ASSOC);

$sqlCats = "SELECT id, nom FROM categories ORDER BY nom";
$cats = $db->query($sqlCats)->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM article WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: articles.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $idutil  = (int)($_POST['idutil'] ?? 0);
    $idca    = (int)($_POST['idca'] ?? 0);
    $status  = trim($_POST['status'] ?? 'brouillon');

    if ($titre === '')   $errors[] = 'Titre obligatoire';
    if ($contenu === '') $errors[] = 'Contenu obligatoire';
    if ($idutil <= 0)    $errors[] = 'Auteur obligatoire';
    if ($idca <= 0)      $errors[] = 'Catégorie obligatoire';

    if (empty($errors)) {
        $sqlUpdate = "UPDATE article
                      SET idutil = :idutil,
                          idca   = :idca,
                          titre  = :titre,
                          contenu = :contenu,
                          status = :status
                      WHERE id = :id";
        $stmtUp = $db->prepare($sqlUpdate);
        $stmtUp->execute([
            ':idutil'  => $idutil,
            ':idca'    => $idca,
            ':titre'   => $titre,
            ':contenu' => $contenu,
            ':status'  => $status,
            ':id'      => $id
        ]);
        header('Location: articles.php');
        exit;
    }
} else {
    $_POST = $article;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier un article</title>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body class="p-4">
  <div class="container">
    <h1 class="mb-4">Modifier un article</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Titre</label>
        <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Contenu</label>
        <textarea name="contenu" class="form-control" rows="8"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Auteur</label>
        <select name="idutil" class="form-select">
          <option value="">-- Choisir --</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= (int)$u['id'] ?>" <?= (($_POST['idutil'] ?? $article['idutil']) == $u['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select name="idca" class="form-select">
          <option value="">-- Choisir --</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= (($_POST['idca'] ?? $article['idca']) == $c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="brouillon" <?= (($_POST['status'] ?? $article['status']) === 'brouillon') ? 'selected' : '' ?>>Brouillon</option>
          <option value="publie" <?= (($_POST['status'] ?? $article['status']) === 'publie') ? 'selected' : '' ?>>Publié</option>
        </select>
      </div>

      <button type="submit" class="btn btn-success">Mettre à jour</button>
      <a href="articles.php" class="btn btn-secondary">Annuler</a>
    </form>
  </div>
</body>
</html>