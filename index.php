<?php
require_once 'php/config.php';


$sqlArticles = "SELECT a.id, a.titre, a.contenu, a.date_pub,
                       u.nom AS auteur,
                       c.nom AS categorie,
                       (SELECT COUNT(*) FROM commantaire cm WHERE cm.ida = a.id) AS nb_comments
                FROM article a
                JOIN utilisateur u ON a.idutil = u.id
                JOIN categories c ON a.idca = c.id
                ORDER BY a.date_pub DESC";
$articles = $db->query($sqlArticles)->fetchAll(PDO::FETCH_ASSOC);


$sqlCommentaires = "SELECT c.*, a.titre AS article_titre, u.nom AS auteur_commentaire
                    FROM commantaire c
                    JOIN article a ON c.ida = a.id
                    JOIN utilisateur u ON c.idu = u.id
                    ORDER BY c.created_at DESC";
$commentaires = $db->query($sqlCommentaires)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <title>Trips &mdash; Website Template by Colorlib</title>
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

                        <span class="d-inline-block d-lg-none"><a href="#" class="text-white site-menu-toggle js-menu-toggle py-5 text-white"><span class="icon-menu h3 text-white"></span></a>
                        </span>

                        <nav class="site-navigation text-right ml-auto d-none d-lg-block" role="navigation">
                            <ul class="site-menu main-menu js-clone-nav ml-auto ">
                                <li class="active"><a href="index.php" class="nav-link">Home</a></li>
                                <li><a href="about.php" class="nav-link">About</a></li>
                                <li><a href="trips.php" class="nav-link">Trips</a></li>
                                <li><a href="blog.php" class="nav-link">Blog</a></li>
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
                    <div class="row align-items-center">
                        <div class="col-md-5" data-aos="fade-right">
                            <h1 class="mb-3 text-white">Let's Enjoy with arabi The Wonders of Nature</h1>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Soluta veritatis in tenetur doloremque, maiores doloribus officia iste. Dolores.</p>
                            <p class="d-flex align-items-center">
                                <a href="https://vimeo.com/191947042" data-fancybox class="play-btn-39282 mr-3"><span class="icon-play"></span></a>
                                <span class="small">Watch the video</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="site-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="heading-39101 mb-5">
                            <span class="backdrop">Story</span>
                            <span class="subtitle-39191">Discover Story</span>
                            <h3>Our Story</h3>
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi quae expedita fugiat quo incidunt, possimus temporibus aperiam eum, quaerat sapiente.</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos debitis enim a pariatur molestiae.</p>
                    </div>
                    <div class="col-md-6" data-aos="fade-right">
                        <img src="images/traveler.jpg" alt="Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>

        
        <div class="site-section">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-md-10">
                        <div class="heading-39101 mb-5">
                            <span class="backdrop text-center">Articles</span>
                            <span class="subtitle-39191">Tous les articles</span>
                            <h3>Articles de la base de données (<?= count($articles) ?> articles)</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if (count($articles) > 0): ?>
                        <?php foreach ($articles as $article): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="post-entry-1 h-100">
                                    <a href="single.php?id=<?= (int)$article['id'] ?>">
                                        <img src="images/img_1.jpg" alt="Image" class="img-fluid">
                                    </a>
                                    <div class="post-entry-1-contents">

                                        <span class="px-3 mb-3 category bg-primary"><?= htmlspecialchars($article['categorie']) ?></span>

                                        <h2><a href="single.php?id=<?= (int)$article['id'] ?>"><?= htmlspecialchars($article['titre']) ?></a></h2>

                                        <span class="meta d-inline-block mb-3">
                                            Date: <?= !empty($article['date_pub']) ? htmlspecialchars($article['date_pub']) : 'Non spécifiée' ?>
                                            <span class="mx-2">•</span> Auteur: <?= htmlspecialchars($article['auteur']) ?>
                                            <span class="mx-2">•</span> <?= (int)$article['nb_comments'] ?> commentaire(s)
                                        </span>

                                        <p><?= htmlspecialchars(mb_substr($article['contenu'], 0, 150)) ?>...</p>
                                        
                                        <div class="mt-3">
                                            <a href="single.php?id=<?= (int)$article['id'] ?>" class="btn btn-primary btn-sm">Lire la suite</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="lead">Aucun article dans la base de données.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="site-section bg-light">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-md-10">
                        <div class="heading-39101 mb-5">
                            <span class="backdrop text-center">Commentaires</span>
                            <span class="subtitle-39191">Tous les commentaires</span>
                            <h3>Commentaires de la base de données (<?= count($commentaires) ?> commentaires)</h3>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if (count($commentaires) > 0): ?>
                        <?php foreach ($commentaires as $commentaire): ?>
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="post-entry-1 h-100">
                                    <div class="post-entry-1-contents p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-primary rounded-circle p-2 mr-3 text-white">
                                                <span class="icon-person"></span>
                                            </div>
                                            <div>
                                                <h5 class="mb-0"><?= htmlspecialchars($commentaire['auteur_commentaire']) ?></h5>
                                                <small class="text-muted">
                                                    <?= !empty($commentaire['created_at']) ? date('d/m/Y H:i', strtotime($commentaire['created_at'])) : 'Date non spécifiée' ?>
                                                </small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <span class="badge badge-info">Article ID: <?= (int)$commentaire['ida'] ?></span>
                                            <a href="single.php?id=<?= (int)$commentaire['ida'] ?>" class="badge badge-secondary ml-2">
                                                <?= htmlspecialchars(mb_substr($commentaire['article_titre'], 0, 40)) ?>...
                                            </a>
                                        </div>
                                        
                                        <div class="comment-content p-3 bg-white rounded">
                                            <p class="mb-0"><?= htmlspecialchars($commentaire['contenu']) ?></p>
                                        </div>
                                        
                                        <div class="mt-3 text-muted small">
                                            <span>Status: <?= !empty($commentaire['status']) ? htmlspecialchars($commentaire['status']) : 'Non spécifié' ?></span>
                                            <?php if (!empty($commentaire['UPDATE_D_at'])): ?>
                                                <span class="ml-3">Modifié le: <?= date('d/m/Y', strtotime($commentaire['UPDATE_D_at'])) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="lead">Aucun commentaire dans la base de données.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <footer class="site-footer bg-light">
            <div class="container">
                <div class="row pt-5 mt-5 text-center">
                    <div class="col-md-12">
                        <div class="border-top pt-5">
                            <p>
                                Copyright &copy;
                                <script>document.write(new Date().getFullYear());</script>
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