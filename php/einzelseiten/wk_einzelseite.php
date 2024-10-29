<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Workout Details</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
</head>
<body>

<?php
include '../datenbank_connection.php';
global $conn;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['workout_id'])) {
    $workout_id = intval($_GET['workout_id']);
} else {
    echo "Kein Wokout ausgewählt";
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $user_id;
} else {
    echo "Kein Nutzerdaten";
}

$sql = "
    SELECT
        wk.workout_name,
        wk.is_universal,
        wk.wk_bild,
        wk.description,
        COALESCE(uf.liked,0) as liked,
        us.username as username,
        zi.ziel_name as ziel_name,
        Mu.muscle_name as muscle_name,
        le.level_name as level_name,
        eq.equipment_name as equipment_name
    FROM
        Workouts wk
    LEFT JOIN
        UserFavorites uf ON uf.workout_id = wk.workout_id AND uf.user_id = ?
    LEFT JOIN
        Users us ON wk.creator_user_id = us.id
    LEFT JOIN
        trainingsziel zi ON wk.trainingsziel = zi.ziel_id
    LEFT JOIN
        Muscle Mu ON wk.body_part = Mu.muscle_id
    LEFT JOIN
        Levels le ON wk.Level = le.level_id
    LEFT JOIN
        equipment eq ON wk.equipment = eq.equipment_id
    WHERE
        wk.workout_id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id,$workout_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo "<div class='container_seite'>";
    echo "<div class='top_seite'>";
    echo "<img class='svg' src='../../svg/back-svgrepo-com.svg' onclick='loadworkout()'>";
    echo "<h2 class='name_seite'>" . htmlspecialchars($row['workout_name']) . "</h2>";
    echo "<img id='edit_icon' class='svg' src='../../svg/edit-svgrepo-com-3.svg'>";
    echo "</div>";
    echo "<div class='content_seite'>";

    echo "<img id='img_titel' class='element-bild' src='/img/workout_bilder/" . htmlspecialchars($row['wk_bild']) . "'>";
    if (!empty($row['description'])) {
        echo "<p class='element-text'>" . htmlspecialchars($row['description']) . "</p>";
    } else {
        echo "<p class='element-text'>nothing</p>";
    }

    echo "<table class='element-table'>";
    echo "<tr class='ueberschrift_tabelle'>";
    echo "<td>Ziel</td>";
    echo "<td>Zielmuskel</td>";
    echo "<td>Benötigtes Equipment</td>";
    echo "<td>Level</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ziel_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['muscle_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['equipment_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['level_name']) . "</td>";
    echo "</tr>";
    echo "</table>";


    echo "<table class='element-table-mobile'>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Ziel</td>";
    echo "<td>" . htmlspecialchars($row['ziel_name']) . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Zielmuskel</td>";
    echo "<td>" . htmlspecialchars($row['muscle_name']) . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Benötigtes Equipment</td>";
    echo "<td>" . htmlspecialchars($row['equipment_name']) . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Level</td>";
    echo "<td>" . htmlspecialchars($row['level_name']) . "</td>";
    echo "</tr>";
    echo "</table>";

    echo "</div>";
}
?>