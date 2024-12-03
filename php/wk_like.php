<?php
session_start();
include 'datenbank_connection.php';
global $conn;

$userid = null;

if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $userid = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $userid;
}
$workoutid = $_POST['workout_id'];
$liked = $_POST['liked'];

//Überprüfen, ob ein Eintrag schon besteht
$sql_check = "SELECT * FROM userfavorites WHERE user_id = ? AND workout_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $userid, $workoutid);
$stmt_check->execute();
$result = $stmt_check->get_result();


if ($result->num_rows > 0) {
    //falls vorhanden Status aktualiseren
    $sql_update = "UPDATE userfavorites SET liked = ? WHERE user_id = ? AND workout_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iii", $liked, $userid, $workoutid);
    $stmt_update->execute();
} else {
    $sql_insert = "INSERT INTO userfavorites (user_id, workout_id, liked) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iii", $userid, $workoutid, $liked);
    $stmt_insert->execute();
}

$conn->close();
echo json_encode(["Status" => "Success"]);

?>

