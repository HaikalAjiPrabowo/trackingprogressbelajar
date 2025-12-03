<?php

namespace Controllers;

use Utils\DB;

class CourseController {

public function index() {
    $user_id = $_SESSION['user_id'];
    $role    = $_SESSION['role'];
    $db      = DB::conn();

    if ($role === 'admin') {
        // admin bisa melihat semua course
        $q = $db->query("SELECT * FROM courses");
    } else {
        // siswa hanya miliknya sendiri
        $q = $db->prepare("SELECT * FROM courses WHERE user_id = ?");
        $q->execute([$user_id]);
    }

    jsonResponse(["status" => "success", "data" => $q->fetchAll()]);
}
public function store() {
    $user_id = $_SESSION['user_id'];

    $data = json_decode(file_get_contents("php://input"), true);

    $q = DB::conn()->prepare("
        INSERT INTO courses (user_id, code, title, category, color)
        VALUES (?, ?, ?, ?, ?)
    ");

    $q->execute([
        $user_id,
        $data['code'],
        $data['title'],
        $data['category'],
        $data['color']
    ]);

    jsonResponse(["status" => "success"]);
}

public function update($id) {
    $user_id = $_SESSION['user_id'];
    $role    = $_SESSION['role'];

    $data = json_decode(file_get_contents("php://input"), true);

    $db = DB::conn();

    if ($role === 'admin') {
        // admin bebas edit apa saja
        $q = $db->prepare("
            UPDATE courses SET code=?, title=?, category=?, color=? WHERE id=?
        ");
        $q->execute([
            $data['code'],
            $data['title'],
            $data['category'],
            $data['color'],
            $id
        ]);
    } else {
        // siswa hanya edit miliknya
        $q = $db->prepare("
            UPDATE courses 
            SET code=?, title=?, category=?, color=?
            WHERE id=? AND user_id=?
        ");
        $q->execute([
            $data['code'],
            $data['title'],
            $data['category'],
            $data['color'],
            $id,
            $user_id
        ]);
    }

    jsonResponse(["status" => "success"]);
}

public function destroy($id) {
    $user_id = $_SESSION['user_id'];
    $role    = $_SESSION['role'];
    $db      = DB::conn();

    if ($role === 'admin') {
        $q = $db->prepare("DELETE FROM courses WHERE id=?");
        $q->execute([$id]);
    } else {
        $q = $db->prepare("DELETE FROM courses WHERE id=? AND user_id=?");
        $q->execute([$id, $user_id]);
    }

    jsonResponse(["status" => "success"]);
}
}
