<?php
session_start();

// FIX PATH
require_once __DIR__. '/../api/src/Utils/DB.php';
use Utils\DB;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $conn = DB::conn();

    $stmt = $conn->prepare("SELECT * FROM user WHERE Email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row) {
        echo "<script>
                alert('Email tidak terdaftar!');
                window.location='../form/login.html';
              </script>";
        exit;
    }
}

    // cek password benar
    if (!password_verify($password, $row['Password'])) {
        echo "<script>
                alert('Email atau password salah!');
                window.location='../form/login.html';
              </script>";
        exit;
    }

    // ✔ SET SESSION — WAJIB
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['role']    = $row['role'];  // Wajib untuk role
    $_SESSION['nama']    = $row['Nama'];
    $_SESSION['email'] = $row['Email'];
    $_SESSION['prodi'] = $row['Prodi'] ?? '';
    $_SESSION['tanggal_lahir'] = $row['Tanggal_lahir'] ?? '';


    // redirect dashboard
    header("Location: ../dashboard.php");
    exit;


?>