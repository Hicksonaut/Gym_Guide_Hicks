<?php
include __DIR__ . "/../../../php/datenbank_connection.php";
global $conn;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$sql = "
    SELECT
        UWP.plan_id,
        WP.name,
        WP.Bild
    FROM
        userworkoutplan AS UWP
    LEFT JOIN 
        workoutplan WP ON UWP.plan_id = WP.plan_id
    WHERE 
        user_id = ?
";

if (!isset($_SESSION['user_id'])) {
    die("Kein Benutzer ausgewählt.");
}

if (!isset($_SESSION['plan_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $_SESSION['plan_id'] = $row['plan_id'];
    } else {
        die("Kein Plan in der Datenbank gefunden.");
    }
}

$user_id = $_SESSION['user_id'];
$planID = $_SESSION['plan_id'];

$days = ["montag", "dienstag", "mittwoch", "donnerstag", "freitag", "samstag", "sonntag"];

// Abfrage: Bereits gespeicherte Workouts pro Tag abrufen
$sql_existing = "
    SELECT day, workout_id
    FROM user_workout_schedule
    WHERE user_id = ? AND plan_id = ?";
$stmt_existing = $conn->prepare($sql_existing);
$stmt_existing->bind_param("ii", $user_id, $planID);
$stmt_existing->execute();
$result_existing = $stmt_existing->get_result();


$existing_workouts = [];
while ($row = $result_existing->fetch_assoc()) {
    $existing_workouts[$row['day']] = $row['workout_id'];
}

// Wenn POST-Anfrage, Daten speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($days as $day) {
        if (isset($_POST['workout_' . $day])) {
            $workout_id = $_POST['workout_' . $day];

            // Skip "restday" entries
            if ($workout_id === 'restday') {
                // Lösche Eintrag für diesen Tag
                $sql_delete = "
                DELETE FROM user_workout_schedule
                WHERE user_id = ? AND plan_id = ? AND day = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("iis", $user_id, $planID, $day);

                if (!$stmt_delete->execute()) {
                    error_log("Fehler beim Löschen für $day: " . $stmt_delete->error);
                }
                continue; // Überspringe den Rest der Logik
            }

            // Insert or update the entry
            $sql = "
                INSERT INTO user_workout_schedule (user_id, plan_id, day, workout_id)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE workout_id = VALUES(workout_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisi", $user_id, $planID, $day, $workout_id);

            if (!$stmt->execute()) {
                error_log("Fehler beim Speichern von $day: " . $stmt->error);
            }
        }
    }
}

// Workouts für das Formular abrufen
$workouts = include "pl_sql_select_days.php";
?>
<form action="" method="post" id="choose_days_workout">
    <div class="grid_seven">
        <?php foreach ($days as $day): ?>
            <div class="day">
                <h3><?php echo ucfirst($day); ?></h3>
                <div class="button-group">
                    <input
                            type="radio"
                            id="<?php echo $day; ?>_restday"
                            name="workout_<?php echo $day; ?>"
                            value="restday"
                        <?php echo !isset($existing_workouts[$day]) ? 'checked' : ''; ?>
                    >
                    <label for="<?php echo $day; ?>_restday" class="button">Ruhetag</label>
                    <?php foreach ($workouts as $workout): ?>
                        <input
                                type="radio"
                                id="<?php echo $day; ?>_<?php echo $workout['id']; ?>"
                                name="workout_<?php echo $day; ?>"
                                value="<?php echo $workout['id']; ?>"
                            <?php echo (isset($existing_workouts[$day]) && $existing_workouts[$day] == $workout['id']) ? 'checked' : ''; ?>
                        >
                        <label for="<?php echo $day; ?>_<?php echo $workout['id']; ?>" class="button"><?php echo $workout['name']; ?></label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <input type="submit" value="Speichern">
</form>
