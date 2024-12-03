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


$filter_sql_ex = "SELECT DISTINCT 
        M.muscle_name AS muscle_name, 
        e.equipment_name AS equipment_name, 
        me.mechanics_name AS mechanics_name, 
        l.level_name AS level_name, 
        ex.force_type, 
        ex.exercise_type,
        COALESCE(uf.liked,0) as liked
    FROM 
        exercises ex
    LEFT JOIN
        userfavorites uf ON ex.ex_id = uf.exercise_id AND uf.user_id = ?
    LEFT JOIN
        muscle M ON ex.target_muscle = M.muscle_id
    LEFT JOIN
        equipment e ON ex.equipment_requierd = e.equipment_id
    LEFT JOIN 
        mechanics me ON ex.mechanics = me.mechanics_id
    LEFT JOIN
        levels l ON ex.experience_level = l.level_id
";
$filter_result_ex = $conn->prepare($filter_sql_ex);
if (!$filter_result_ex) {
    die ("SQL Error: " . $conn->error);
}
$filter_result_ex->bind_param("i", $user_id);
$filter_result_ex->execute();
$result_ex = $filter_result_ex->get_result();

$target_muscles = [];
$equipment_requierd = [];
$mechanics = [];
$experience_level = [];
$force_type = [];
$exercise_type = [];
$liked = [];

if ($result_ex->num_rows > 0) {
    while($row = $result_ex->fetch_assoc()) {
        if (!in_array($row['muscle_name'], $target_muscles)) {
            $target_muscles[] = $row['muscle_name'];
        }
        if (!in_array($row['equipment_name'], $equipment_requierd)) {
            $equipment_requierd[] = $row['equipment_name'];
        }
        if (!in_array($row['mechanics_name'], $mechanics)) {
            $mechanics[] = $row['mechanics_name'];
        }
        if (!in_array($row['level_name'], $experience_level)) {
            $experience_level[] = $row['level_name'];
        }
        if (!in_array($row['force_type'], $force_type)) {
            $force_type[] = $row['force_type'];
        }
        if (!in_array($row['exercise_type'], $exercise_type)) {
            $exercise_type[] = $row['exercise_type'];
        }
        if (!in_array($row['liked'], $liked)) {
            $liked[] = $row['liked'];
        }
    }
}
?>