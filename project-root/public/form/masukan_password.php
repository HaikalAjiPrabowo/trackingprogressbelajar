<?php
// masukan_password.php
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../api/src/Utils/DB.php"; 
use Utils\DB;

// ambil koneksi PDO
$conn = DB::conn();

// validasi query param email
if (!isset($_GET['email'])) {
    echo "<script>alert('Akses tidak valid.'); window.location='lupa_pw.html';</script>";
    exit;
}

$email = trim($_GET['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email tidak valid.'); window.location='lupa_pw.html';</script>";
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password  = trim($_POST['password'] ?? '');
    $password2 = trim($_POST['password2'] ?? '');

    // Validasi dasar
    if ($password === '' || $password2 === '') {
        $errors[] = "Masukkan password dan konfirmasi password.";
    } elseif ($password !== $password2) {
        $errors[] = "Password dan konfirmasi tidak cocok.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    if (empty($errors)) {
        // Ambil reset_code
        $stmt = $conn->prepare("SELECT reset_code FROM user WHERE email = ?");
        $stmt->execute([$email]);
        $r = $stmt->fetch();

        if (!$r) {
            $errors[] = "Email tidak ditemukan.";
        } elseif (empty($r['reset_code'])) {
            $errors[] = "Token reset tidak valid atau sudah digunakan.";
        } else {
            // Update password
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt2 = $conn->prepare(
                "UPDATE user 
                 SET password = ?, reset_code = NULL, code_expired = NULL 
                 WHERE email = ?"
            );

            $ok = $stmt2->execute([$hash, $email]);

            if ($ok && $stmt2->rowCount() > 0) {
                echo "<script>
                        alert('Password berhasil diubah. Silakan login.');
                        window.location='login.html';
                      </script>";
                exit;
            } else {
                $errors[] = "Gagal menyimpan password. Silakan coba lagi.";
            }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masukkan Password Baru</title>
<link rel="stylesheet" href="forget.css">
</head>
<body>
  <div style="max-width:420px;margin:40px auto;padding:20px;border-radius:8px;background:#fff;box-shadow:0 6px 20px rgba(0,0,0,0.06);">
    <h2>Masukkan Password Baru</h2>
    <p>Email: <b><?= htmlspecialchars($email) ?></b></p>

    <?php if (!empty($errors)): ?>
      <div style="background:#fff0f0;color:#900;padding:10px;border-radius:6px;margin-bottom:10px;">
        <?php foreach ($errors as $err) echo "<div>- ".htmlspecialchars($err)."</div>"; ?>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <label for="password">Password baru</label><br>
      <input type="password" id="password" name="password" required style="width:100%;padding:8px;margin:8px 0;"><br>

      <label for="password2">Konfirmasi password</label><br>
      <input type="password" id="password2" name="password2" required style="width:100%;padding:8px;margin:8px 0;"><br>

      <button type="submit" style="padding:10px 14px;width:100%;">Simpan Password Baru</button>
    </form>

    <p style="margin-top:12px;font-size:13px;color:#666;">Jika ada masalah, kembali ke <a href="login.html">minta kode baru</a>.</p>
  </div>
</body>
</html>