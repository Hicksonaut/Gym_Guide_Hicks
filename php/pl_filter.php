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
        Pl.is_universal,
        T.ziel_name AS ziel,
        M.muscle_name AS muscle,
        L.level_name AS level,
        E.equipment_name AS equipment,
        Pl.trainingstage,
        COALESCE(uf.liked,0) as liked
    FROM 
        WorkoutPlan Pl
    LEFT JOIN
        UserFavorites uf ON Pl.plan_id = uf.plan_id_fk AND uf.user_id = ?
    LEFT JOIN
        trainingsziel T ON Pl.ziel = t.ziel_id
    LEFT JOIN 
        Muscle M ON Pl.body_part = m.muscle_id
    LEFT JOIN
        Levels L On Pl.Level = L.level_id
    LEFT JOIN 
        equipment E ON Pl.equipment = e.equipment_id
";
$filter_result = $conn->prepare($filter_sql);
if (!$filter_result) {
    die ("SQL Error: " . $conn->error);
}
$filter_result->bind_param("i", $user_id);
$filter_result->execute();
$result = $filter_result->get_result();

$is_universal_pl = [];
$ziel_pl = [];
$muscle_pl = [];
$level_pl = [];
$equipment_pl = [];
$liked_pl = [];
$trainingstage_pl = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['is_universal'], $is_universal_pl)) {
            $is_universal_pl[] = $row['is_universal'];
        }
        if (!in_array($row['ziel'], $ziel_pl)) {
            $ziel_pl[] = $row['ziel'];
        }
        if (!in_array($row['muscle'], $muscle_pl)) {
            $muscle_pl[] = $row['muscle'];
        }
        if (!in_array($row['level'], $level_pl)) {
            $level_pl[] = $row['level'];
        }
        if (!in_array($row['equipment'], $equipment_pl)) {
            $equipment_pl[] = $row['equipment'];
        }
        if (!in_array($row['liked'], $liked_pl)) {
            $liked_pl[] = $row['liked'];
        }
        if (!in_array($row['trainingstage'], $trainingstage_pl)) {
            $trainingstage_pl[] = $row['trainingstage'];
        }
    }
}
?>