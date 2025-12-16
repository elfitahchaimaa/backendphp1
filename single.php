<?php
session_start();
require_once 'php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: blog.php');
    exit;
}

$sql = "SELECT a.id, a.titre, a.contenu, a.date_creation, a.status,
               u.nom AS auteur,
               c.nom AS categorie
        FROM article a
        JOIN utilisateur u ON a.idutil = u.id
        JOIN categories c ON a.idca = c.id
        WHERE a.id = :id
        LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    header('Location: blog.php');
    exit;
}

$commentErrors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    if (empty($_SESSION['user'])) {
        $commentErrors[] = "Vous devez être connecté pour commenter.";
    } else {
        $contenu = trim($_POST['contenu'] ?? '');
        $idu = (int)$_SESSION['user']['id'];
        $ida = (int)$article['id'];

        if ($contenu === '') {
            $commentErrors[] = 'Commentaire vide';
        }

        if (!$commentErrors) {
            $ins = $db->prepare("INSERT INTO commantaire (contenu, idu, ida, created_at, UPDATED_at, status)
                                 VALUES (:contenu, :idu, :ida, CURDATE(), CURDATE(), :status)");
            $ins->execute([
                ':contenu' => $contenu,
                ':idu' => $idu,
                ':ida' => $ida,
                ':status' => 'approuve'
            ]);
            header("Location: single.php?id=" . $ida);
            exit;
        }
    }
}

$sel = $db->prepare("SELECT cm.id, cm.contenu, cm.created_at, cm.status,
                            u.nom AS auteur
                     FROM commantaire cm
                     JOIN utilisateur u ON cm.idu = u.id
                     WHERE cm.ida = :ida
                     ORDER BY cm.created_at DESC");
$sel->execute([':ida' => (int)$article['id']]);
$comments = $sel->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

  <head>
    <title><?= htmlspecialchars($article['titre']) ?> &mdash; Trips</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Work+Sans:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
    <link rel="stylesheet" href="css/aos.css">
    <link rel="stylesheet" href="css/style.css">
  </head>

  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

    <div class="site-wrap" id="home-section">

      <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
          <div class="site-mobile-menu-close mt-3">
            <span class="icon-close2 js-menu-toggle"></span>
          </div>
        </div>
        <div class="site-mobile-menu-body"></div>
      </div>

      <header class="site-navbar site-navbar-target" role="banner">
        <div class="container">
          <div class="row align-items-center position-relative">

            <div class="col-3 ">
              <div class="site-logo">
                <a href="index.php" class="font-weight-bold">
                  <img src="images/logo.png" alt="Image" class="img-fluid">
                </a>
              </div>
            </div>

            <div class="col-9  text-right">
              <span class="d-inline-block d-lg-none">
                <a href="#" class="text-white site-menu-toggle js-menu-toggle py-5 text-white">
                  <span class="icon-menu h3 text-white"></span>
                </a>
              </span>

              <nav class="site-navigation text-right ml-auto d-none d-lg-block" role="navigation">
                <ul class="site-menu main-menu js-clone-nav ml-auto ">
                  <li><a href="index.php" class="nav-link">Home</a></li>
                  <li><a href="about.php" class="nav-link">About</a></li>
                  <li><a href="trips.php" class="nav-link">Trips</a></li>
                  <li class="active"><a href="blog.php" class="nav-link">Blog</a></li>
                  <li><a href="contact.php" class="nav-link">Contact</a></li>
                </ul>
              </nav>
            </div>

          </div>
        </div>
      </header>

      <div class="ftco-blocks-cover-1">
        <div class="site-section-cover overlay" style="background-image: url('images/hero_1.jpg')">
          <div class="container">
            <div class="row align-items-center justify-content-center text-center">
              <div class="col-md-8" data-aos="fade-up">
                <h1 class="mb-3 text-white"><?= htmlspecialchars($article['titre']) ?></h1>
                <p class="text-white mb-0">
                  <?= htmlspecialchars($article['date_creation']) ?>
                  <span class="mx-2">by</span> <?= htmlspecialchars($article['auteur']) ?>
                  <span class="mx-2">in</span> <?= htmlspecialchars($article['categorie']) ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="site-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <p><img src="images/img_1.jpg" alt="Image" class="img-fluid mb-4"></p>
              <div class="blog-content">
                <?= nl2br(htmlspecialchars($article['contenu'])) ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="site-section">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-md-8">

              <h3 class="mb-4">Commentaires</h3>

              <?php if ($commentErrors): ?>
                <div class="alert alert-danger">
                  <?= implode('<br>', array_map('htmlspecialchars', $commentErrors)) ?>
                </div>
              <?php endif; ?>

              <?php if (!empty($_SESSION['user'])): ?>
                <form method="post" class="bg-light p-4 mb-4">
                  <div class="form-group">
                    <textarea name="contenu" class="form-control" rows="4" placeholder="Écrire un commentaire..."></textarea>
                  </div>
                  <button class="btn btn-primary" type="submit" name="add_comment" value="1">Publier</button>
                </form>
              <?php else: ?>
                <div class="alert alert-info">Connectez-vous pour écrire un commentaire.</div>
              <?php endif; ?>

              <?php if ($comments): ?>
                <?php foreach ($comments as $c): ?>
                  <div class="border p-3 mb-3 bg-white">
                    <div class="small text-muted mb-2">
                      <?= htmlspecialchars($c['auteur']) ?> — <?= htmlspecialchars($c['created_at']) ?>
                    </div>
                    <div><?= nl2br(htmlspecialchars($c['contenu'])) ?></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <p>Aucun commentaire.</p>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>

      <footer class="site-footer bg-light">
        <div class="container">
          <div class="row pt-5 mt-5 text-center">
            <div class="col-md-12">
              <div class="border-top pt-5">
                <p>
                  Copyright &copy;<script>document.write(new Date().getFullYear());</script>
                  All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i>
                  by <a href="https://colorlib.com" target="_blank">Colorlib</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/jquery-migrate-3.0.0.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/jquery.waypoints.min.js"></script>
    <script src="js/jquery.animateNumber.min.js"></script>
    <script src="js/jquery.fancybox.min.js"></script>
    <script src="js/jquery.stellar.min.js"></script>
    <script src="js/jquery.easing.1.3.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/isotope.pkgd.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/main.js"></script>

  </body>
</html>