<?php
session_start();
include "../datenbank_connection.php";
global $conn;

if (!isset($_SESSION['user_id'])) {
    echo "error: not authenticated";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $plan_id = $_SESSION['plan_id'];

    echo "Plan ID: " . $plan_id . ", User ID: " . $user_id;

    // SQL-Anweisung zum Löschen
    $sql = "DELETE FROM workoutplan WHERE plan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $plan_id );

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "deleted";
    } else {
        echo "error: could not delete plan";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "error: invalid request method";
}
?>
