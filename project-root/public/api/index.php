<?php
session_start();
require_once __DIR__ . '/src/Utils/db.php';

use Utils\DB;
use Services\GoogleCalendarService;
// ===============================================
// AUTOLOAD CONTROLLERS
// ===============================================
spl_autoload_register(function ($class) {
    $base = __DIR__ . "/src/";
    $path = str_replace("\\", "/", $class) . ".php";
    $full = $base . $path;

    if (file_exists($full)) {
        require $full;
        return;
    }
});

// debug sementara (hapus kalau sudah selesai)
ini_set('display_errors',1);
error_reporting(E_ALL);

// sementara untuk testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

// Headers API
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

// ===============================================
// DEFINISIKAN PATH DULU (INI BAGIAN WAJIB)
// ===============================================
$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

$basePath = "/trackingprogressbelajar/project-root/public/api";

// buang base path
$clean = str_replace($basePath, "", $uri);

// buang query string
$clean = strtok($clean, "?");

// pastikan selalu ada 1 slash di depan
$path = "/" . ltrim($clean, "/");

// fallback jika kosong
if ($path === "//" || $path === "") {
    $path = "/";
}

// ===============================================
// HELPER JSON
// ===============================================
function jsonResponse($data) {
    echo json_encode($data);
    exit;
}

// ===============================================
// CONTROLLER ROUTES
// ===============================================
use Controllers\CourseController;
use Controllers\StudySessionController;

// COURSES
if ($path === "/courses") {
    if ($method === "GET")  (new CourseController())->index();
    if ($method === "POST") (new CourseController())->store();
}

if (preg_match("#^/courses/([0-9]+)$#", $path, $m)) {
    $id = $m[1];
    if ($method === "PUT")    (new CourseController())->update($id);
    if ($method === "DELETE") (new CourseController())->destroy($id);
}

// SESSIONS
if ($path === "/sessions") {
    if ($method === "GET")  (new StudySessionController())->index();
    if ($method === "POST") (new StudySessionController())->store();
}

if (preg_match("#^/sessions/([0-9]+)$#", $path, $m)) {
    $id = $m[1];
    if ($method === "PUT")    (new StudySessionController())->update($id);
    if ($method === "DELETE") (new StudySessionController())->destroy($id);
}

// DEFAULT
jsonResponse(["status"=>"error","message"=>"Route not found: ".$path]);