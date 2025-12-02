<?php
require_once __DIR__ . '/../api/src/Utils/DB.php';
use Utils\DB;

if (isset($_POST['submit'])) {

    $nama         = $_POST['nama'];
    $tl           = $_POST['tl'];
    $prodi        = $_POST['prodi'];
    $email        = $_POST['email'];
    $password     = $_POST['password'];
    $konfirmasi_pw = $_POST['konfirmasi_pw'];

    // ==========================
    // CEK PASSWORD KONFIRMASI
    // ==========================
    if ($password !== $konfirmasi_pw) {
        echo "<script>
                alert('Password dan konfirmasi password tidak sama!');
                window.history.back();
              </script>";
        exit;
    }

    // ==========================
    // KONEKSI DATABASE (PDO)
    // ==========================
    $db = DB::conn();

    // ==========================
    // CEK EMAIL SUDAH DIPAKAI ?
    // ==========================
    $check = $db->prepare("SELECT id FROM user WHERE Email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        echo "<script>
                alert('Email sudah terdaftar!');
                window.location.href = '../form/register.html';
              </script>";
        exit;
    }

    // ==========================
    // HASH PASSWORD
    // ==========================
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // ==========================
    // INSERT DATA USER
    // ==========================
    $insert = $db->prepare("
        INSERT INTO user (Nama, Tanggal_lahir, Prodi, Email, Password)
        VALUES (?, ?, ?, ?, ?)
    ");

    $ok = $insert->execute([
        $nama,
        $tl,
        $prodi,
        $email,
        $hash
    ]);

    if ($ok) {
        echo "<script>
                alert('Register berhasil! Silakan login.');
                window.location.href = '../form/login.html';
              </script>";
    } else {
        echo "<script>
                alert('Gagal Register! Error database.');
                window.history.back();
              </script>";
    }
}
?>
