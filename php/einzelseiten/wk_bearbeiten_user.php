<?php
// Datenbankverbindung und Session starten
include '../datenbank_connection.php';
session_start();
global $conn;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prüfen, ob workout_id übergeben wurde
if (($_GET['workout_id'])){
    $_SESSION['workout_id'] = $_GET['workout_id'];
    $workout_id = intval($_GET['workout_id']);
} elseif (!isset($_GET['workout_id'])) {
    if (!isset($_SESSION['workout_id'])) {
        die("Kein Workout ausgewählt2.");
    } elseif (isset($_SESSION['workout_id'])) {
        $workout_id = $_SESSION['workout_id'];
    }
}



// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    die("Keine Nutzerdaten.");
}

$user_id = $_SESSION['user_id'];

// Workout-Daten aus der Datenbank abrufen, um das Formular vorzubefüllen
$sql = "SELECT workout_name, wk_bild, description FROM Workouts WHERE workout_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workout_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Workout nicht gefunden.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['workout_name']);
    $description = trim($_POST['description']);
    $bild_hochgeladen = false;

    if (isset($_FILES['wk_bild']) && $_FILES['wk_bild']['error'] == UPLOAD_ERR_OK) {
        // Einzigartigen Dateinamen für das Bild generieren
        $uploaddir = '../../img/workout_bilder/';
        $originaldateiname = pathinfo($_FILES["wk_bild"]["name"], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES["wk_bild"]["name"], PATHINFO_EXTENSION);
        $uniquefilename = $originaldateiname . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadfile = $uploaddir . $uniquefilename;

        // Datei in das Zielverzeichnis verschieben
        if (move_uploaded_file($_FILES['wk_bild']['tmp_name'], $uploadfile)) {
            $bild_hochgeladen = true;
            echo "Bild erfolgreich hochgeladen.<br>";
        } else {
            die("Fehler beim Hochladen des Bildes.");
        }
    }

    // SQL-Abfrage vorbereiten: Unterscheidung ob Bild aktualisiert wird oder nicht
    if ($bild_hochgeladen) {
        // Wenn ein neues Bild hochgeladen wurde, auch das Bild aktualisieren
        $sql_update = "UPDATE Workouts SET workout_name = ?, description = ?, wk_bild = ? WHERE workout_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $name, $description, $uniquefilename, $workout_id);
    } else {
        // Wenn kein neues Bild hochgeladen wurde, nur Name und Beschreibung aktualisieren
        $sql_update = "UPDATE Workouts SET workout_name = ?, description = ? WHERE workout_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $name, $description, $workout_id);
    }

    // Update ausführen und Erfolg prüfen
    if ($stmt_update->execute()) {
        echo "Workout erfolgreich aktualisiert!";
    } else {
        echo "Fehler beim Aktualisieren des Workouts: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Workout Bearbeiten</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
</head>
<body>
<div class="container_main">
    <div class="form_main">
        <h2>Bearbeite Workout: <?php echo htmlspecialchars($row['workout_name']); ?></h2>

        <form action="" method="post" enctype="multipart/form-data" id="UpdateWorkoutForm">
            <div class="form_group">
                <label for="workout_name">Workout Name:</label>
                <input type="text" id="workout_name" name="workout_name" value="<?php echo htmlspecialchars($row['workout_name']); ?>" required>
            </div>

            <div class="form_group">
                <label for="description">Beschreibung:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>

            <div class="form_group">
                <label for="wk_bild">Bild aktualisieren:</label>
                <!-- Aktuelles Bild anzeigen -->
                <img src="../../img/workout_bilder/<?php echo !empty($row['wk_bild']) ? htmlspecialchars($row['wk_bild']) : 'random.png'; ?>" alt="Workout Bild" style="max-width: 200px;">
                <!-- Datei-Upload für neues Bild -->
                <input type="file" id="wk_bild" name="wk_bild">
            </div>

            <div class="form_group">
                <input type="submit" name="submit" value="submit" required>
            </div>
        </form>
    </div>
</div>
</body>
</html>