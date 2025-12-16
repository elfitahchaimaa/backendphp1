<?php
require_once '../php/config.php';
require_once '../php/auth.php';
require_role(['auteur','admin']);

$uid = (int)$_SESSION['user']['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM article WHERE id = :id AND idutil = :uid");
    $stmt->execute([':id' => $id, ':uid' => $uid]);
}

header('Location: articles.php');
exit;