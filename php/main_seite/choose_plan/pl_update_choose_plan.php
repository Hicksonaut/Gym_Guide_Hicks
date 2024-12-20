<?php
session_start();
include __DIR__ . '/../../../php/datenbank_connection.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan_id = $_SESSION['plan_id'];

    if (!isset($_POST['plan_id'])) {
        echo "Plan ID fehlt.";
        exit();
    }

    // Benutzer-ID aus der Session abrufen
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    } elseif (isset($_COOKIE['user_id'])) {
        $user_id = $_COOKIE['user_id'];
    } else {
        echo "Kein Benutzer angemeldet.";
        exit();
    }

    $plan_id = $_POST['plan_id'];

    // Prüfen, ob der Benutzer den Plan bereits ausgewählt hat
    $check_sql = "SELECT * FROM UserWorkoutPlan WHERE user_id = ? AND plan_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $plan_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Plan bereits ausgewählt, daher löschen (Deselektion)
        $delete_sql = "DELETE FROM UserWorkoutPlan WHERE user_id = ? AND plan_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $user_id, $plan_id);
        if ($delete_stmt->execute()) {
            // Auch alle Tageszuordnungen löschen
            $delete_schedule_sql = "DELETE FROM user_workout_schedule WHERE user_id = ? AND plan_id = ?";
            $delete_schedule_stmt = $conn->prepare($delete_schedule_sql);
            $delete_schedule_stmt->bind_param("ii", $user_id, $plan_id);
            $delete_schedule_stmt->execute();

            echo "removed"; // Rückmeldung an die UI
        } else {
            echo "Fehler beim Entfernen des Plans.";
        }
    } else {
        // Vorherigen Plan des Benutzers löschen (falls vorhanden)
        $delete_existing_sql = "DELETE FROM UserWorkoutPlan WHERE user_id = ?";
        $delete_existing_stmt = $conn->prepare($delete_existing_sql);
        $delete_existing_stmt->bind_param("i", $user_id);
        $delete_existing_stmt->execute();

        // Auch alle Tageszuordnungen löschen
        $delete_existing_schedule_sql = "DELETE FROM user_workout_schedule WHERE user_id = ?";
        $delete_existing_schedule_stmt = $conn->prepare($delete_existing_schedule_sql);
        $delete_existing_schedule_stmt->bind_param("i", $user_id);
        $delete_existing_schedule_stmt->execute();

        // Neuen Plan hinzufügen
        $insert_sql = "INSERT INTO UserWorkoutPlan (user_id, plan_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $plan_id);
        if ($insert_stmt->execute()) {
            $_SESSION['plan_id'] = $plan_id;
            echo "added"; // Rückmeldung an die UI
        } else {
            echo "Fehler beim Hinzufügen des Plans.";
        }
    }
}