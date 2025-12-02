<?php
session_start();
$namaUser = $_SESSION['nama'] ?? 'User';
$emailUser = $_SESSION['email'] ?? '-';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Tracking Progress Belajar — Dashboard</title>

    <!-- CSS -->
    <link rel="stylesheet" href="/trackingprogressbelajar/project-root/public/assets/css/styles.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">TrackStudy</a>
<div class="dropdown">
    <button class="btn btn-white dropdown-toggle d-flex align-items-center" 
            type="button" 
            data-bs-toggle="dropdown">

        <img src="https://ui-avatars.com/api/?name=<?= urlencode($namaUser) ?>&background=random"
            width="36" height="36" 
            class="rounded-circle me-2">

        <span><?= htmlspecialchars($namaUser) ?></span>
    </button>

    <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php">
            <i class="bi bi-person-circle me-2"></i>Profil Saya
        </a></li>

        <li><a class="dropdown-item" href="settings.php">
            <i class="bi bi-gear me-2"></i>Pengaturan
        </a></li>

        <li><hr class="dropdown-divider"></li>

        <li><a class="dropdown-item text-danger" href="logout.php">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
        </a></li>
    </ul>
</div>
    </div>
</nav>

<main class="container my-4">

    <!-- TOP CARDS -->
    <div class="d-flex gap-3 mb-3 flex-column flex-md-row">
        <div class="card flex-fill shadow-sm">
            <div class="card-body">
                <h6>Total Jam Belajar (Minggu Ini)</h6>
                <h2 id="card-total-hours" class="fw-bold">0</h2>
                <p class="text-muted">Jam efektif dihitung dari effective minutes</p>
            </div>
        </div>

        <div class="card flex-fill shadow-sm">
            <div class="card-body">
                <h6>Rata-rata Efektivitas</h6>
                <h2 id="card-effective-rate" class="fw-bold">0%</h2>
                <p class="text-muted">Effective minutes / total minutes</p>
            </div>
        </div>

        <div class="card shadow-sm" style="min-width:240px">
            <div class="card-body">
                <h6>Aksi Cepat</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCourse">
                        <i class="bi bi-plus-lg me-2"></i>Tambahkan Mata Kuliah
                    </button>

                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAddSession">
                        <i class="bi bi-clock-history me-2"></i>Tambah Sesi
                    </button>

                    <button id="btn-export-csv" class="btn btn-outline-success">
                        <i class="bi bi-download me-2"></i>Unduh Laporan CSV
                    </button>

                    <button id="btnSyncGoogle" class="btn btn-danger">
                        <i class="bi bi-calendar3"></i> Hubungkan Google Calendar
                    </button>

                </div>
            </div>
        </div>
    </div>

    <!-- POMODORO TIMER CARD -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">

            <h5 class="fw-bold">Pomodoro Timer</h5>
            <p class="text-muted mb-1" id="pomo-mode">Mode: Fokus</p>

            <h1 id="pomo-display" class="fw-bold" style="font-size:48px;">25:00</h1>

            <div class="d-flex justify-content-center gap-2 mt-3">
                <button id="pomo-start" class="btn btn-success">
                    <i class="bi bi-play-fill"></i> Mulai
                </button>

                <button id="pomo-pause" class="btn btn-warning">
                    <i class="bi bi-pause-fill"></i> Jeda
                </button>

                <button id="pomo-reset" class="btn btn-danger">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>

            <div class="mt-3">
                <label class="form-label">Pilih Mata Kuliah</label>
                <select id="pomo-course" class="form-select w-auto mx-auto"></select>
            </div>

            <p class="text-muted small mt-2">Sesi otomatis disimpan setelah mode fokus selesai.</p>
        </div>
    </div>

    <!-- CHART ROW -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6>Jam Belajar Per Hari</h6>
                    <canvas id="timeSeriesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST ROW -->
    <div class="row g-3">

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Mata Kuliah</strong>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddCourse">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <ul id="coursesList" class="list-group list-group-flush"></ul>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <strong>Sesi Belajar Terbaru</strong>
                    <small class="text-muted">klik sesi untuk edit</small>
                </div>

                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Efektif (menit)</th>
                        <th>Catatan</th>
                        <th></th>
                    </tr>
                    </thead>

                    <tbody id="sessionsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<!-- MODAL ADD COURSE -->
<div class="modal fade" id="modalAddCourse" tabindex="-1">
    <div class="modal-dialog">
        <form id="formCourse" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input type="hidden" id="courseId">

                <div class="mb-3">
                    <label>Kode</label>
                    <input type="text" id="courseCode" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nama Mata Kuliah</label>
                    <input type="text" id="courseTitle" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kategori</label>
                    <input type="text" id="courseCategory" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Warna</label>
                    <input type="color" id="courseColor" class="form-control form-control-color" value="#4CAF50">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL ADD SESSION -->
<div class="modal fade" id="modalAddSession" tabindex="-1">
    <div class="modal-dialog">
        <form id="formSession" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Sesi Belajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="sessionId">

                <div class="mb-3">
                    <label>Mata Kuliah</label>
                    <select id="sessionCourse" class="form-select" required></select>
                </div>

                <div class="mb-3">
                    <label>Mulai</label>
                    <input type="datetime-local" id="sessionStart" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Selesai</label>
                    <input type="datetime-local" id="sessionEnd" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Menit Efektif</label>
                    <input type="number" id="sessionEffective" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Catatan</label>
                    <textarea id="sessionNote" class="form-control"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>

        </form>
    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
<script src="/trackingprogressbelajar/project-root/public/assets/js/app.js" defer></script>

</body>
</html>
