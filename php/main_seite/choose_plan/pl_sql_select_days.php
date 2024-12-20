<?php
include __DIR__ . "/../../../php/datenbank_connection.php";
global $conn;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $user_id;
} else {
    echo "Kein Benutzer angemeldet!<br>";
    exit();
}

if (isset($_SESSION['plan_id'])) {
    $planID = $_SESSION['plan_id'];
} else {
    echo "Kein Plan ausgewählt!<br>";
    exit();
}

$filter_sql = "SELECT
        W.workout_id AS id,
        W.workout_name AS name
    FROM 
        UserWorkoutPlan UWP
    LEFT JOIN 
        Link_Plan_Workout LPW ON UWP.plan_id = LPW.plan_id
    LEFT JOIN
        Workouts W ON LPW.Workout_id = W.workout_id
    WHERE
        UWP.plan_id = ?
";
$stmt = $conn->prepare($filter_sql);
$stmt->bind_param("i", $planID);
$stmt->execute();
$result = $stmt->get_result();

$workouts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $workouts[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
}

return $workouts;
?>
