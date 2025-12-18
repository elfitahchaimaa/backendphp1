<?php
session_start();

require '../config.php';

function checkUser($conn, $username, $pass){

    $stmt = $conn->prepare("SELECT * from user where email =? or username =?");
    $stmt->execute([$username, $username]);

    $user = $stmt->fetch();

    if(!$user) return 'wrong username/email';

    if ($user['password'] == $pass) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['fullname'] = $user['name'];
        $_SESSION['role'] = $user['auther'];

        return true;
    } else {
        return 'wrong password';
    }

}

// check if user i loggen in
if (isset($_SESSION['username'])) {
    echo "Welcome back, " . $_SESSION['username'];
    header('location: index.php');
    exit;
} else {

    // check if there is a post method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $results = checkUser($conn, $_POST['username'],$_POST['password']);
        if($results === true) {
            header('location: index.php');
        }
        else {
            echo $results;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .card-stat {
            transition: transform 0.3s;
        }
        .card-stat:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .quick-stats {
            margin-bottom: 2rem;
        }
        .recent-activity {
            margin-top: 2rem;
        }
        .list-group-item {
            border-left: 4px solid transparent;
        }
        .list-group-item:hover {
            border-left-color: #667eea;
            background-color: #f8f9fa;
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-1"><i class="bi bi-speedometer2"></i> Dashboard Admin</h1>
                    <p class="mb-0">Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | Système de gestion de contenu</p>
                </div>
                <div>
                    <span class="badge bg-light text-dark me-2">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['role']); ?>
                    </span>
                    <a href="../logout.php" class="btn btn-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="row quick-stats">
            <div class="col-md-3 mb-3">
                <div class="card card-stat text-bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2">Articles</h6>
                                <h2 class="card-title"><?= $articlesTotal ?></h2>
                                <div class="small">
                                    <span class="text-success"><?= $articlesPublies ?> publiés</span> | 
                                    <span class="text-warning"><?= $articlesBrouillons ?> brouillons</span>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-journal-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-stat text-bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2">Commentaires</h6>
                                <h2 class="card-title"><?= $commentsTotal ?></h2>
                                <div class="small">
                                    <span class="text-success"><?= $commentsApprouves ?> approuvés</span> | 
                                    <span class="text-warning"><?= $commentsEnAttente ?> en attente</span>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-chat-text"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-stat text-bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2">Utilisateurs</h6>
                                <h2 class="card-title"><?= $usersTotal ?></h2>
                                <div class="small">
                                    <span class="text-light"><?= $adminsTotal ?> administrateurs</span>
                                </div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-people"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="card card-stat text-bg-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2">Catégories</h6>
                                <?php 
                                $categoriesTotal = (int)$db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
                                ?>
                                <h2 class="card-title"><?= $categoriesTotal ?></h2>
                                <div class="small">Catégories actives</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-tags"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-lightning"></i> Actions rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="articles.php" class="btn btn-primary">
                                <i class="bi bi-journal-plus"></i> Nouvel article
                            </a>
                            <a href="gestion-articles.php" class="btn btn-outline-primary">
                                <i class="bi bi-journals"></i> Gérer les articles
                            </a>
                            <a href="gestion-commentaires.php" class="btn btn-outline-success">
                                <i class="bi bi-chat-left-text"></i> Gérer les commentaires
                            </a>
                            <a href="gestion-utilisateurs.php" class="btn btn-outline-info">
                                <i class="bi bi-people"></i> Gérer les utilisateurs
                            </a>
                            <a href="gestion-categories.php" class="btn btn-outline-warning">
                                <i class="bi bi-tags"></i> Gérer les catégories
                            </a>
                            <a href="../logout.php" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu principal en 2 colonnes -->
        <div class="row">
            <!-- Colonne gauche : Articles récents -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> Articles récents</h5>
                        <a href="gestion-articles.php" class="btn btn-sm btn-outline-primary">Voir tous</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($articlesRecents->rowCount() > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($article = $articlesRecents->fetch(PDO::FETCH_ASSOC)): 
                                    $status_color = '';
                                    $status_text = '';
                                    switch($article['status']) {
                                        case 'publie': $status_color = 'success'; $status_text = 'Publié'; break;
                                        case 'brouillon': $status_color = 'secondary'; $status_text = 'Brouillon'; break;
                                        case 'en_attente': $status_color = 'warning'; $status_text = 'En attente'; break;
                                        default: $status_color = 'info'; $status_text = $article['status'];
                                    }
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($article['titre']); ?></h6>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> <?php echo htmlspecialchars($article['auteur_nom'] ?? 'Non assigné'); ?> |
                                                <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($article['date_creation'])); ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?= $status_color ?> status-badge ms-2"><?= $status_text ?></span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-journal-x display-4 text-muted mb-3"></i>
                                <p class="text-muted">Aucun article pour le moment</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Commentaires récents -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Commentaires récents</h5>
                        <a href="gestion-commentaires.php" class="btn btn-sm btn-outline-primary">Voir tous</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($commentairesRecents->rowCount() > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($comment = $commentairesRecents->fetch(PDO::FETCH_ASSOC)): 
                                    $status_color = $comment['status'] === 'approuve' ? 'success' : ($comment['status'] === 'en_attente' ? 'warning' : 'secondary');
                                    $status_text = $comment['status'] === 'approuve' ? 'Approuvé' : ($comment['status'] === 'en_attente' ? 'En attente' : 'Non approuvé');
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($comment['utilisateur_nom'] ?? 'Anonyme'); ?></h6>
                                            <p class="mb-1 small"><?php echo htmlspecialchars(substr($comment['contentu'], 0, 80)); ?><?php echo strlen($comment['contentu']) > 80 ? '...' : ''; ?></p>
                                            <small class="text-muted">
                                                Article: <?php echo htmlspecialchars($comment['article_titre']); ?> |
                                                <?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-<?= $status_color ?> status-badge ms-2"><?= $status_text ?></span>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-chat-left-text display-4 text-muted mb-3"></i>
                                <p class="text-muted">Aucun commentaire pour le moment</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deuxième ligne : Utilisateurs récents et Statistiques -->
        <div class="row">
            <!-- Utilisateurs récents -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-people"></i> Utilisateurs récents</h5>
                        <a href="gestion-utilisateurs.php" class="btn btn-sm btn-outline-primary">Voir tous</a>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($utilisateursRecents->rowCount() > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($user = $utilisateursRecents->fetch(PDO::FETCH_ASSOC)): 
                                    $role_color = $user['role'] === 'admin' ? 'danger' : 'info';
                                    $role_icon = $user['role'] === 'admin' ? 'bi-shield-check' : 'bi-person';
                                ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi <?= $role_icon ?> fs-4 text-<?= $role_color ?>"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($user['nom']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?= $role_color ?>"><?= $user['role'] ?></span>
                                            <small class="text-muted d-block text-end"><?php echo date('d/m/Y', strtotime($user['CREATE_D_AT'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                                <p class="text-muted">Aucun utilisateur</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Statistiques avancées -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistiques avancées</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <div class="text-muted small">Articles ce mois</div>
                                    <div class="h4">
                                        <?php 
                                        $articlesCeMois = $db->query("SELECT COUNT(*) FROM article 
                                                                     WHERE MONTH(date_creation) = MONTH(CURRENT_DATE())
                                                                     AND YEAR(date_creation) = YEAR(CURRENT_DATE())")->fetchColumn();
                                        echo $articlesCeMois;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <div class="text-muted small">Commentaires ce mois</div>
                                    <div class="h4">
                                        <?php 
                                        $commentairesCeMois = $db->query("SELECT COUNT(*) FROM commentaire 
                                                                         WHERE MONTH(created_at) = MONTH(CURRENT_DATE())
                                                                         AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn();
                                        echo $commentairesCeMois;
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <div class="text-muted small">Taux de publication</div>
                                    <div class="h4 text-success">
                                        <?php 
                                        $tauxPublication = $articlesTotal > 0 ? round(($articlesPublies / $articlesTotal) * 100, 1) : 0;
                                        echo $tauxPublication . '%';
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 border rounded">
                                    <div class="text-muted small">Taux d'approbation</div>
                                    <div class="h4 text-info">
                                        <?php 
                                        $tauxApprobation = $commentsTotal > 0 ? round(($commentsApprouves / $commentsTotal) * 100, 1) : 0;
                                        echo $tauxApprobation . '%';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pied de page -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-0 text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Connecté en tant que <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> | 
                            Dernière connexion: <?php echo date('d/m/Y H:i'); ?> |
                            <a href="../logout.php" class="text-danger text-decoration-none">
                                <i class="bi bi-box-arrow-right"></i> Se déconnecter
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Scripts simples
        document.addEventListener('DOMContentLoaded', function() {
            // Confirmation pour les actions importantes
            document.querySelectorAll('.btn-danger').forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                        e.preventDefault();
                    }
                });
            });

            // Actualiser automatiquement toutes les 5 minutes
            setTimeout(function() {
                window.location.reload();
            }, 300000); // 5 minutes
        });
    </script>
</body>
</html>