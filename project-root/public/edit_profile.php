<?php
session_start();
require_once __DIR__ . '/api/src/Utils/DB.php';

use Utils\DB;

if (!isset($_SESSION['user_id'])) {
    header("Location: form/login.html");
    exit;
}

$db = DB::conn();

$user_id = $_SESSION['user_id'];

$q = $db->prepare("SELECT * FROM user WHERE id = ?");
$q->execute([$user_id]);
$user = $q->fetch();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="mb-3">Edit Profil</h3>

            <form action="backend/update_profile.php" method="POST">

                <div class="mb-3">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" 
                           value="<?= htmlspecialchars($user['Nama']) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($user['Email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label>Program Studi</label>
                    <input type="text" name="prodi" class="form-control"
                           value="<?= htmlspecialchars($user['Prodi']) ?>">
                </div>

                <div class="mb-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control"
                           value="<?= htmlspecialchars($user['Tanggal_lahir']) ?>">
                </div>

                <hr>

                <h5>Ganti Password (opsional)</h5>
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah password</small>

                <div class="mb-3 mt-2">
                    <label>Password Lama</label>
                    <input type="password" name="old_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_new_password" class="form-control">
                </div>

                <button class="btn btn-primary mt-3" type="submit">Simpan Perubahan</button>
                <a href="profile.php" class="btn btn-secondary mt-3">Batal</a>

            </form>
        </div>
    </div>
</div>

</body>

</html>
