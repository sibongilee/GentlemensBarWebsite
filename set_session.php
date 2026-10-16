<?php
// set_session.php
session_start();
$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['uid'])) {
    $_SESSION['user_id'] = $data['uid'];
    $_SESSION['user_email'] = $data['email'];
    $_SESSION['user_name'] = $data['name'];
    $_SESSION['user_role'] = $data['role'];
}
echo json_encode(['status' => 'success']);
?>