<?php
require_once '../php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: users.php');
    exit;
}

$stmt = $db->prepare("SELECT id, nom, email, mot_de_passe, role, CREATED_AT
                      FROM utilisateur
                      WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: users.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = trim($_POST['role'] ?? 'user');
    $pass = $_POST['mot_de_passe'] ?? '';

    if ($nom === '') $errors[] = 'Nom obligatoire';
    if ($email === '') $errors[] = 'Email obligatoire';
    if ($role === '') $errors[] = 'Rôle obligatoire';

    if (!$errors) {
        $check = $db->prepare("SELECT id FROM utilisateur WHERE email = :email AND id <> :id LIMIT 1");
        $check->execute([':email' => $email, ':id' => $id]);
        if ($check->fetch()) {
            $errors[] = 'Email existe déjà';
        }
    }

    if (!$errors) {
        if ($pass !== '') {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $up = $db->prepare("UPDATE utilisateur
                                SET nom = :nom, email = :email, role = :role, mot_de_passe = :mdp
                                WHERE id = :id");
            $up->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':role' => $role,
                ':mdp' => $hash,
                ':id' => $id
            ]);
        } else {
            $up = $db->prepare("UPDATE utilisateur
                                SET nom = :nom, email = :email, role = :role
                                WHERE id = :id");
            $up->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':role' => $role,
                ':id' => $id
            ]);
        }

        header('Location: users.php');
        exit;
    }
} else {
    $_POST = [
        'nom' => $user['nom'],
        'email' => $user['email'],
        'role' => $user['role']
    ];
}

$pageTitle = 'Modifier utilisateur';
$activePage = 'users';
require 'partials/header.php';
?>

<h1 class="mb-4">Modifier utilisateur #<?= (int)$id ?></h1>

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
        <label class="form-label">Rôle</label>
        <select class="form-select" name="role">
          <?php $r = $_POST['role'] ?? 'user'; ?>
          <option value="user" <?= $r === 'user' ? 'selected' : '' ?>>user</option>
          <option value="auteur" <?= $r === 'auteur' ? 'selected' : '' ?>>auteur</option>
          <option value="admin" <?= $r === 'admin' ? 'selected' : '' ?>>admin</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Nouveau mot de passe (optionnel)</label>
        <input class="form-control" type="password" name="mot_de_passe">
      </div>

      <button class="btn btn-success" type="submit">Mettre à jour</button>
      <a class="btn btn-secondary" href="users.php">Annuler</a>
    </form>
  </div>
</div>

<?php require 'partials/footer.php'; ?>