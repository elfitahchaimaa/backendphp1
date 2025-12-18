<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'php/config.php';

// Vérifier si déjà connecté
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    } elseif ($_SESSION['role'] === 'auteur') {
        header('Location: author/dashboard.php');
        exit();
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (!empty($email) && !empty($password)) {
        try {
            // Récupérer l'utilisateur par email
            $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Vérifier le mot de passe avec Bcrypt
                if (password_verify($password, $user['mot_de_passe'])) {
                    // Vérifier que c'est admin ou auteur
                    if (in_array($user['role'], ['admin', 'auteur'])) {
                        // ✅ CRÉER LES SESSIONS AVEC LES BONS NOMS
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['nom'];  // 👈 "username" pas "user_name"
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];     // 👈 "role" pas "user_role"
                        
                        // Redirection selon le rôle
                        if ($user['role'] === 'admin') {
                            header('Location: admin/dashboard.php');
                            exit();
                        } else {
                            header('Location: author/dashboard.php');
                            exit();
                        }
                    } else {
                        $error = "Accès réservé aux administrateurs et auteurs uniquement";
                    }
                } else {
                    $error = "Email ou mot de passe incorrect";
                }
            } else {
                $error = "Email ou mot de passe incorrect";
            }
        } catch (Exception $e) {
            $error = "Erreur de connexion : " . $e->getMessage();
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>

<!doctype html>
<html lang="fr">

  <head>
    <title>Trips &mdash; Connexion</title>
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

    <!-- MAIN CSS -->
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
                <a href="index.html" class="font-weight-bold">
                  <img src="images/logo.png" alt="Image" class="img-fluid">
                </a>
              </div>
            </div>

            <div class="col-9  text-right">
              

              <span class="d-inline-block d-lg-none"><a href="#" class="text-white site-menu-toggle js-menu-toggle py-5 text-white"><span class="icon-menu h3 text-white"></span></a></span>

              

              <nav class="site-navigation text-right ml-auto d-none d-lg-block" role="navigation">
                <ul class="site-menu main-menu js-clone-nav ml-auto ">
                  <li><a href="index.html" class="nav-link">Home</a></li>
                  <li><a href="about.html" class="nav-link">About</a></li>
                  <li><a href="trips.html" class="nav-link">Trips</a></li>
                  <li><a href="blog.html" class="nav-link">Blog</a></li>
                  <li class="active"><a href="contact.php" class="nav-link">Connexion</a></li>
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
            <div class="col-md-5" data-aos="fade-up">
              <h1 class="mb-3 text-white">Connexion</h1>
              <p>Accédez à votre espace personnel.</p>
              
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="site-section">
      <div class="container">

        <div class="row justify-content-center text-center mb-5">
          <div class="col-md-10">
            <div class="heading-39101 mb-5">
              <span class="backdrop text-center">Login</span>
              <span class="subtitle-39191">Se connecter</span>
              <h3>Connexion au système</h3>
            </div>
          </div>
        </div>
       
        <div class="row">
          <div class="col-lg-8 mb-5" >
            <form action="" method="post">
              <div class="form-group row">
                <div class="col-md-6 mb-4 mb-lg-0">
                  <input type="email" class="form-control" name="email" placeholder="Adresse email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                  <input type="password" class="form-control" name="password" placeholder="Mot de passe" required>
                </div>
              </div>

              <?php if (!empty($error)): ?>
              <div class="form-group row">
                <div class="col-md-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>❌ Erreur :</strong> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                </div>
              </div>
              <?php endif; ?>

              <div class="form-group row">
                <div class="col-md-6 mr-auto">
                  <input type="submit" class="btn btn-block btn-primary text-white py-3 px-5" value="Se connecter">
                </div>
                <div class="col-md-6">
                  <a href="index.html" class="btn btn-block btn-secondary text-white py-3 px-5">Retour à l'accueil</a>
                </div>
              </div>
              
              <div class="form-group row mt-3">
                <div class="col-md-12 text-center">
                  <small class="text-muted">
                    🔒 Sécurisé avec Bcrypt | Le système détecte automatiquement votre rôle
                  </small>
                </div>
              </div>
            </form>
          </div>
          <div class="col-lg-4 ml-auto">
            <div class="bg-white p-3 p-md-5">
              <h3 class="text-black mb-4">📋 Informations de connexion</h3>
              <ul class="list-unstyled footer-link">
                <li class="d-block mb-3">
                  <span class="d-block text-black">👨‍💼 Compte Admin:</span>
                  <span><strong>admin@example.com</strong></span>
                </li>
                <li class="d-block mb-3">
                  <span class="d-block text-black">✍️ Compte Auteur:</span>
                  <span><strong>auteur@example.com</strong></span>
                </li>
                <li class="d-block mb-3">
                  <span class="d-block text-black">🔑 Mot de passe:</span>
                  <span><strong>admin123</strong> / <strong>auteur123</strong></span>
                </li>
                <li class="d-block mb-3">
                  <span class="d-block text-black">💬 Support:</span>
                  <span>support@example.com</span>
                </li>
              </ul>
              
              <div class="mt-4 p-3" style="background: #d4edda; border-left: 4px solid #28a745;">
                <h5 class="text-dark">🔐 Sécurité</h5>
                <p class="small mb-0">
                  Authentification sécurisée avec <strong>Bcrypt</strong>
                </p>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div> <!-- END .site-section -->

    <div class="site-section">

      <div class="container">

        <div class="row justify-content-center text-center">
          <div class="col-md-10">
            <div class="heading-39101 mb-5">
              <span class="backdrop text-center">Testimonials</span>
              <span class="subtitle-39191">Testimony</span>
              <h3>Happy Customers</h3>
            </div>
          </div>
        </div>

        <div class="owl-carousel slide-one-item">
          <div class="row">
            <div class="col-md-6">

              <div class="testimonial-39191 d-flex">
                <div class="mr-4">
                  <img src="images/person_1.jpg" alt="Image" class="img-fluid">
                </div>
                <div>
                <blockquote>&ldquo;Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, accusamus, facilis! Placeat praesentium alias porro aperiam facilis accusantium veniam?&rdquo;</blockquote>
                <p>&mdash; John Doe</p>
                </div>
              </div>    
              
            </div>

            <div class="col-md-6">

              <div class="testimonial-39191 d-flex">
                <div class="mr-4">
                  <img src="images/person_2.jpg" alt="Image" class="img-fluid">
                </div>
                <div>
                <blockquote>&ldquo;Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, accusamus, facilis! Placeat praesentium alias porro aperiam facilis accusantium veniam?&rdquo;</blockquote>
                <p>&mdash; John Doe</p>
                </div>
              </div>    
              
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">

              <div class="testimonial-39191 d-flex">
                <div class="mr-4">
                  <img src="images/person_1.jpg" alt="Image" class="img-fluid">
                </div>
                <div>
                <blockquote>&ldquo;Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, accusamus, facilis! Placeat praesentium alias porro aperiam facilis accusantium veniam?&rdquo;</blockquote>
                <p>&mdash; John Doe</p>
                </div>
              </div>    
              
            </div>

            <div class="col-md-6">

              <div class="testimonial-39191 d-flex">
                <div class="mr-4">
                  <img src="images/person_2.jpg" alt="Image" class="img-fluid">
                </div>
                <div>
                <blockquote>&ldquo;Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore, accusamus, facilis! Placeat praesentium alias porro aperiam facilis accusantium veniam?&rdquo;</blockquote>
                <p>&mdash; John Doe</p>
                </div>
              </div>    
              
            </div>
          </div>

        </div>

      </div>
    </div>

    <footer class="site-footer bg-light">
      <div class="container">
        <div class="row">
          <div class="col-lg-3">
            <h2 class="footer-heading mb-3">Instagram</h2>
            <div class="row">
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_1.jpg" alt="Image" class="img-fluid"></a>
              </div>
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_2.jpg" alt="Image" class="img-fluid"></a>
              </div>
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_3.jpg" alt="Image" class="img-fluid"></a>
              </div>
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_4.jpg" alt="Image" class="img-fluid"></a>
              </div>
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_5.jpg" alt="Image" class="img-fluid"></a>
              </div>
              <div class="col-4 gal_col">
                <a href="#"><img src="images/insta_6.jpg" alt="Image" class="img-fluid"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-8 ml-auto">
            <div class="row">
              <div class="col-lg-6 ml-auto">
                <h2 class="footer-heading mb-4">Quick Links</h2>
                <ul class="list-unstyled">
                  <li><a href="#">About Us</a></li>
                  <li><a href="#">Testimonials</a></li>
                  <li><a href="#">Terms of Service</a></li>
                  <li><a href="#">Privacy</a></li>
                  <li><a href="#">Contact Us</a></li>
                </ul>
              </div>
              <div class="col-lg-6">
                <h2 class="footer-heading mb-4">Newsletter</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Nesciunt odio iure animi ullam quam, deleniti rem!</p>
                <form action="#" class="d-flex" class="subscribe">
                  <input type="text" class="form-control mr-3" placeholder="Email">
                  <input type="submit" value="Send" class="btn btn-primary">
                </form>
              </div>
              
            </div>
          </div>
        </div>
        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <div class="border-top pt-5">
              <p>
            Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank" >Colorlib</a>
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