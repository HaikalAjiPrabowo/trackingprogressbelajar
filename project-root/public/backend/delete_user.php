<?php
session_start();

if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

require_once __DIR__ . '/../api/src/Utils/DB.php';
use Utils\DB;

$id = $_POST['id'];
$db = DB::conn();

// hapus data terkait
$db->prepare("DELETE FROM courses WHERE user_id=?")->execute([$id]);
$db->prepare("DELETE FROM study_sessions WHERE user_id=?")->execute([$id]);

// hapus user
$db->prepare("DELETE FROM user WHERE id=?")->execute([$id]);

header("Location: ../admin_panel.php");
exit;
