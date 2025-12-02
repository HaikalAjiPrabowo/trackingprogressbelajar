<?php
session_start();
use Utils\DB;
require "../api/src/Utils/DB.php"; 

$pdo = DB::conn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Ambil user berdasarkan email
    $stmt = $pdo->prepare("SELECT id, password FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Email tidak ditemukan
    if (!$user) {
        echo "<script>
                alert('Email tidak terdaftar!');
                window.location='../form/login.html';
              </script>";
        exit;
    }
    
    // Cek password hash
    if (password_verify($password, $user['password'])) {

        session_start();
        $_SESSION['user_id'] = $user['id'];

        echo "<script>
                alert('Login berhasil!');
                window.location='../dashboard.php';
              </script>";
        exit;

    } else {
        echo "<script>
                alert('Email atau password salah!');
                window.location='../form/login.html';
              </script>";
        exit;
    }
}
?>
