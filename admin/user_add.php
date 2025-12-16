<?php
require_once '../php/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['mot_de_passe'] ?? '';
    $role = trim($_POST['role'] ?? 'user');

    if ($nom === '') $errors[] = 'Nom obligatoire';
    if ($email === '') $errors[] = 'Email obligatoire';
    if ($pass === '') $errors[] = 'Mot de passe obligatoire';
    if ($role === '') $errors[] = 'Rôle obligatoire';

    if (!$errors) {
        $check = $db->prepare("SELECT id FROM utilisateur WHERE email = :email LIMIT 1");
        $check->execute([':email' => $email]);
        if ($check->fetch()) {
            $errors[] = 'Email existe déjà';
        }
    }

    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, role, CREATED_AT)
                              VALUES (:nom, :email, :mdp, :role, CURDATE())");
        $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':mdp' => $hash,
            ':role' => $role
        ]);

        header('Location: users.php');
        exit;
    }
}

$pageTitle = 'Ajouter utilisateur';
$activePage = 'users';
require 'partials/header.php';
?>

<h1 class="mb-4">Ajouter un utilisateur</h1>

<?php if ($errors): ?>
  <div class="alert alert-danger"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Nom</label>
        <input class="form-control" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input class="form-control" type="password" name="mot_de_passe">
      </div>

      <div class="mb-3">
        <label class="form-label">Rôle</label>
        <select class="form-select" name="role">
          <?php $r = $_POST['role'] ?? 'user'; ?>
          <option value="user" <?= $r === 'user' ? 'selected' : '' ?>>user</option>
          <option value="auteur" <?= $r === 'auteur' ? 'selected' : '' ?>>auteur</option>
          <option value="admin" <?= $r === 'admin' ? 'selected' : '' ?>>admin</option>
        </select>
      </div>

      <button class="btn btn-success" type="submit">Enregistrer</button>
      <a class="btn btn-secondary" href="users.php">Annuler</a>
    </form>
  </div>
</div>

<?php require 'partials/footer.php'; ?>