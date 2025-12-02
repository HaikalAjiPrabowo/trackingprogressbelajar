<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);

    // cek password benar
    if (!password_verify($password, $row['Password'])) {
        echo "Password salah";
        exit;
    }

    // ✔ SET SESSION — WAJIB
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['nama'] = $row['Nama'];
    $_SESSION['email'] = $row['Email'];
    $_SESSION['prodi'] = $row['Prodi'] ?? '';
    $_SESSION['tanggal_lahir'] = $row['Tanggal_lahir'] ?? '';


    // redirect dashboard
    header("Location: ../dashboard.php");
    exit;
}

?>
