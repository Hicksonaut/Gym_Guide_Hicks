<?php
include 'datenbank_connection.php';
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

$filter_sql = "SELECT DISTINCT 
        wk.is_universal, 
        T.ziel_name AS trainingsziel, 
        M.muscle_name AS body_part_name, 
        L.level_name AS level_name, 
        E.equipment_name AS equipment_name,
        COALESCE(uf.liked,0) as liked
    FROM 
        workouts wk
    LEFT JOIN
        userfavorites uf ON wk.workout_id = uf.workout_id AND uf.user_id = ?
    LEFT JOIN
        muscle M ON wk.body_part = M.muscle_id
    LEFT JOIN
        levels L ON wk.level = L.level_id
    LEFT JOIN
        trainingsziel T ON wk.trainingsziel = T.ziel_id
    LEFT JOIN
        equipment E ON wk.equipment = E.equipment_id
";
$filter_result = $conn->prepare($filter_sql);
if (!$filter_result) {
    die ("SQL Error: " . $conn->error);
}
$filter_result->bind_param("i", $user_id);
$filter_result->execute();
$result = $filter_result->get_result();

$is_universal = [];
$trainingsziel = [];
$body_part = [];
$Level = [];
$equipment = [];
$liked = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['is_universal'], $is_universal)) {
            $is_universal[] = $row['is_universal'];
        }
        if (!in_array($row['trainingsziel'], $trainingsziel)) {
            $trainingsziel[] = $row['trainingsziel'];
        }
        if (!in_array($row['body_part_name'], $body_part)) {
            $body_part[] = $row['body_part_name'];
        }
        if (!in_array($row['level_name'], $Level)) {
            $Level[] = $row['level_name'];
        }
        if (!in_array($row['equipment_name'], $equipment)) {
            $equipment[] = $row['equipment_name'];
        }
        if (!in_array($row['liked'], $liked)) {
            $liked[] = $row['liked'];
        }
    }
}
?>