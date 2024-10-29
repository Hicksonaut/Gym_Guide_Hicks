<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'datenbank_connection.php';
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
        wk.workout_id,
        wk.workout_name,
        wk.wk_bild,
        T.ziel_name AS trainingsziel,
        m.muscle_name AS body_part_name,
        E.equipment_name AS equipment_name,
        l.level_name AS level_name,
        wk.is_universal,
        COALESCE(uf.liked,0) as liked
    FROM
        workouts wk
    LEFT JOIN
        UserFavorites uf ON wk.workout_id = uf.workout_id AND uf.user_id = ?
    LEFT JOIN
        Muscle m ON wk.body_part = m.muscle_id
    LEFT JOIN
        Levels l ON wk.Level = l.level_id
    LEFT JOIN
        trainingsziel T ON wk.trainingsziel = t.ziel_id
    LEFT JOIN
        equipment E ON wk.equipment = e.equipment_id
    WHERE 
        wk.creator_user_id = ? OR wk.is_universal = 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die ("SQL Error: " . $conn->error);
}
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='workout-container'>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='workout-card' data-workout-id='" . htmlspecialchars(isset($row['workout_id']) ? $row['workout_id'] : '') . "' data-is-universal='" . htmlspecialchars(isset($row['is_universal']) ? $row['is_universal'] : '') . "' data-level='" . htmlspecialchars(isset($row['level_name']) ? $row['level_name'] : '') . "' data-liked='" . htmlspecialchars($row['liked']) . "' onclick='load_einzelseite_wk(".htmlspecialchars($row['workout_id']).")'>";
        $likedClass = $row['liked'] ? 'active' : '';

        $heartIcon = $row["liked"] ? '/svg/heart_filled.svg' : '/svg/heart-svgrepo-com.svg';
        echo "<div class='like-icon-wk $likedClass ' onclick='toggleLikeWk(this, " . htmlspecialchars($row['workout_id']) . ")'>";
        echo "<img src='" . $heartIcon . "' alt='Like Icon' class='heart-icon-wk'>";
        echo "</div>";

        if (!empty($row['wk_bild'])) {
            echo "<img class='workout-image' src='/img/workout_bilder/" . htmlspecialchars($row['wk_bild']) . "'>";
        } else {
            echo "<img class='workout-image' src='/img/image-not-found.png'>";
        }
        echo "<h2 class='workout-name'>" . htmlspecialchars($row['workout_name']) . "</h2>";
        echo "<div class='wk_container_attribut'>";
        echo "<p class='workout-attribut-border'>" . (!empty($row['trainingsziel']) ? htmlspecialchars($row['trainingsziel']) : "nichts") . "</p>";
        echo "<p class='workout-attribut-border'>" . (!empty($row['body_part_name']) ? htmlspecialchars($row['body_part_name']) : "nichts") . "</p>";
        echo "<p class='workout-attribut-border'>" . (!empty($row['equipment_name']) ? htmlspecialchars($row['equipment_name']) : "nichts") . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunde.";
}

$conn->close();
?>