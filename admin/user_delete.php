<?php
require_once '../php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM utilisateur WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header('Location: users.php');
exit;