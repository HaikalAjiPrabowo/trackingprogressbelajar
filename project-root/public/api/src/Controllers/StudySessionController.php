<?php

namespace Controllers;

use Utils\DB;

class StudySessionController {

    public function index() {
        $user_id = $_SESSION['user_id'];

        $q = DB::conn()->prepare("SELECT * FROM study_sessions WHERE user_id = ?");
        $q->execute([$user_id]);

        jsonResponse(["status" => "success", "data" => $q->fetchAll()]);
    }

    public function store() {
        $user_id = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);

        $q = DB::conn()->prepare("
            INSERT INTO study_sessions (user_id, course_id, started_at, ended_at, effective_minutes, note)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $q->execute([
            $user_id,
            $data['course_id'],
            $data['started_at'],
            $data['ended_at'],
            $data['effective_minutes'],
            $data['note']
        ]);

        jsonResponse(["status" => "success"]);
    }

    public function update($id) {
        $user_id = $_SESSION['user_id'];
        $data = json_decode(file_get_contents("php://input"), true);

        $q = DB::conn()->prepare("
            UPDATE study_sessions
            SET course_id=?, started_at=?, ended_at=?, effective_minutes=?, note=?
            WHERE id=? AND user_id=?
        ");

        $q->execute([
            $data['course_id'],
            $data['started_at'],
            $data['ended_at'],
            $data['effective_minutes'],
            $data['note'],
            $id,
            $user_id
        ]);

        jsonResponse(["status" => "success"]);
    }

    public function destroy($id) {
        $user_id = $_SESSION['user_id'];

        $q = DB::conn()->prepare("
            DELETE FROM study_sessions WHERE id=? AND user_id=?
        ");

        $q->execute([$id, $user_id]);

        jsonResponse(["status" => "success"]);
    }
}
