<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='fr'><head><meta charset='utf-8'>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 15px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { border-left: 4px solid #28a745; }
.error { border-left: 4px solid #dc3545; }
.warning { border-left: 4px solid #ffc107; }
.info { border-left: 4px solid #17a2b8; }
h1 { color: #333; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 5px; }
h2 { color: #333; margin-top: 0; }
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
th { background: #007bff; color: white; }
tr:nth-child(even) { background: #f8f9fa; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border-left: 3px solid #007bff; }
.btn { display: inline-block; padding: 15px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; }
.btn:hover { background: #0056b3; }
code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; color: #e83e8c; }
</style></head><body>";

echo "<h1>🔧 DIAGNOSTIC COMPLET DU SYSTÈME DE CONNEXION</h1>";

// ============================================================================
// ÉTAPE 1 : TEST DE CONNEXION À LA BASE DE DONNÉES
// ============================================================================
echo "<div class='box info'>";
echo "<h2>1️⃣ Test de connexion à la base de données</h2>";

$db_connected = false;
$db = null;

try {
    require_once 'php/config.php';
    
    if (isset($db) && $db !== null) {
        $test = $db->query("SELECT 1");
        echo "✅ <strong>Connexion à la base de données réussie !</strong><br>";
        echo "Type de connexion : <code>" . get_class($db) . "</code><br>";
        $db_connected = true;
    } else {
        echo "❌ <strong>Erreur : La variable \$db n'est pas définie dans database.php</strong><br>";
        echo "Vérifiez que votre fichier database.php contient bien une variable \$db<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Erreur de connexion :</strong> " . $e->getMessage() . "<br>";
    echo "<pre>";
    echo "Vérifiez votre fichier database.php\n";
    echo "Il devrait contenir quelque chose comme :\n\n";
    echo "\$host = 'localhost';\n";
    echo "\$dbname = 'votre_base';\n";
    echo "\$username = 'root';\n";
    echo "\$password = '';\n\n";
    echo "\$db = new PDO(\"mysql:host=\$host;dbname=\$dbname;charset=utf8\", \$username, \$password);\n";
    echo "\$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n";
    echo "</pre>";
}
echo "</div>";

if (!$db_connected) {
    echo "<div class='box error'>";
    echo "<h2>❌ ARRÊT DU DIAGNOSTIC</h2>";
    echo "<p>Impossible de continuer sans connexion à la base de données.</p>";
    echo "<p>Corrigez le fichier <code>database.php</code> et réessayez.</p>";
    echo "</div>";
    echo "</body></html>";
    exit();
}

// ============================================================================
// ÉTAPE 2 : VÉRIFICATION DE LA STRUCTURE DE LA TABLE
// ============================================================================
echo "<div class='box info'>";
echo "<h2>2️⃣ Vérification de la table 'utilisateur'</h2>";

try {
    $stmt = $db->query("SHOW TABLES LIKE 'utilisateur'");
    
    if ($stmt->rowCount() > 0) {
        echo "✅ <strong>La table 'utilisateur' existe</strong><br><br>";
        
        // Afficher la structure
        echo "<strong>Structure de la table :</strong><br>";
        $structure = $db->query("DESCRIBE utilisateur")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($structure as $col) {
            echo "<tr>";
            echo "<td><strong>{$col['Field']}</strong></td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Vérifier les colonnes nécessaires
        $required_columns = ['id', 'nom', 'email', 'mot_de_passe', 'role'];
        $existing_columns = array_column($structure, 'Field');
        
        echo "<br><strong>Vérification des colonnes requises :</strong><br>";
        foreach ($required_columns as $col) {
            if (in_array($col, $existing_columns)) {
                echo "✅ Colonne <code>{$col}</code> existe<br>";
            } else {
                echo "❌ Colonne <code>{$col}</code> MANQUANTE<br>";
            }
        }
        
    } else {
        echo "❌ <strong>La table 'utilisateur' n'existe PAS !</strong><br>";
        echo "<br><strong>Voulez-vous que je la crée ?</strong><br>";
        echo "<pre>";
        echo "CREATE TABLE utilisateur (\n";
        echo "  id INT(11) AUTO_INCREMENT PRIMARY KEY,\n";
        echo "  nom VARCHAR(100) NOT NULL,\n";
        echo "  email VARCHAR(100) UNIQUE NOT NULL,\n";
        echo "  mot_de_passe VARCHAR(255) NOT NULL,\n";
        echo "  role VARCHAR(50) NOT NULL,\n";
        echo "  CREATED_AT DATE\n";
        echo ");";
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 3 : AFFICHAGE DES UTILISATEURS ACTUELS
// ============================================================================
echo "<div class='box warning'>";
echo "<h2>3️⃣ Utilisateurs actuels dans la base</h2>";

try {
    $stmt = $db->query("SELECT * FROM utilisateur");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<strong>Nombre d'utilisateurs : " . count($users) . "</strong><br><br>";
        
        echo "<table>";
        echo "<tr>";
        foreach (array_keys($users[0]) as $key) {
            echo "<th>{$key}</th>";
        }
        echo "</tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            foreach ($user as $key => $value) {
                if ($key === 'mot_de_passe') {
                    echo "<td style='font-size: 10px; word-break: break-all;'>" . substr($value, 0, 40) . "...</td>";
                } else {
                    echo "<td><strong>{$value}</strong></td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Analyser les mots de passe
        echo "<br><strong>Analyse des mots de passe :</strong><br>";
        foreach ($users as $user) {
            $pwd_length = strlen($user['mot_de_passe']);
            $is_md5 = ($pwd_length === 32);
            $is_bcrypt = (substr($user['mot_de_passe'], 0, 4) === '$2y$' || substr($user['mot_de_passe'], 0, 4) === '$2a$');
            
            echo "Email : <strong>{$user['email']}</strong> - ";
            echo "Longueur hash : {$pwd_length} - ";
            
            if ($is_bcrypt) {
                echo "Type : ✅ <strong>Bcrypt</strong><br>";
            } elseif ($is_md5) {
                echo "Type : ⚠️ <strong>MD5 (obsolète)</strong><br>";
            } else {
                echo "Type : ❓ <strong>Inconnu ou texte clair</strong><br>";
            }
        }
        
    } else {
        echo "⚠️ <strong>Aucun utilisateur dans la base de données</strong>";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 4 : SUPPRESSION DES UTILISATEURS EXISTANTS
// ============================================================================
echo "<div class='box error'>";
echo "<h2>4️⃣ Suppression de tous les utilisateurs existants</h2>";

try {
    $count = $db->exec("DELETE FROM utilisateur");
    echo "✅ <strong>{$count} utilisateur(s) supprimé(s)</strong><br>";
    echo "La table est maintenant vide et prête pour les nouveaux utilisateurs.";
} catch (Exception $e) {
    echo "❌ Erreur lors de la suppression : " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 5 : CRÉATION DE NOUVEAUX UTILISATEURS AVEC BCRYPT
// ============================================================================
echo "<div class='box success'>";
echo "<h2>5️⃣ Création de nouveaux utilisateurs avec Bcrypt</h2>";

$nouveaux_users = [
    [
        'nom' => 'Administrateur',
        'email' => 'admin@example.com',
        'password' => 'admin123',
        'role' => 'admin'
    ],
    [
        'nom' => 'Auteur Principal',
        'email' => 'auteur@example.com',
        'password' => 'auteur123',
        'role' => 'auteur'
    ]
];

echo "<table>";
echo "<tr>";
echo "<th>Nom</th>";
echo "<th>Email</th>";
echo "<th>Mot de passe</th>";
echo "<th>Hash Bcrypt généré</th>";
echo "<th>Rôle</th>";
echo "<th>Statut</th>";
echo "</tr>";

foreach ($nouveaux_users as $user) {
    $hashed = password_hash($user['password'], PASSWORD_BCRYPT);
    
    echo "<tr>";
    echo "<td>{$user['nom']}</td>";
    echo "<td><strong>{$user['email']}</strong></td>";
    echo "<td><strong style='color: #dc3545;'>{$user['password']}</strong></td>";
    echo "<td style='font-size: 10px; word-break: break-all;'>" . substr($hashed, 0, 50) . "...</td>";
    echo "<td><strong>{$user['role']}</strong></td>";
    
    try {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$user['nom'], $user['email'], $hashed, $user['role']]);
        
        if ($result) {
            echo "<td style='background: #d4edda;'>✅ <strong>CRÉÉ</strong></td>";
        } else {
            echo "<td style='background: #f8d7da;'>❌ Échec</td>";
        }
    } catch (Exception $e) {
        echo "<td style='background: #f8d7da;'>❌ " . $e->getMessage() . "</td>";
    }
    
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// ============================================================================
// ÉTAPE 6 : VÉRIFICATION DES UTILISATEURS CRÉÉS
// ============================================================================
echo "<div class='box info'>";
echo "<h2>6️⃣ Vérification des utilisateurs créés</h2>";

try {
    $stmt = $conn->query("SELECT id, nom, email, role, LENGTH(mot_de_passe) as longueur_hash, LEFT(mot_de_passe, 30) as hash_preview FROM utilisateur");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Longueur Hash</th><th>Hash (aperçu)</th></tr>";
        
        foreach ($users as $user) {
            $hash_ok = ($user['longueur_hash'] >= 60); // Bcrypt = 60 caractères
            $bg = $hash_ok ? '#d4edda' : '#f8d7da';
            
            echo "<tr style='background: {$bg};'>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['nom']}</td>";
            echo "<td><strong>{$user['email']}</strong></td>";
            echo "<td><strong>{$user['role']}</strong></td>";
            echo "<td>{$user['longueur_hash']} " . ($hash_ok ? '✅' : '❌') . "</td>";
            echo "<td style='font-size: 10px;'>{$user['hash_preview']}...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 7 : TEST DE CONNEXION EN DIRECT
// ============================================================================
echo "<div class='box success'>";
echo "<h2>7️⃣ TEST DE CONNEXION EN DIRECT</h2>";

$test_credentials = [
    ['email' => 'admin@example.com', 'password' => 'admin123', 'role_attendu' => 'admin'],
    ['email' => 'auteur@example.com', 'password' => 'auteur123', 'role_attendu' => 'auteur']
];

echo "<table>";
echo "<tr>";
echo "<th>Email testé</th>";
echo "<th>Password testé</th>";
echo "<th>Utilisateur trouvé ?</th>";
echo "<th>password_verify()</th>";
echo "<th>Rôle correct ?</th>";
echo "<th>RÉSULTAT FINAL</th>";
echo "</tr>";

$all_tests_passed = true;

foreach ($test_credentials as $cred) {
    echo "<tr>";
    echo "<td><strong>{$cred['email']}</strong></td>";
    echo "<td><strong style='color: #dc3545;'>{$cred['password']}</strong></td>";
    
    try {
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$cred['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<td style='background: #d4edda;'>✅ OUI</td>";
            
            $verify = password_verify($cred['password'], $user['mot_de_passe']);
            
            if ($verify) {
                echo "<td style='background: #d4edda;'>✅ TRUE</td>";
            } else {
                echo "<td style='background: #f8d7da;'>❌ FALSE</td>";
                $all_tests_passed = false;
            }
            
            if ($user['role'] === $cred['role_attendu']) {
                echo "<td style='background: #d4edda;'>✅ {$user['role']}</td>";
            } else {
                echo "<td style='background: #f8d7da;'>❌ {$user['role']} (attendu: {$cred['role_attendu']})</td>";
                $all_tests_passed = false;
            }
            
            if ($verify && $user['role'] === $cred['role_attendu']) {
                echo "<td style='background: #d4edda; font-weight: bold;'>✅ CONNEXION OK</td>";
            } else {
                echo "<td style='background: #f8d7da; font-weight: bold;'>❌ ÉCHEC</td>";
                $all_tests_passed = false;
            }
            
        } else {
            echo "<td style='background: #f8d7da;'>❌ NON</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td style='background: #f8d7da; font-weight: bold;'>❌ UTILISATEUR INTROUVABLE</td>";
            $all_tests_passed = false;
        }
    } catch (Exception $e) {
        echo "<td colspan='4' style='background: #f8d7da;'>❌ Erreur : {$e->getMessage()}</td>";
        $all_tests_passed = false;
    }
    
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// ============================================================================
// RÉSUMÉ FINAL
// ============================================================================
if ($all_tests_passed) {
    echo "<div class='box success'>";
    echo "<h2>🎉 TOUT FONCTIONNE PARFAITEMENT !</h2>";
    echo "<h3>✅ Les utilisateurs ont été créés avec succès</h3>";
    echo "<h3>✅ Tous les tests de connexion sont passés</h3>";
    echo "<br>";
    echo "<p style='font-size: 18px;'><strong>Vous pouvez maintenant vous connecter avec :</strong></p>";
    echo "<ul style='font-size: 16px;'>";
    echo "<li>👨‍💼 <strong>Admin :</strong> admin@example.com / admin123</li>";
    echo "<li>✍️ <strong>Auteur :</strong> auteur@example.com / auteur123</li>";
    echo "</ul>";
    echo "<div style='padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; margin-top: 20px;'>";
    echo "<h4>⚠️ IMPORTANT - SÉCURITÉ</h4>";
    echo "<p>1. <strong>SUPPRIMEZ</strong> ce fichier <code>fix_all.php</code> immédiatement</p>";
    echo "<p>2. Utilisez maintenant votre page <code>contact.php</code> pour vous connecter</p>";
    echo "</div>";
    echo "<a href='contact.php' class='btn'>🚀 ALLER À LA PAGE DE CONNEXION</a>";
    echo "</div>";
} else {
    echo "<div class='box error'>";
    echo "<h2>❌ PROBLÈME DÉTECTÉ</h2>";
    echo "<p>Certains tests ont échoué. Vérifiez les détails ci-dessus.</p>";
    echo "<p><strong>Que faire ?</strong></p>";
    echo "<ol>";
    echo "<li>Vérifiez que votre fichier <code>database.php</code> est correct</li>";
    echo "<li>Vérifiez que la table <code>utilisateur</code> a la bonne structure</li>";
    echo "<li>Réexécutez ce script</li>";
    echo "</ol>";
    echo "<p>Si le problème persiste, envoyez-moi une capture d'écran de TOUTE cette page.</p>";
    echo "</div>";
}

echo "</body></html>";
?>