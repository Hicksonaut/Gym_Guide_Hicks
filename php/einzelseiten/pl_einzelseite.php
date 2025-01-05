<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Plan Details</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
</head>
<body>

<?php
include '../datenbank_connection.php';
global $conn;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['plan_id'])) {
    $plan_id = intval($_GET['plan_id']);
} else {
    echo "Kein Plan ausgewählt";
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
        pl.plan_id,
        pl.name,
        pl.is_universal,
        pl.Bild,
        pl.description,
        pl.trainingstage,
        COALESCE(uf.liked,0) as liked,
        us.username as username,
        zi.ziel_name as ziel_name,
        Mu.muscle_name as muscle_name,
        le.level_name as level_name,
        eq.equipment_name as equipment_name,
        pl.creator_user_id
    FROM
        workoutplan pl
    LEFT JOIN
        userfavorites uf ON uf.plan_id_fk = pl.plan_id AND uf.user_id = ?
    LEFT JOIN
        users us ON pl.creator_user_id = us.id
    LEFT JOIN
        trainingsziel zi ON pl.ziel = zi.ziel_id
    LEFT JOIN
        muscle Mu ON pl.body_part = Mu.muscle_id
    LEFT JOIN
        levels le ON pl.Level = le.level_id
    LEFT JOIN
        equipment eq ON pl.equipment = eq.equipment_id
    WHERE
        pl.plan_id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $plan_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();


    echo "<div class='container_seite'>";
    echo "<div class='top_seite'>";
    echo "<img class='svg' src='../../svg/back-svgrepo-com.svg' onclick='loadplan()'>";

    echo "<div class='name_herz' data-plan-id='". htmlspecialchars(isset($row['plan_id']) ? $row['plan_id'] : "") ."'>";
    echo "<h2 class='name_seite'>" . htmlspecialchars($row['name']) . "</h2>";

    $likedClass = $row['liked'] ? 'active' : '';
    $heartIcon = $row['liked'] ? '../../svg/heart_filled.svg' : '../../svg/heart-svgrepo-com.svg';
    echo "<div class='like-icon-einzelseite $likedClass' onclick='toggleLike(this, " . htmlspecialchars($row['plan_id']) . ",\"exercise\")'>";
    echo "<img src='" . $heartIcon . "' alt='Like Icon' class='heart-icon-einzelseite'>";
    echo "</div>"; #ende like-icon
 
    echo "</div>"; #ende name & herz
    if ($row['creator_user_id'] == $_SESSION['user_id']) {
        echo "<img id='edit_icon' onclick='load_pl_bearbeiten_user($plan_id)' class='svg' src='../../svg/edit-svgrepo-com-3.svg'>";
    } else {
        echo "<img id='edit_icon' class='svg' src='../../svg/gray.svg'>";
    }


    echo "</div>";#ende top seite
    echo "<div class='content_seite'>";

    echo "<img id='img_titel' class='element-bild' src='/img/Plan_bilder/" . htmlspecialchars($row['Bild']) . "'>";
    if (!empty($row['description'])) {
        echo "<p class='element-text'>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
    } else {
        echo "<p class='element-text'>There is no description available for this Plan. If this is your own Plan you can create one, in the Editing Menu.</p>";
    }

    echo "<table class='element-table'>";
    echo "<tr class='ueberschrift_tabelle'>";
    echo "<td>Ziel</td>";
    echo "<td>Zielmuskel</td>";
    echo "<td>Benötigtes Equipment</td>";
    echo "<td>Level</td>";
    echo "<td>Trainingstage</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ziel_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['muscle_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['equipment_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['level_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['trainingstage']) . "</td>";
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
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Trainingstage</td>";
    echo "<td>" . htmlspecialchars($row['trainingstage']) . "</td>";
    echo "</tr>";
    echo "</table>";


    $sql_workouts = "
        SELECT 
            Wk.workout_id AS wk_id,
            Wk.workout_name AS workout_name, 
            mu.muscle_name AS muscle_name,
            eq.equipment_name AS equipment_name,
            Le.level_name AS level_name
        FROM 
            link_plan_workout lPW
        JOIN 
            workouts Wk ON lPW.Workout_id = Wk.workout_id
        LEFT JOIN 
            muscle mu ON Wk.body_part = mu.muscle_id
        LEFT JOIN
            equipment eq ON Wk.equipment = eq.equipment_id
        LEFT JOIN
            levels Le ON Wk.Level = Le.level_id
        WHERE 
            lPW.plan_id = ?
    ";
    $stmt_workouts = $conn->prepare($sql_workouts);
    if (!$stmt_workouts) {
        die("SQL Error: " . $conn->error);
    }
    $stmt_workouts->bind_param("i", $plan_id);
    $stmt_workouts->execute();
    $result_workouts = $stmt_workouts->get_result();

    // Übungen anzeigen
    if ($result_workouts->num_rows > 0) {
        echo "<h2>Workouts:</h2>";
        echo "<table class='element-table'>";
        echo "<tr class='ueberschrift_tabelle'> 
                <td>Workout</td>
                <td>Zielmuskel</td>
                <td>Benötigtes Equipment</td>
                <td>Level</td>
              </tr>";
        while ($workout = $result_workouts->fetch_assoc()) {
            echo "<tr>";
            echo "<tr onclick='load_einzelseite_wk(" . htmlspecialchars($workout['wk_id']) . ")'>";
            echo "<td>" . htmlspecialchars($workout['workout_name']) . "</td>";
            echo "<td>" . htmlspecialchars($workout['muscle_name']) . "</td>";
            echo "<td>" . htmlspecialchars($workout['equipment_name']) . "</td>";
            echo "<td>" . htmlspecialchars($workout['level_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
echo "</div>"; #ende content seite

echo "</div>";#ende container seite


?>

<?php include '../Impressum/impressum_link_zeile.php'; ?>
