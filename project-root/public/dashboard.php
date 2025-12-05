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

    <!-- CUSTOM PREMIUM SIDEBAR STYLE -->
    <style>
        body {
            background: #f4f6fc;
            font-family: 'Inter', sans-serif;
        }

        /* NAVBAR */
/* NAVBAR PREMIUM */
        .navbar-custom {
            background: #3b3bc4;
            background: linear-gradient(90deg, rgba(59, 59, 196, 1) 35%, rgba(21, 176, 207, 1) 100%);
            padding: 14px 25px;
            box-shadow: 0 4px 20px rgba(0, 80, 255, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(6px);
            position: sticky;
            top: 0;
            z-index: 1050;
            transition: all 0.25s ease;
        }

/* Brand */
        .navbar-custom .navbar-brand {
            color: #ffffff !important;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

/* Dropdown toggle styling */
        .navbar-custom .dropdown-toggle {
            color: #ffffff !important;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 10px;
            transition: 0.25s ease;
    }

        .navbar-custom .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

/* Avatar effect */
        .navbar-custom img {
            border: 2px solid rgba(255, 255, 255, 0.7);
            transition: 0.25s ease;
        }

        .navbar-custom img:hover {
            border-color: #ffffff;
            transform: scale(1.05);
        }

/* Mobile button */
        .navbar-custom .btn-light {
            border: none;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
        }

        .navbar-custom .btn-light:hover {
            background: rgba(255, 255, 255, 0.35);
        }

/* PREMIUM PROFILE CARD */
        .profile-card {
            width: 210px;
            max-width: 210px;
            overflow: hidden;
            background: #f8faff;
            border-radius: 16px;
            padding: 18px 20px;
            margin: 0 14px 10px 14px;
            margin-top: -20px; 
            display: flex;
            align-items: center;
            gap: 14px;
            border: 1px solid rgba(0, 102, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 80, 200, 0.07);
            transition: 0.25s ease;
            display: flex;
            align-items: center;
        }

/* Hover effect */
        .profile-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 80, 200, 0.12);
        }

/* Avatar ring */
        .profile-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 3px solid #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.25);
            transition: 0.25s ease;
        }

        .profile-card:hover .profile-avatar {
            transform: scale(1.06);
            box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.45);
        }

/* Name + email styling */
        .profile-info .name {
            font-size: 15px;
            font-weight: 700;
            color: #1f2937;
        }

        .profile-info .email {
            font-size: 13px;
            color: #6b7280;
            margin-top: -2px;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            min-width: 0;                /* Wajib untuk wrap */
        }

        .profile-info .email {
            font-size: 13px;
            color: #6b7280;
            white-space: normal;         /* Biar turun ke bawah */
            word-break: break-word;      /* Biar huruf patah & wrap */
            max-width: 150px;            /* Biar tetap rapi */
        }




        /* ===== SIDEBAR PREMIUM (BLUE BRANDING STYLE B) ===== */
        /* Sidebar — kurangi padding agar profile card naik */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 30px;      
            z-index: 1040;
            border-right: 1px solid #dce3f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        /* === Profile Area (Point 1) === */
        .sidebar .profile-box {
            padding: 15px 20px;
            margin: 0 14px 10px;
            border-radius: 12px;
            background: #f0f6ff;
            box-shadow: inset 0 0 0 1px #d8e6ff;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .sidebar .profile-box img {
            border-radius: 50%;
        }

        .sidebar .profile-box .name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: -2px;
        }

        .sidebar .profile-box .email {
            font-size: 12px;
            opacity: 0.7;
        }

        /* Title Section (Point 2) */
        .sidebar .menu-title {
            padding: 5px 25px;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            color: #8aa0c4;
            opacity: 0.9;
            margin-top: 8px;
        }

        /* Normal Link */
        .sidebar a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 20px;
            color: #294265;
            font-weight: 500;
            border-radius: 12px;
            margin: 0 14px;
            transition: all 0.25s ease;
            text-decoration: none;
            font-size: 15px;
        }

        /* Icon (Point 4) */
        .sidebar a i {
            font-size: 18px;
            opacity: 0.75;
        }

        /* Hover effect (Point 6) */
        .sidebar a:hover {
            background: #eaf3ff;
            color: #0066ff;
            transform: translateX(4px);
            box-shadow: 0 3px 10px rgba(0, 102, 255, 0.15);
        }

        /* Active link (Point 3) */
        .sidebar a.active {
            background: rgba(0, 102, 255, 0.15);
            color: #0066ff !important;
            font-weight: 700;
            border-left: 4px solid #0066ff;
            transform: translateX(4px);
            box-shadow: inset 0 0 0 1px rgba(0, 102, 255, 0.25);
        }

        .sidebar a.active i {
            color: #0066ff !important;
            opacity: 1;
        }

        /* ADMIN LINK */
        .sidebar a.text-danger {
            color: #d9534f !important;
        }

        .sidebar a.text-danger:hover {
            background: #ffecef;
            color: #c9302c !important;
        }

        /* Divider */
        .sidebar .menu-title:not(:first-child) {
            border-top: 1px solid #e6eaf1;
            padding-top: 14px;
        }

        /* Spacing airy (Point 5) */
        .sidebar a,
        .sidebar .menu-title {
            letter-spacing: 0.2px;
        }

        /* RESPONSIVE (Point 7) */
        @media (max-width: 991px) {
            .sidebar {
                display: none;
            }
        }

        body.hasSidebar {
            margin-left: 250px;
        }

        @media (max-width: 991px) {
            body.hasSidebar {
                margin-left: 0;
            }
        }

        /* CARDS STYLE */
        .card {
            border-radius: 14px;
            border: none;
        }

        .card-header {
            border-radius: 14px 14px 0 0 !important;
        }
    </style>
</head>

<body class="bg-light hasSidebar">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm px-3">
    <div class="container-fluid">

        <!-- HAMBURGER MOBILE -->
        <button class="btn btn-light d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="bi bi-list"></i>
        </button>

        <a class="navbar-brand fw-bold" href="#">TrackStudy</a>

        <div class="dropdown ms-auto me-3">
            <button class="btn btn-white dropdown-toggle d-flex align-items-center"
                type="button" data-bs-toggle="dropdown">

                <img src="https://ui-avatars.com/api/?name=<?= urlencode($namaUser) ?>&background=random"
                    width="36" height="36" class="rounded-circle me-2">

                <span><?= htmlspecialchars($namaUser) ?></span>
                <span class="badge bg-primary ms-2"><?= $_SESSION['role'] ?></span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-circle me-2"></i>Profil Saya</a></li>
                <li><a class="dropdown-item" href="settings.php"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
            </ul>
        </div>

    </div>
</nav>

<!-- SIDEBAR DESKTOP -->
<div class="sidebar d-none d-lg-block">

    <!-- PROFILE AREA (Point 1) -->
    <div class="profile-card">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($namaUser) ?>&background=random"
            class="profile-avatar"  width="48" height="48" >

        <div class="profile-info">
            <div class="name"><?= htmlspecialchars($namaUser) ?></div>
            <div class="email"><?= htmlspecialchars($emailUser) ?></div>
        </div>
    </div>

    <div class="menu-title">Menu Utama</div>

    <a href="#" class="active">
        <i class="bi bi-speedometer2"></i> Home
    </a>

    <a href="#">
        <i class="bi bi-book"></i> Mata Kuliah
    </a>

    <a href="#">
        <i class="bi bi-clock-history"></i> Sesi Belajar
    </a>

    <a href="#">
        <i class="bi bi-calendar3"></i> Kalender
    </a>

    <?php if ($_SESSION['role'] === 'admin'): ?>
    <div class="menu-title">Admin</div>
    <a href="admin_panel.php" class="text-danger">
        <i class="bi bi-shield-lock"></i> Admin Panel
    </a>
    <?php endif; ?>

</div>

<!-- SIDEBAR MOBILE -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Menu</h5>
        <button class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <a href="#" class="d-block mb-2"><i class="bi bi-speedometer2 me-2"></i> Home</a>
        <a href="#" class="d-block mb-2"><i class="bi bi-book me-2"></i> Mata Kuliah</a>
        <a href="#" class="d-block mb-2"><i class="bi bi-clock-history me-2"></i> Sesi Belajar</a>
        <a href="#" class="d-block mb-2"><i class="bi bi-calendar3 me-2"></i> Kalender</a>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="admin_panel.php" class="d-block text-danger"><i class="bi bi-shield-lock me-2"></i> Admin Panel</a>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================= -->
<!-- MAIN CONTENT (TIDAK DIUBAH SEDIKITPUN) -->
<!-- ========================================= -->

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
