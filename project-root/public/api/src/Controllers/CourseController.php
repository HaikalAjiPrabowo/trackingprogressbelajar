<?php
namespace Controllers;

use Models\Course;

class CourseController {

    public function index() {
        $user_id = $_SESSION['user_id'];
        $data = Course::all($user_id);
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
        exit;
    }

    public function store() {
        $user_id = $_SESSION['user_id'];
        $input = json_decode(file_get_contents("php://input"), true);

        $id = Course::create($user_id, $input);

        echo json_encode([
            "status" => "success",
            "message" => "created",
            "id" => $id
        ]);
        exit;
    }

    public function update($id) {
        $user_id = $_SESSION['user_id'];
        $input = json_decode(file_get_contents("php://input"), true);

        Course::update($id, $user_id, $input);

        echo json_encode([
            "status" => "success",
            "message" => "updated"
        ]);
        exit;
    }

    public function destroy($id) {
        $user_id = $_SESSION['user_id'];

        Course::delete($id, $user_id);

        echo json_encode([
            "status" => "success",
            "message" => "deleted"
        ]);
        exit;
    }
}
