<?php 

$user = "root";
$pass = "";

try {
    $db = new PDO("mysql:host=localhost;dbname=blogcms;charset=utf8", $user, $pass);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $req = $db->query("SELECT * FROM article");

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

?>
