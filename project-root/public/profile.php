<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: form/login.html");
    exit;
}

$nama = $_SESSION['nama'] ?? '';
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow-sm">
        <div class="card-body">

            <h3 class="mb-3">Profil Saya</h3>

            <div class="text-center mb-3">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama) ?>&size=120&background=random"
                    class="rounded-circle border">
            </div>

            <table class="table table-bordered">
                <tr>
                    <th>Nama Lengkap</th>
                    <td><?= htmlspecialchars($nama) ?></td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($email) ?></td>
                </tr>

                <tr>
                    <th>Program Studi</th>
                    <td><?= $_SESSION['prodi'] ?? '-' ?></td>
                </tr>

                <tr>
                    <th>Tanggal Lahir</th>
                    <td><?= $_SESSION['tanggal_lahir'] ?? '-' ?></td>
                </tr>
            </table>
            <a href="edit_profile.php" class="btn btn-primary mt-3">
    <i class="bi bi-pencil-square me-2"></i>Edit Profil
</a>
            <a href="dashboard.php" class="btn btn-secondary mt-3">Kembali</a>
        </div>
    </div>

</div>

</body>
</html>
