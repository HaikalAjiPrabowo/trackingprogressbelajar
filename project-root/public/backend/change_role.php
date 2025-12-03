<?php
session_start();

// hanya admin
if ($_SESSION['role'] !== 'admin') exit("Unauthorized");

require_once __DIR__ . '/../api/src/Utils/DB.php';
use Utils\DB;

$id = $_POST['id'];
$role = $_POST['role'];

$db = DB::conn();

$q = $db->prepare("UPDATE user SET role=? WHERE id=?");
$q->execute([$role, $id]);

header("Location: ../admin_panel.php");
exit;
