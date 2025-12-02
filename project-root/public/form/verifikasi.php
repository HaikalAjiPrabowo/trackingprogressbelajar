<?php
// verifikasi.php (PDO + OOP Version)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../api/src/Utils/DB.php";
use Utils\DB;

if (!isset($_GET['email'])) {
    echo "<script>alert('Akses tidak valid!'); window.location='lupa_pw.html';</script>";
    exit;
}

$email = trim($_GET['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email tidak valid!'); window.location='lupa_pw.html';</script>";
    exit;
}

$errors = [];
$notice = "";

// Jika form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifikasi'])) {

    $kode_input = trim($_POST['kode']);

    if ($kode_input === '') {
        $errors[] = "Masukkan kode OTP.";
    } else {

        // Ambil koneksi PDO
        $conn = DB::conn();

        // Ambil database
        $stmt = $conn->prepare("SELECT reset_code, code_expired FROM user WHERE email = :email");
        $stmt->bindValue(":email", $email);
        $stmt->execute();
        $data = $stmt->fetch();

        if (!$data) {
            $errors[] = "Email tidak ditemukan.";
        } else {
            date_default_timezone_set('Asia/Jakarta');
            $now = date("Y-m-d H:i:s");

            if (empty($data['code_expired']) || $now > $data['code_expired']) {
                $errors[] = "Kode OTP sudah kadaluarsa. Minta kode baru.";
            } else {
                $stored = trim((string)$data['reset_code']);

                if ($kode_input !== $stored) {
                    $errors[] = "Kode OTP salah.";
                } else {
                    // OTP benar → arahkan ke halaman ubah password
                    header("Location: masukan_password.php?email=" . urlencode($email));
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Verifikasi Kode OTP</title>
<link rel="stylesheet" href="forget.css">
</head>
<body>
  <div style="max-width:420px;margin:40px auto;padding:20px;border-radius:8px;background:#fff;box-shadow:0 6px 20px rgba(0,0,0,0.06);">
    <h2>Verifikasi Kode OTP</h2>
    <p>Email: <b><?= htmlspecialchars($email) ?></b></p>

    <?php if (!empty($notice)): ?>
      <div style="color:green;">
        <?= htmlspecialchars($notice) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div style="background:#ffe8e8;color:#b30000;padding:10px;border-radius:6px;margin-bottom:12px;">
        <?php foreach ($errors as $err): ?>
            <div>- <?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label for="kode">Masukkan Kode OTP</label>
      <input id="kode" name="kode" type="text" maxlength="6" required 
             style="width:100%;padding:8px;margin:8px 0;">

      <button type="submit" name="verifikasi" style="padding:10px 14px;width:100%;">Verifikasi</button>
    </form>

    <p style="margin-top:12px;font-size:13px;color:#666;">
      Jika kode tidak diterima, kembali ke <a href="lupa_pw.html">minta kode baru</a>.
    </p>
  </div>
</body>
</html>
