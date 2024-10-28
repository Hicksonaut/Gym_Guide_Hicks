<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'datenbank_connection.php';
global $conn;

if ($conn->connect_error) {
    die("Verbindung zur Datenbank fehlgeschlagen: " . $conn->connect_error);
}

// Abrufen der Übungen
if (isset($_SESSION['user_id'])) {
    $userid = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $userid = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $userid;
} else {
    echo "Kein Benutzer angemeldet.<br>";
    exit();
}

$sql = "
    SELECT
        ex.ex_id,
        ex.name,
        ex.bild_ex,
        M.muscle_name AS target_muscle_name,
        e.equipment_name AS e_name,
        me.mechanics_name AS me_name,
        L.level_name AS experience_level_name,
        COALESCE(uf.liked, 0) as liked
    FROM
        exercises ex
    LEFT JOIN 
        UserFavorites uf ON ex.ex_id = uf.exercise_id AND uf.user_id = ?
    LEFT JOIN
        Muscle M ON ex.target_muscle = M.muscle_id
    LEFT JOIN
        equipment e ON ex.equipment_requierd = e.equipment_id
    LEFT JOIN 
        mechanics me ON ex.mechanics = me.mechanics_id
    LEFT JOIN
        Levels L ON ex.experience_level = L.level_id
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<div class='exercise-container''>";
    while ($row = $result->fetch_assoc()) {
        echo "<div  class='exercise-card' data-exercise-id='" . htmlspecialchars($row['ex_id']) . "' data-likedex='" . htmlspecialchars($row['liked']) . "' onclick='load_einzelseite_ex(".htmlspecialchars($row['ex_id']).")'>";
        $likedClass = $row["liked"] ? 'active' : '';

        $heartIcon = $row["liked"] ? '/svg/heart_filled.svg' : '/svg/heart-svgrepo-com.svg';
        echo "<div class='like-icon $likedClass' onclick='toggleLike(this, " . htmlspecialchars($row['ex_id']) . ")'>";
        echo "<img src='" . $heartIcon . "' alt='Like Icon' class='heart-icon'>";
        echo "</div>";

        if (!empty($row['bild_ex'])) {
            echo "<img class='exercise-image' src='/img/Exercise_bilder/" . htmlspecialchars($row["bild_ex"]) . "' >";
        } else {
            echo "<img class='exercise-image' src='/img/image-not-found.png' '>";
        }
        echo "<h2 class='exercise-name'>" . htmlspecialchars($row["name"]) . "</h2>";
        echo "<div class='container-attribut'>";
        echo "<p class='exercise-attribut-border'>" . htmlspecialchars($row["target_muscle_name"]) . "</p>";
        echo "<p class='exercise-attribut-border'>" . htmlspecialchars($row["e_name"]) . "</p>";
        echo "<p class='exercise-attribut-border'>" . htmlspecialchars($row["me_name"]) . "</p>";
        echo "<p class='exercise-attribut-border'>" . htmlspecialchars($row["experience_level_name"]) . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "Keine Ergebnisse gefunden.";
}

$conn->close();
?>