<?php
// Datenbankverbindung und Session starten
include '../datenbank_connection.php';
session_start();
global $conn;

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (($_GET['plan_id'])){
    $_SESSION['plan_id'] = $_GET['plan_id'];
    $plan_id = intval($_GET['plan_id']);
} elseif (!isset($_GET['plan_id'])) {
    if (!isset($_SESSION['plan_id'])) {
        die("Kein Plan ausgewählt2.");
    } elseif (isset($_SESSION['plan_id'])) {
        $plan_id = $_SESSION['plan_id'];
    }
}



// Prüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    die("Keine Nutzerdaten.");
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT name, Bild, description FROM WorkoutPlan WHERE plan_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    die("Plan nicht gefunden.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $bild_hochgeladen = false;

    if (isset($_FILES['Bild']) && $_FILES['Bild']['error'] == UPLOAD_ERR_OK) {
        // Einzigartigen Dateinamen für das Bild generieren
        $uploaddir = '../../img/plan_bilder/';
        $originaldateiname = pathinfo($_FILES["pl_bild"]["name"], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES["pl_bild"]["name"], PATHINFO_EXTENSION);
        $uniquefilename = $originaldateiname . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadfile = $uploaddir . $uniquefilename;

        // Datei in das Zielverzeichnis verschieben
        if (move_uploaded_file($_FILES['pl_bild']['tmp_name'], $uploadfile)) {
            $bild_hochgeladen = true;
            echo "Bild erfolgreich hochgeladen.<br>";
        } else {
            die("Fehler beim Hochladen des Bildes.");
        }
    }

    // SQL-Abfrage vorbereiten: Unterscheidung ob Bild aktualisiert wird oder nicht
    if ($bild_hochgeladen) {
        // Wenn ein neues Bild hochgeladen wurde, auch das Bild aktualisieren
        $sql_update = "UPDATE WorkoutPlan SET name = ?, description = ?, Bild = ? WHERE plan_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $name, $description, $uniquefilename, $plan_id);
    } else {
        // Wenn kein neues Bild hochgeladen wurde, nur Name und Beschreibung aktualisieren
        $sql_update = "UPDATE WorkoutPlan SET name = ?, description = ? WHERE plan_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $name, $description, $plan_id);
    }

    // Update ausführen und Erfolg prüfen
    if ($stmt_update->execute()) {
        echo "Plan erfolgreich aktualisiert!";
    } else {
        echo "Fehler beim Aktualisieren des Plans: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Plan Bearbeiten</title>
    <link rel="stylesheet" href="../../css/einzelseite.css">
</head>
<body>
<div class="container_main">
    <div class="form_main">
        <h2>Bearbeite Plan: <?php echo htmlspecialchars($row['name']); ?></h2>

        <form action="" method="post" enctype="multipart/form-data" id="UpdatePlanForm">
            <div class="form_group">
                <label for="plan_name">Plan Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="form_group">
                <label for="description">Beschreibung:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($row['description']); ?></textarea>
            </div>

            <div class="form_group">
                <label for="pl_bild">Bild aktualisieren:</label>
                <!-- Aktuelles Bild anzeigen -->
                <img src="../../img/Plan_bilder/<?php echo !empty($row['bild']) ? htmlspecialchars($row['Bild']) : 'random.png'; ?>" alt="Plan Bild" style="max-width: 200px;">
                <!-- Datei-Upload für neues Bild -->
                <input type="file" id="pl_bild" name="pl_bild">
            </div>

            <div class="form_group">
                <input type="submit" name="submit" value="submit" required>
            </div>
        </form>
    </div>
</div>
</body>
</html>