<?php

namespace Controllers;

use Utils\DB;

class CourseController {

    public function index() {
        $user_id = $_SESSION['user_id'];

        $db = DB::conn();
        $q = $db->prepare("SELECT * FROM courses WHERE user_id = ?");
        $q->execute([$user_id]);

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
        $data = json_decode(file_get_contents("php://input"), true);

        $q = DB::conn()->prepare("
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

        jsonResponse(["status" => "success"]);
    }

    public function destroy($id) {
        $user_id = $_SESSION['user_id'];

        $q = DB::conn()->prepare("
            DELETE FROM courses WHERE id=? AND user_id=?
        ");

        $q->execute([$id, $user_id]);

        jsonResponse(["status" => "success"]);
    }
}
