<?php
session_start();

// ❗ BATASAN AKSES: hanya ADMIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_panel.php");
    exit;
}

require_once __DIR__ . '/api/src/Utils/DB.php';
use Utils\DB;

$db = DB::conn();

// Ambil semua user (tanpa created_at karena tabel tidak punya)
$users = $db->query("SELECT id, Nama, Email, role FROM user ORDER BY id DESC")->fetchAll();

// Hitung total courses & sessions tiap user
$courseCounts = $db->query("SELECT user_id, COUNT(*) as total FROM courses GROUP BY user_id")->fetchAll();
$sessionCounts = $db->query("SELECT user_id, COUNT(*) as total FROM study_sessions GROUP BY user_id")->fetchAll();

// Bikin map supaya mudah dipakai
$coursesMap = [];
foreach ($courseCounts as $c) $coursesMap[$c['user_id']] = $c['total'];

$sessionsMap = [];
foreach ($sessionCounts as $s) $sessionsMap[$s['user_id']] = $s['total'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">
        <span class="navbar-brand fw-bold">Admin Panel — TrackStudy</span>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container py-4">

    <h3 class="mb-3">Daftar Pengguna</h3>

    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Total Mata Kuliah</th>
                <th>Total Sesi Belajar</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['Nama']) ?></td>
                <td><?= htmlspecialchars($u['Email']) ?></td>

                <td>
                    <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?>">
                        <?= $u['role'] ?>
                    </span>
                </td>

                <td><?= $coursesMap[$u['id']] ?? 0 ?></td>
                <td><?= $sessionsMap[$u['id']] ?? 0 ?></td>

                <td>
                    <form method="post" action="backend/change_role.php" class="d-inline">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <select name="role" class="form-select form-select-sm w-auto d-inline">
                            <option value="siswa" <?= $u['role']=='siswa'?'selected':'' ?>>siswa</option>
                            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>admin</option>
                        </select>
                        <button class="btn btn-sm btn-success">Simpan</button>
                    </form>

                    <form method="post" action="backend/delete_user.php" class="d-inline"
                        onsubmit="return confirm('Hapus user ini? Semua data mata kuliah & sesi akan ikut terhapus!');">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </td>

            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

</body>
</html>
