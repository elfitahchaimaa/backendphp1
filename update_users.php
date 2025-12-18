<?php
require_once 'php/config.php';

echo "<!DOCTYPE html>";
echo "<html lang='fr'><head><meta charset='utf-8'>";
echo "<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
.success { border-left-color: #28a745; }
h1 { color: #333; }
ul { list-style: none; padding: 0; }
li { padding: 5px 0; }
</style></head><body>";

echo "<h1>Création des utilisateurs</h1>";

// Supprimer tous les utilisateurs existants
try {
    $conn->exec("DELETE FROM utilisateur");
    echo "<div class='box'>";
    echo " Tous les anciens utilisateurs ont été supprimés<br>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div class='box' style='border-left-color: #dc3545;'>";
    echo " Erreur lors de la suppression : " . $e->getMessage() . "<br>";
    echo "</div>";
}

// Créer les nouveaux utilisateurs
$users = [
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

echo "<div class='box success'>";
echo "<h2>👥 Création des nouveaux utilisateurs</h2>";

foreach ($users as $user) {
    // Hash du mot de passe avec Bcrypt
    $hashed_password = password_hash($user['password'], PASSWORD_BCRYPT);
    
    try {
        $stmt = $conn->prepare("INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['nom'], $user['email'], $hashed_password, $user['role']]);
        
        echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<strong>{$user['nom']}</strong><br>";
        echo "<ul>";
        echo "<li>Email: <strong>{$user['email']}</strong></li>";
        echo "<li> Mot de passe: <strong>{$user['password']}</strong></li>";
        echo "<li> Rôle: <strong>{$user['role']}</strong></li>";
        echo "<li> Hash: " . substr($hashed_password, 0, 40) . "...</li>";
        echo "</ul>";
        echo "</div>";
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo " Erreur pour {$user['email']}: " . $e->getMessage();
        echo "</div>";
    }
}
echo "</div>";

// Vérifier les utilisateurs créés
echo "<div class='box'>";
echo "<h2> Vérification des utilisateurs créés</h2>";
try {
    $stmt = $conn->query("SELECT id, nom, email, role, LEFT(mot_de_passe, 30) as hash_preview FROM utilisateur");
    $users_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users_db) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #007bff; color: white;'>";
        echo "<th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Hash (aperçu)</th>";
        echo "</tr>";
        
        foreach ($users_db as $u) {
            echo "<tr>";
            echo "<td>{$u['id']}</td>";
            echo "<td>{$u['nom']}</td>";
            echo "<td><strong>{$u['email']}</strong></td>";
            echo "<td><strong>{$u['role']}</strong></td>";
            echo "<td style='font-size: 10px;'>{$u['hash_preview']}...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo " Erreur : " . $e->getMessage();
}
echo "</div>";

// Test de connexion
echo "<div class='box success'>";
echo "<h2> Test de connexion automatique</h2>";

$test_credentials = [
    ['email' => 'admin@example.com', 'password' => 'admin123'],
    ['email' => 'auteur@example.com', 'password' => 'auteur123']
];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background: #28a745; color: white;'>";
echo "<th>Email testé</th><th>Mot de passe</th><th>Résultat</th>";
echo "</tr>";

foreach ($test_credentials as $cred) {
    try {
        $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$cred['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($cred['password'], $user['mot_de_passe'])) {
            echo "<tr style='background: #d4edda;'>";
            echo "<td><strong>{$cred['email']}</strong></td>";
            echo "<td><strong>{$cred['password']}</strong></td>";
            echo "<td> <strong>CONNEXION RÉUSSIE</strong></td>";
            echo "</tr>";
        } else {
            echo "<tr style='background: #f8d7da;'>";
            echo "<td>{$cred['email']}</td>";
            echo "<td>{$cred['password']}</td>";
            echo "<td> <strong>ÉCHEC</strong></td>";
            echo "</tr>";
        }
    } catch (Exception $e) {
        echo "<tr style='background: #f8d7da;'>";
        echo "<td>{$cred['email']}</td>";
        echo "<td>{$cred['password']}</td>";
        echo "<td> Erreur: {$e->getMessage()}</td>";
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

// Message final
echo "<div class='box success'>";
echo "<h2> Configuration terminée !</h2>";
echo "<h3>Vous pouvez maintenant vous connecter avec :</h3>";
echo "<ul style='font-size: 18px;'>";
echo "<li> <strong>Admin :</strong> admin@example.com / admin123</li>";
echo "<li> <strong>Auteur :</strong> auteur@example.com / auteur123</li>";
echo "</ul>";
echo "<div style='padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; margin-top: 20px;'>";
echo " <strong>IMPORTANT :</strong> Supprimez ce fichier <code>update_users.php</code> pour des raisons de sécurité !";
echo "</div>";
echo "<p style='margin-top: 20px;'>";
echo "<a href='contact.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 18px;'>🚀 ALLER À LA PAGE DE CONNEXION</a>";
echo "</p>";
echo "</div>";

echo "</body></html>";
?>