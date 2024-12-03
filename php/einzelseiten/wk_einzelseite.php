<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Workout Details Workout</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
    <script src="../../js/workout.js"></script>
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
        wk.workout_id,
        wk.workout_name,
        wk.is_universal,
        wk.wk_bild,
        wk.description,
        COALESCE(uf.liked,0) as liked,
        us.username as username,
        zi.ziel_name as ziel_name,
        Mu.muscle_name as muscle_name,
        le.level_name as level_name,
        eq.equipment_name as equipment_name,
        wk.creator_user_id
    FROM
        workouts wk
    LEFT JOIN
        userfavorites uf ON uf.workout_id = wk.workout_id AND uf.user_id = ?
    LEFT JOIN
        users us ON wk.creator_user_id = us.id
    LEFT JOIN
        trainingsziel zi ON wk.trainingsziel = zi.ziel_id
    LEFT JOIN
        muscle Mu ON wk.body_part = Mu.muscle_id
    LEFT JOIN
        levels le ON wk.Level = le.level_id
    LEFT JOIN
        equipment eq ON wk.equipment = eq.equipment_id
    WHERE
        wk.workout_id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $workout_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();


    echo "<div class='container_seite'>";
    echo "<div class='top_seite'>";
    echo "<img class='svg' src='../../svg/back-svgrepo-com.svg' onclick='loadworkout()'>";

    echo "<div class='name_herz' data-workout-id='". htmlspecialchars(isset($row['workout_id']) ? $row['workout_id'] : "") ."'>";
    echo "<h2 class='name_seite'>" . htmlspecialchars($row['workout_name']) . "</h2>";

    $likedClass = $row['liked'] ? 'active' : '';
    $heartIcon = $row['liked'] ? '../../svg/heart_filled.svg' : '../../svg/heart-svgrepo-com.svg';
    echo "<div class='like-icon-einzelseite $likedClass' onclick='toggleLikeWk(this, " . htmlspecialchars($row['workout_id']) . ")'>";
    echo "<img src='" . $heartIcon . "' alt='Like Icon' class='heart-icon-einzelseite'>";
    echo "</div>"; #ende like-icon

    echo "</div>"; #ende name & herz
    if ($row['creator_user_id'] == $_SESSION['user_id']) {
        echo "<img id='edit_icon' onclick='load_wk_bearbeiten_user($workout_id)' class='svg' src='../../svg/edit-svgrepo-com-3.svg'>";
    } else {
        echo "<img id='edit_icon' class='svg' src='../../svg/gray.svg'>";
    }


    echo "</div>";#ende top seite
    echo "<div class='content_seite'>";

    echo "<img id='img_titel' class='element-bild' src='/img/workout_bilder/" . htmlspecialchars($row['wk_bild']) . "'>";
    if (!empty($row['description'])) {
        echo "<p class='element-text'>" . htmlspecialchars($row['description']) . "</p>";
    } else {
        echo "<p class='element-text'>There is no description available for this workout.</p>";
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


    $sql_exercises = "
        SELECT 
            ex.name AS exercise_name, 
            mu.muscle_name AS target_muscle,
            eq.equipment_name AS equipment_name,
            fo.force_name AS force_name
        FROM 
            link_workout_exercise lwe
        JOIN 
            exercises ex ON lwe.exercise_id_fk = ex.ex_id
        LEFT JOIN 
            muscle mu ON ex.target_muscle = mu.muscle_id
        LEFT JOIN
            equipment eq ON ex.equipment_requierd = eq.equipment_id
        LEFT JOIN
            force_type fo ON ex.force_type = fo.force_id
        WHERE 
            lwe.workout_id_fk = ?
    ";
    $stmt_exercises = $conn->prepare($sql_exercises);
    if (!$stmt_exercises) {
        die("SQL Error: " . $conn->error);
    }
    $stmt_exercises->bind_param("i", $workout_id);
    $stmt_exercises->execute();
    $result_exercises = $stmt_exercises->get_result();

    // Übungen anzeigen
    if ($result_exercises->num_rows > 0) {
        echo "<h2>Übungen:</h2>";
        echo "<table class='element-table'>";
        echo "<tr class='ueberschrift_tabelle'> 
                <td>Übung</td>
                <td>Zielmuskel</td>
                <td>Benötigtes Equipment</td>
                <td>Force Type</td>
              </tr>";
        while ($exercise = $result_exercises->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($exercise['exercise_name']) . "</td>";
            echo "<td>" . htmlspecialchars($exercise['target_muscle']) . "</td>";
            echo "<td>" . htmlspecialchars($exercise['equipment_name']) . "</td>";
            echo "<td>" . htmlspecialchars($exercise['force_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
echo "</div>"; #ende content seite

echo "</div>";#ende container seite


?>


<?php include '../Impressum/impressum_link_zeile.php'; ?>
