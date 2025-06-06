<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Übung Details</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
</head>
<body>

<?php
include '../datenbank_connection.php';
global $conn;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['exercise_id'])) {
    $exercise_id = intval($_GET['exercise_id']);
} else {
    echo "Keine Übung ausgewählt!";
    exit();
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
        ex.ex_id AS exercise_id,
        ex.name,
        ex.instructions,
        ex.Tips,
        ex.bild_ex,
        ex.bild_muscle,
        m.muscle_name AS muscle_name,
        z.ziel_name AS ziel_name,
        eq.equipment_name AS equipment_name,
        Me.mechanics_name AS mechanics_name,
        fo.force_name AS force_name,
        le.level_name AS level_name,
        COALESCE(uf.liked,0) as liked
    FROM
        exercises ex
    LEFT JOIN
        userfavorites uf ON uf.exercise_id = ex_id AND uf.user_id = ?
    LEFT JOIN
        muscle m ON ex.target_muscle = m.muscle_id
    LEFT JOIN
        trainingsziel z ON ex.exercise_type = z.ziel_id
    LEFT JOIN
        equipment eq ON ex.equipment_requierd = eq.equipment_id
    LEFT JOIN
        mechanics Me ON ex.Mechanics = Me.mechanics_id
    LEFT JOIN
        force_type fo ON ex.force_type = fo.force_id
    LEFT JOIN
        levels le ON ex.experience_level = le.level_id
    WHERE
        ex.ex_id = ?
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $exercise_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<div class='container_seite'>";
    echo "<div class='top_seite'>";
    echo "<img class='svg' src='../../svg/back-svgrepo-com.svg' onclick='loadexercise()'>";

    echo "<div class='name_herz' data-exercise-id='{$exercise_id}'>";
    echo "<h2 class='name_seite'>" . htmlspecialchars($row['name']) . "</h2>";


    $likedClass = $row['liked'] ? 'active' : '';
    $heartIcon = $row['liked'] ? '../../svg/heart_filled.svg' : '../../svg/heart-svgrepo-com.svg';
    echo "<div class='like-icon-einzelseite' $likedClass onclick='toggleLike(this, ".htmlspecialchars($row['exercise_id']).",\"exercise\")'>";
    echo "<img src='". $heartIcon. "' alt='Like Icon' class='heart-icon-einzelseite'>";
    echo "</div>";

    echo "</div>";


    echo "<img id='edit_icon' class='svg' src='../../svg/gray.svg' style='z-index: -9'>";
    echo "</div>";
    echo "<div class='content_seite'>";

    if (!empty($row['bild_ex'])) {
        echo "<img id='img_titel' class='element-bild' src='/img/Exercise_bilder/" . htmlspecialchars($row["bild_ex"]) . "' >";
    } else {
        echo "<img id='img_titel' class='element-bild' src='/img/image-not-found.png' '>";
    }


    echo "<div class='element-text'>";
    echo "<h3>".htmlspecialchars($row['name'])." Instructions</h3>";
    if (!empty($row['instructions'])) {
        echo "<p>" . nl2br(htmlspecialchars($row['instructions'])) . "</p>";
    } else {
        echo "<p>nothing</p>";
    }
    echo "<h3>".htmlspecialchars($row['name'])." Tips</h3>";
    if (!empty($row['Tips'])) {
        echo "<p>" . htmlspecialchars($row['Tips']) . "</p>";
    } else {
        echo "<p>nothing</p>";
    }

    echo "</div>";

    echo "<table class='element-table'>";
    echo "<tr class='ueberschrift_tabelle'>";
    echo "<td>Zielmuskel</td>";
    echo "<td>Ziel der Übung</td>";
    echo "<td>Benötigtes Equipment</td>";
    echo "<td>Mechanics</td>";
    echo "<td>Force Type</td>";
    echo "<td>Experience Level</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['muscle_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ziel_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['equipment_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['mechanics_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['force_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['level_name']) . "</td>";
    echo "</tr>";
    echo "</table>";


    echo "<table class='element-table-mobile'>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Zielmuskel</td>";
    echo "<td>".htmlspecialchars($row['muscle_name'])."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Ziel der Übung</td>";
    echo "<td>".htmlspecialchars($row['ziel_name'])."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Benötigtes Equipment</td>";
    echo "<td>".htmlspecialchars($row['equipment_name'])."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Mechanics</td>";
    echo "<td>".htmlspecialchars($row['mechanics_name'])."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Force Type</td>";
    echo "<td>".htmlspecialchars($row['force_name'])."</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='ueberschrift_tabelle'>Experience Level</td>";
    echo "<td>".htmlspecialchars($row['level_name'])."</td>";
    echo "</tr>";
    echo "</table>";

    echo "</div>"; #schliest die oberste DIV container_seite
}
?>

<?php include '../Impressum/impressum_link_zeile.php'; ?>
