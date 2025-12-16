<?php
require_once '../php/config.php';

$articlesTotal = (int)$db->query("SELECT COUNT(*) FROM article")->fetchColumn();
$articlesPublies = (int)$db->query("SELECT COUNT(*) FROM article WHERE status = 'publie'")->fetchColumn();
$articlesBrouillons = (int)$db->query("SELECT COUNT(*) FROM article WHERE status = 'brouillon'")->fetchColumn();

$commentsTotal = (int)$db->query("SELECT COUNT(*) FROM commantaire")->fetchColumn();
$commentsEnAttente = (int)$db->query("SELECT COUNT(*) FROM commantaire WHERE status = 'en_attente'")->fetchColumn();
$commentsApprouves = (int)$db->query("SELECT COUNT(*) FROM commantaire WHERE status = 'approuve'")->fetchColumn();

$usersTotal = (int)$db->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
$adminsTotal = (int)$db->query("SELECT COUNT(*) FROM utilisateur WHERE role = 'admin'")->fetchColumn();

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require 'partials/header.php';
?>

<h1 class="mb-4">Dashboard</h1>

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

<?php require 'partials/footer.php'; ?>
*