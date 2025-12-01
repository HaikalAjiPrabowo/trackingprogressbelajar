<?php
namespace Models;

use Utils\DB;

class StudySession {

    public static function all($user_id) {
        $stmt = DB::conn()->prepare("SELECT * FROM study_sessions WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function create($user_id, $data) {
        $stmt = DB::conn()->prepare("
            INSERT INTO study_sessions (user_id, course_id, started_at, ended_at, effective_minutes, note)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $data['course_id'],
            $data['started_at'],
            $data['ended_at'],
            $data['effective_minutes'],
            $data['note']
        ]);
        return DB::conn()->lastInsertId();
    }

    public static function update($id, $user_id, $data) {
        $stmt = DB::conn()->prepare("
            UPDATE study_sessions SET
            course_id=?, started_at=?, ended_at=?, effective_minutes=?, note=?
            WHERE id=? AND user_id=?
        ");
        return $stmt->execute([
            $data['course_id'],
            $data['started_at'],
            $data['ended_at'],
            $data['effective_minutes'],
            $data['note'],
            $id,
            $user_id
        ]);
    }

    public static function delete($id, $user_id) {
        $stmt = DB::conn()->prepare("
            DELETE FROM study_sessions WHERE id=? AND user_id=?
        ");
        return $stmt->execute([$id, $user_id]);
    }
}
