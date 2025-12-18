<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'php/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='fr'><head><meta charset='utf-8'>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 15px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.success { border-left: 4px solid #28a745; }
.error { border-left: 4px solid #dc3545; }
.warning { border-left: 4px solid #ffc107; }
h1 { color: white; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 5px; margin: 0; }
h2 { color: #333; margin-top: 0; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #007bff; color: white; }
.btn { display: inline-block; padding: 15px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 20px; font-size: 18px; }
.btn:hover { background: #218838; }
code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; color: #e83e8c; font-weight: bold; }
</style></head><body>";

echo "<h1>🔧 RÉPARATION COMPLÈTE DU SYSTÈME DE CONNEXION</h1>";

$success_count = 0;
$total_steps = 6;

// ============================================================================
// ÉTAPE 1 : Désactiver les contraintes de clés étrangères
// ============================================================================
echo "<div class='box warning'>";
echo "<h2>1️⃣ Désactivation des contraintes de clés étrangères</h2>";
try {
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    echo "✅ <strong>Contraintes désactivées temporairement</strong><br>";
    echo "<small>Cela permet de supprimer les utilisateurs même s'ils ont des commentaires associés</small>";
    $success_count++;
} catch (Exception $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 2 : Supprimer TOUS les utilisateurs
// ============================================================================
echo "<div class='box error'>";
echo "<h2>2️⃣ Suppression de TOUS les utilisateurs existants</h2>";
try {
    $count = $db->exec("DELETE FROM utilisateur");
    echo "✅ <strong>{$count} utilisateur(s) supprimé(s) avec succès</strong><br>";
    echo "<small>La table utilisateur est maintenant vide</small>";
    $success_count++;
} catch (Exception $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 3 : Réinitialiser l'auto-increment
// ============================================================================
echo "<div class='box warning'>";
echo "<h2>3️⃣ Réinitialisation de l'ID auto-increment</h2>";
try {
    $db->exec("ALTER TABLE utilisateur AUTO_INCREMENT = 1");
    echo "✅ <strong>Compteur ID réinitialisé à 1</strong><br>";
    echo "<small>Les nouveaux utilisateurs commenceront à l'ID 1</small>";
    $success_count++;
} catch (Exception $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 4 : Créer les nouveaux utilisateurs
// ============================================================================
echo "<div class='box success'>";
echo "<h2>4️⃣ Création des nouveaux utilisateurs avec Bcrypt</h2>";

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
echo "<tr><th>Nom</th><th>Email</th><th>Mot de passe</th><th>Rôle</th><th>ID créé</th><th>Statut</th></tr>";

$users_created = 0;

foreach ($nouveaux_users as $user) {
    $hashed = password_hash($user['password'], PASSWORD_BCRYPT);
    
    echo "<tr>";
    echo "<td>{$user['nom']}</td>";
    echo "<td><strong>{$user['email']}</strong></td>";
    echo "<td><strong style='color: #dc3545;'>{$user['password']}</strong></td>";
    echo "<td><strong>{$user['role']}</strong></td>";
    
    try {
        $stmt = $db->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$user['nom'], $user['email'], $hashed, $user['role']]);
        
        if ($result) {
            $last_id = $conn->lastInsertId();
            echo "<td><strong>ID: {$last_id}</strong></td>";
            echo "<td style='background: #d4edda;'>✅ <strong>CRÉÉ</strong></td>";
            $users_created++;
        } else {
            echo "<td>-</td>";
            echo "<td style='background: #f8d7da;'>❌ Échec</td>";
        }
    } catch (Exception $e) {
        echo "<td>-</td>";
        echo "<td style='background: #f8d7da;'>❌ " . $e->getMessage() . "</td>";
    }
    
    echo "</tr>";
}
echo "</table>";

if ($users_created == 2) {
    echo "<br>✅ <strong>2 utilisateurs créés avec succès</strong>";
    $success_count++;
}
echo "</div>";

// ============================================================================
// ÉTAPE 5 : Réactiver les contraintes
// ============================================================================
echo "<div class='box warning'>";
echo "<h2>5️⃣ Réactivation des contraintes de clés étrangères</h2>";
try {
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✅ <strong>Contraintes réactivées</strong><br>";
    echo "<small>La base de données fonctionne normalement maintenant</small>";
    $success_count++;
} catch (Exception $e) {
    echo "❌ <strong>Erreur :</strong> " . $e->getMessage();
}
echo "</div>";

// ============================================================================
// ÉTAPE 6 : Test de connexion en direct
// ============================================================================
echo "<div class='box success'>";
echo "<h2>6️⃣ TEST DE CONNEXION EN DIRECT</h2>";

$test_credentials = [
    ['email' => 'admin@example.com', 'password' => 'admin123', 'role_attendu' => 'admin'],
    ['email' => 'auteur@example.com', 'password' => 'auteur123', 'role_attendu' => 'auteur']
];

echo "<table>";
echo "<tr><th>Email</th><th>Mot de passe</th><th>Trouvé ?</th><th>password_verify()</th><th>Rôle OK ?</th><th>RÉSULTAT</th></tr>";

$all_tests_ok = true;

foreach ($test_credentials as $cred) {
    echo "<tr>";
    echo "<td><strong>{$cred['email']}</strong></td>";
    echo "<td><strong>{$cred['password']}</strong></td>";
    
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
                $all_tests_ok = false;
            }
            
            if ($user['role'] === $cred['role_attendu']) {
                echo "<td style='background: #d4edda;'>✅ {$user['role']}</td>";
            } else {
                echo "<td style='background: #f8d7da;'>❌ {$user['role']}</td>";
                $all_tests_ok = false;
            }
            
            if ($verify && $user['role'] === $cred['role_attendu']) {
                echo "<td style='background: #d4edda; font-weight: bold; font-size: 16px;'>✅ CONNEXION OK</td>";
            } else {
                echo "<td style='background: #f8d7da; font-weight: bold;'>❌ ÉCHEC</td>";
                $all_tests_ok = false;
            }
            
        } else {
            echo "<td style='background: #f8d7da;'>❌ NON</td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td style='background: #f8d7da;'>❌ INTROUVABLE</td>";
            $all_tests_ok = false;
        }
    } catch (Exception $e) {
        echo "<td colspan='4' style='background: #f8d7da;'>❌ " . $e->getMessage() . "</td>";
        $all_tests_ok = false;
    }
    
    echo "</tr>";
}
echo "</table>";

if ($all_tests_ok) {
    $success_count++;
}

echo "</div>";

// ============================================================================
// RÉSUMÉ FINAL
// ============================================================================
if ($success_count == $total_steps) {
    echo "<div class='box success' style='border: 3px solid #28a745;'>";
    echo "<h2 style='color: #28a745; font-size: 28px;'>🎉🎉🎉 SUCCÈS COMPLET ! 🎉🎉🎉</h2>";
    echo "<h3 style='color: #28a745;'>✅ Toutes les étapes ont réussi ({$success_count}/{$total_steps})</h3>";
    echo "<hr>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='margin-top: 0;'>🔐 Identifiants de connexion</h3>";
    echo "<table style='margin: 0;'>";
    echo "<tr><th>Rôle</th><th>Email</th><th>Mot de passe</th></tr>";
    echo "<tr><td><strong>👨‍💼 Admin</strong></td><td><strong>admin@example.com</strong></td><td><strong style='color: #dc3545;'>admin123</strong></td></tr>";
    echo "<tr><td><strong>✍️ Auteur</strong></td><td><strong>auteur@example.com</strong></td><td><strong style='color: #dc3545;'>auteur123</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; border: 2px solid #ffc107; margin: 20px 0;'>";
    echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ IMPORTANT - SÉCURITÉ</h3>";
    echo "<ol style='font-size: 16px; line-height: 1.8;'>";
    echo "<li><strong>SUPPRIMEZ IMMÉDIATEMENT</strong> ce fichier <code>repair_login.php</code></li>";
    echo "<li>Supprimez aussi <code>fix_all.php</code> et <code>fix_final.php</code> s'ils existent</li>";
    echo "<li>Ces fichiers de diagnostic sont dangereux en production</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='text-align: center;'>";
    echo "<a href='contact.php' class='btn' style='font-size: 20px; padding: 20px 40px;'>🚀 ALLER À LA PAGE DE CONNEXION</a>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='box error'>";
    echo "<h2>❌ Problème détecté</h2>";
    echo "<p><strong>Étapes réussies : {$success_count}/{$total_steps}</strong></p>";
    echo "<p>Certaines étapes ont échoué. Vérifiez les messages d'erreur ci-dessus.</p>";
    echo "<hr>";
    echo "<h3>Que faire ?</h3>";
    echo "<ol>";
    echo "<li>Vérifiez que votre fichier <code>database.php</code> est correct</li>";
    echo "<li>Vérifiez que vous avez les droits d'écriture sur la base</li>";
    echo "<li>Essayez de rafraîchir cette page (F5)</li>";
    echo "<li>Si le problème persiste, envoyez-moi une capture d'écran COMPLÈTE</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</body></html>";
?>