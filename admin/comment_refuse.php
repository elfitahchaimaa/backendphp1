<?php
session_start();
require_once '../php/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql = "UPDATE commantaire
            SET status = 'refuse',
                UPDATED_at = NOW()
            WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
}

header('Location: comments.php?status=en_attente');
exit;