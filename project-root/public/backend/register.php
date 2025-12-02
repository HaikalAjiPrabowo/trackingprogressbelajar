<?php
use Utils\DB;
require "../api/src/Utils/DB.php";  // Load class DB

$conn = DB::conn();

if (isset($_POST['submit'])) {

    $nama           = $_POST['nama'];
    $tl             = $_POST['tl'];
    $prodi          = $_POST['prodi'];
    $email          = $_POST['email'];
    $password       = $_POST['password'];
    $konfirmasi_pw  = $_POST['konfirmasi_pw'];

    // VALIDASI PASSWORD Minimal 6 karakter

    if (strlen($password) < 6) {
        echo "<script>
                alert('Password minimal 6 karakter!');
                window.history.back();
              </script>";
        exit;
    }

    // Cek password dan konfirmasi
    if ($password !== $konfirmasi_pw) {
        echo "<script>
                alert('Password dan konfirmasi password tidak sama!');
                window.history.back();
              </script>";
        exit;
    }

    // Hash password (setelah lolos validasi)
    $password = password_hash($password, PASSWORD_DEFAULT);

    // CEK EMAIL SUDAH ADA BELUM

    $check = $conn->prepare("SELECT Email FROM user WHERE Email = :email");
    $check->bindParam(":email", $email);
    $check->execute();

    if ($check->rowCount() > 0) {
        echo "<script>
                alert('Email sudah terdaftar!');
                window.location.href = '../form/register.html';
              </script>";
        exit;
    }

    // INSERT KE DATABASE
    
    $sql = "INSERT INTO user (Nama, Tanggal_lahir, Prodi, Email, Password)
            VALUES (:nama, :tl, :prodi, :email, :password)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":nama", $nama);
    $stmt->bindParam(":tl", $tl);
    $stmt->bindParam(":prodi", $prodi);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);

    if ($stmt->execute()) {
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
