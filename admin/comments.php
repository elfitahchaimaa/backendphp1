<?php
require_once '../php/config.php';

$statusFilter = $_GET['status'] ?? 'en_attente';
$allowed = ['en_attente', 'approuve', 'refuse', 'tous'];
if (!in_array($statusFilter, $allowed, true)) {
    $statusFilter = 'en_attente';
}

$sql = "SELECT c.id, c.contenu, c.created_at, c.UPDATED_at, c.status,
               a.titre AS article_titre,
               u.nom AS auteur_nom, u.email AS auteur_email
        FROM commantaire c
        JOIN article a ON c.ida = a.id
        JOIN utilisateur u ON c.idu = u.id";

$params = [];
if ($statusFilter !== 'tous') {
    $sql .= " WHERE c.status = :status";
    $params[':status'] = $statusFilter;
}
$sql .= " ORDER BY c.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Commentaires';
$activePage = 'comments';
require 'partials/header.php';
?>

<h1 class="mb-4">Modération des commentaires</h1>

<div class="card mb-3">
  <div class="card-body">
    <span class="me-2">Filtre status :</span>
    <a href="?status=en_attente" class="btn btn-sm <?= $statusFilter === 'en_attente' ? 'btn-warning' : 'btn-outline-warning' ?>">En attente</a>
    <a href="?status=approuve" class="btn btn-sm <?= $statusFilter === 'approuve' ? 'btn-success' : 'btn-outline-success' ?>">Approuvés</a>
    <a href="?status=refuse" class="btn btn-sm <?= $statusFilter === 'refuse' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Refusés</a>
    <a href="?status=tous" class="btn btn-sm <?= $statusFilter === 'tous' ? 'btn-dark' : 'btn-outline-dark' ?>">Tous</a>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table table-striped table-hover mb-0">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Article</th>
          <th>Auteur</th>
          <th>Contenu</th>
          <th>Créé le</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($comments): ?>
        <?php foreach ($comments as $c): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><?= htmlspecialchars($c['article_titre']) ?></td>
            <td>
              <?= htmlspecialchars($c['auteur_nom']) ?><br>
              <small class="text-muted"><?= htmlspecialchars($c['auteur_email']) ?></small>
            </td>
            <td><?= nl2br(htmlspecialchars(mb_substr($c['contenu'], 0, 120))) ?></td>
            <td><?= htmlspecialchars($c['created_at']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td>
              <?php if ($c['status'] !== 'approuve'): ?>
                <a href="comment_approve.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-success mb-1">Approuver</a>
              <?php endif; ?>
              <?php if ($c['status'] !== 'refuse'): ?>
                <a href="comment_refuse.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-secondary mb-1">Refuser</a>
              <?php endif; ?>
              <a href="comment_delete.php?id=<?= (int)$c['id'] ?>" class="btn btn-sm btn-danger mb-1"
                 onclick="return confirm('Supprimer ce commentaire ?');">Supprimer</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" class="text-center p-4">Aucun commentaire.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require 'partials/footer.php'; ?>