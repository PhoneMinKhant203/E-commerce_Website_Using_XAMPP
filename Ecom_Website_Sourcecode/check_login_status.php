<?php
session_start();
$response = array();

if (isset($_SESSION['user_id'])) {
    $response['loggedIn'] = true;
} else {
    $response['loggedIn'] = false;
}

echo json_encode($response);
?>
