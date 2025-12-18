<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../contact.php');
    exit();
}
require_once '../php/config.php';

$articlesTotal = (int)$db->query("SELECT COUNT(*) FROM article")->fetchColumn();
$articlesPublies = (int)$db->query("SELECT COUNT(*) FROM article WHERE status = 'publie'")->fetchColumn();
$articlesBrouillons = (int)$db->query("SELECT COUNT(*) FROM article WHERE status = 'brouillon'")->fetchColumn();

$commentsTotal = (int)$db->query("SELECT COUNT(*) FROM commantaire")->fetchColumn();
$commentsEnAttente = (int)$db->query("SELECT COUNT(*) FROM commantaire WHERE status = 'en_attente'")->fetchColumn();
$commentsApprouves = (int)$db->query("SELECT COUNT(*) FROM commantaire WHERE status = 'approuve'")->fetchColumn();

$usersTotal = (int)$db->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$adminsTotal = (int)$db->query("SELECT COUNT(*) FROM utilisateur WHERE role = 'admin'")->fetchColumn();

$pageTitle = 'Dashboard Admin';
$activePage = 'dashboard';
require 'partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="mb-0">Dashboard Admin</h1>
  <div>
    <span class="me-3 text-muted">
      <i class="bi bi-person-circle"></i> 
      <?php echo htmlspecialchars($_SESSION['username']); ?> 
      (<?php echo htmlspecialchars($_SESSION['role']); ?>)
    </span>
    <a href="../logout.php" class="btn btn-danger btn-sm">
      <i class="bi bi-box-arrow-right"></i> Déconnexion
    </a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-bg-primary">
      <div class="card-body">
        <div class="small">Articles (total)</div>
        <div class="display-6"><?= $articlesTotal ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-success">
      <div class="card-body">
        <div class="small">Articles publiés</div>
        <div class="display-6"><?= $articlesPublies ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-secondary">
      <div class="card-body">
        <div class="small">Brouillons</div>
        <div class="display-6"><?= $articlesBrouillons ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card text-bg-info">
      <div class="card-body">
        <div class="small">Commentaires (total)</div>
        <div class="display-6"><?= $commentsTotal ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-warning">
      <div class="card-body">
        <div class="small">Commentaires en attente</div>
        <div class="display-6"><?= $commentsEnAttente ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-bg-success">
      <div class="card-body">
        <div class="small">Commentaires approuvés</div>
        <div class="display-6"><?= $commentsApprouves ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card text-bg-dark">
      <div class="card-body">
        <div class="small">Utilisateurs (total)</div>
        <div class="display-6"><?= $usersTotal ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card text-bg-danger">
      <div class="card-body">
        <div class="small">Admins</div>
        <div class="display-6"><?= $adminsTotal ?></div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">Actions rapides</h5>
    <div class="btn-group" role="group">
      <a href="articles.php" class="btn btn-primary">Gérer les articles</a>
      <a href="comments.php" class="btn btn-success">Gérer les commentaires</a>
      <a href="users.php" class="btn btn-info">Gérer les utilisateurs</a>
      <a href="../logout.php" class="btn btn-danger">Se déconnecter</a>
    </div>
  </div>
</div>

<?php require 'partials/footer.php'; ?>