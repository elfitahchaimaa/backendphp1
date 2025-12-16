<?php
require_once '../php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: articles.php'); exit; }

$users = $db->query("SELECT id, nom FROM utilisateur ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$cats  = $db->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT * FROM article WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) { header('Location: articles.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $idutil = (int)($_POST['idutil'] ?? 0);
    $idca = (int)($_POST['idca'] ?? 0);
    $status = trim($_POST['status'] ?? 'brouillon');

    if ($titre === '') $errors[] = 'Titre obligatoire';
    if ($contenu === '') $errors[] = 'Contenu obligatoire';
    if ($idutil <= 0) $errors[] = 'Auteur obligatoire';
    if ($idca <= 0) $errors[] = 'Catégorie obligatoire';

    if (!$errors) {
        $up = $db->prepare("UPDATE article
                            SET idutil=:idutil, idca=:idca, titre=:titre, contenu=:contenu, status=:status
                            WHERE id=:id");
        $up->execute([
            ':idutil' => $idutil,
            ':idca' => $idca,
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':status' => $status,
            ':id' => $id
        ]);
        header('Location: articles.php');
        exit;
    }
} else {
    $_POST = $article;
}

$pageTitle = 'Modifier article';
$activePage = 'articles';
require 'partials/header.php';
?>

<h1 class="mb-4">Modifier l’article #<?= (int)$id ?></h1>

<?php if ($errors): ?>
  <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Titre</label>
        <input class="form-control" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Contenu</label>
        <textarea class="form-control" name="contenu" rows="8"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Auteur</label>
        <select class="form-select" name="idutil">
          <option value="">-- Choisir --</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= (int)$u['id'] ?>" <?= (($_POST['idutil'] ?? '') == $u['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select class="form-select" name="idca">
          <option value="">-- Choisir --</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= (($_POST['idca'] ?? '') == $c['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="brouillon" <?= (($_POST['status'] ?? '') === 'brouillon') ? 'selected' : '' ?>>brouillon</option>
          <option value="publie" <?= (($_POST['status'] ?? '') === 'publie') ? 'selected' : '' ?>>publie</option>
        </select>
      </div>

      <button class="btn btn-success" type="submit">Mettre à jour</button>
      <a class="btn btn-secondary" href="articles.php">Annuler</a>
    </form>
  </div>
</div>

<?php require 'partials/footer.php'; ?>