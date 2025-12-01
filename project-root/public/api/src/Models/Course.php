<?php
namespace Models;

use Utils\DB;

class Course {

    public static function all($user_id) {
        $stmt = DB::conn()->prepare("SELECT * FROM courses WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function create($user_id, $data) {
        $stmt = DB::conn()->prepare("
            INSERT INTO courses (user_id, code, title, category, color) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $data['code'],
            $data['title'],
            $data['category'],
            $data['color']
        ]);
        return DB::conn()->lastInsertId();
    }

    public static function update($id, $user_id, $data) {
        $stmt = DB::conn()->prepare("
            UPDATE courses SET
            code=?, title=?, category=?, color=?
            WHERE id=? AND user_id=?
        ");
        return $stmt->execute([
            $data['code'],
            $data['title'],
            $data['category'],
            $data['color'],
            $id,
            $user_id
        ]);
    }

    public static function delete($id, $user_id) {
        $stmt = DB::conn()->prepare("DELETE FROM courses WHERE id=? AND user_id=?");
        return $stmt->execute([$id, $user_id]);
    }
}
