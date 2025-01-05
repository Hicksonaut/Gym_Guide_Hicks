<?php
global $conn;
include __DIR__ . '/../../../php/datenbank_connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $user_id;
} else {
    echo "Kein Benutzer angemeldet!<br>";
    exit();
}

// Wochentag auf Deutsch ermitteln
$formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::FULL, IntlDateFormatter::NONE);
$formatter->setPattern('eeee');
$day = $formatter->format(new DateTime());


$sql = "
    SELECT
        w.workout_name AS name,
        w.wk_bild AS bild,
        w.workout_id
    FROM
        user_workout_schedule uws
    LEFT JOIN
        workouts w ON uws.workout_id = w.workout_id
    WHERE
        uws.user_id = ? AND uws.day = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $day);
$stmt->execute();
$result = $stmt->get_result();

// Ausgabe der Workouts
echo "
 <div class='card'>
    <div class='card-header'>
        <h2 class='card-title'>Todays Workout</h2>
        <span>See All</span>
    </div>
    <div class='card-body'>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card-item-space-between'>";
        echo "<img onclick='load_workout_fill_in_data(".htmlspecialchars($row['workout_id']).")' class='card-img' src='../../img/workout_bilder/" . htmlspecialchars($row['bild']) . "' alt='WorkoutBild'>";
        echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
        echo "</div>";
    }
} else {
    echo "<div class='card-item-space-between'>";
    echo "<img class='card-img' src='../../../img/GettyImages-643310522-©laflor.jpg.webp' alt='WorkoutBild'>";
    echo "<h3>Restday</h3>";
    echo "</div>";
}

echo "
    </div>
</div>";
?>