<?php
namespace App\Controllers;

use CodeIgniter\Controller;

class TestMySQLi extends Controller
{
    public function index()
    {
        $conn = mysqli_connect("localhost", "root", "", "tusbinright");
        if (!$conn) {
            die(json_encode(["error" => mysqli_connect_error()]));
        } else {
            die(json_encode(["success" => "Connected to MySQL successfully"]));
        }
    }
}
