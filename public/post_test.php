<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['status' => 'ok', 'method' => 'POST']);
} else {
    echo json_encode(['status' => 'wrong_method', 'method' => $_SERVER['REQUEST_METHOD']]);
}
