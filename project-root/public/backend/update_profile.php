<?php
session_start();
require_once __DIR__ . '/../api/src/Utils/DB.php';

use Utils\DB;

if (!isset($_SESSION['user_id'])) {
    header("Location: form/login.html");
    exit;
}

$db = DB::conn();

$user_id = $_SESSION['user_id'];

// Ambil data dari form
$nama     = $_POST['nama'];
$email    = $_POST['email'];
$prodi    = $_POST['prodi'];
$tgl      = $_POST['tanggal_lahir'];

$old_pw   = $_POST['old_password'];
$new_pw   = $_POST['new_password'];
$confirm  = $_POST['confirm_new_password'];

// =========================
// VALIDASI EMAIL SUDAH ADA?
// =========================
$check = $db->prepare("SELECT id FROM user WHERE Email = ? AND id != ?");
$check->execute([$email, $user_id]);
if ($check->fetch()) {
    echo "<script>alert('Email sudah digunakan!'); window.history.back();</script>";
    exit;
}

// =========================
// UPDATE DATA PROFIL
// =========================
$update = $db->prepare("
    UPDATE user SET Nama=?, Email=?, Prodi=?, Tanggal_lahir=? WHERE id=?
");

$update->execute([$nama, $email, $prodi, $tgl, $user_id]);

// =========================
// HANDLE PERGANTIAN PASSWORD
// =========================
if (!empty($old_pw) || !empty($new_pw) || !empty($confirm)) {

    // Ambil password lama
    $q = $db->prepare("SELECT Password FROM user WHERE id=?");
    $q->execute([$user_id]);
    $user = $q->fetch();

    if (!password_verify($old_pw, $user['Password'])) {
        echo "<script>alert('Password lama salah!'); window.history.back();</script>";
        exit;
    }

    if ($new_pw !== $confirm) {
        echo "<script>alert('Password baru tidak sama!'); window.history.back();</script>";
        exit;
    }

    // Update password baru
    $hash = password_hash($new_pw, PASSWORD_DEFAULT);

    $q2 = $db->prepare("UPDATE user SET Password=? WHERE id=?");
    $q2->execute([$hash, $user_id]);
}

// =========================
// UPDATE SESSION
// =========================
$_SESSION['nama'] = $nama;
$_SESSION['email'] = $email;
$_SESSION['prodi'] = $prodi;
$_SESSION['tanggal_lahir'] = $tgl;

echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='../profile.php';</script>";
exit;
