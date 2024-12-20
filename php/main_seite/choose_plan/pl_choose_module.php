<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../../datenbank_connection.php';
global $conn;

if ($conn->connect_error) {
    die("Verbindung zur DB fehlgeschlagen: " . $conn->connect_error);
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

$sql = "
    SELECT
        Pl.plan_id,
        Pl.name,
        Pl.Bild,
        T.ziel_name AS ziel,
        m.muscle_name AS muscle,
        E.equipment_name AS equipment,
        l.level_name AS level,
        Pl.trainingstage AS tage,
        Pl.is_universal,
        EXISTS(
            SELECT 1
            FROM UserWorkoutPlan UWP
            WHERE UWP.plan_id =  PL.plan_id
            AND UWP.user_id = ?
        ) AS ChoosenPlan
    FROM
        workoutplan Pl
    LEFT JOIN
        muscle m ON Pl.body_part = m.muscle_id
    LEFT JOIN
        levels l ON Pl.Level = l.level_id
    LEFT JOIN
        trainingsziel T ON Pl.ziel = T.ziel_id
    LEFT JOIN
        equipment E ON Pl.equipment = E.equipment_id
    WHERE 
        Pl.creator_user_id = ? OR Pl.is_universal = 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die ("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='module-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='module-card' data-plan-id='" . htmlspecialchars(isset($row['plan_id']) ? $row['plan_id'] : '') . "' data-is-universal='" . htmlspecialchars(isset($row['is_universal']) ? $row['is_universal'] : '') . "' data-level='" . htmlspecialchars(isset($row['level_name']) ? $row['level_name'] : '') . "'data-tage='".htmlspecialchars(isset($row['tage']) ? $row['tage'] : '')."')'>";

        if($row['ChoosenPlan']) {
            echo "<div class='plus-icon' onclick='choose_plan(".htmlspecialchars($row['plan_id']).")'>
            <img class='plus-icon-img' src='../../../svg/check-circle.svg' alt='check' title='check'>
            </div>";
        } else {
            echo "<div class='plus-icon' onclick='choose_plan(".htmlspecialchars($row['plan_id']).")'>
            <img class='plus-icon-img' src='../../../svg/plus.svg' alt='plus' title='plus'>
            </div>";
        }

        if (!empty($row['Bild'])) {
            echo "<img class='module-image' src='../../../img/Plan_bilder/" . htmlspecialchars($row['Bild']) . "'>";
        } else {
            echo "<img class='module-image' src='../../../img/image-not-found.png'>";
        }
        echo "<h2 class='module-name'>" . htmlspecialchars($row['name']) . "</h2>";
        echo "<div class='container-attribut'>";
        echo "<p class='module-attribut-border-four'>" . (!empty($row['ziel']) ? htmlspecialchars($row['ziel']) : "nichts") . "</p>";
        echo "<p class='module-attribut-border-four'>" . (!empty($row['muscle']) ? htmlspecialchars($row['muscle']) : "nichts") . "</p>";
        echo "<p class='module-attribut-border-four'>" . (!empty($row['equipment']) ? htmlspecialchars($row['equipment']) : "nichts") . "</p>";
        echo "<p class='module-attribut-border-four'>" . (!empty($row['level']) ? htmlspecialchars($row['level']) : "nichts") . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunde.";
}

$conn->close();
?>