<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../check_login.php';
global $conn;
include '../datenbank_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["workout_name"];
    $uploaddir = "../../img/workout_bilder/";
    $user_id = $_SESSION["user_id"];

    if (isset($_FILES["wk_bild"]) && $_FILES["wk_bild"]["error"] === UPLOAD_ERR_OK) {
        $originaldateiname = pathinfo($_FILES["wk_bild"]["name"], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES["wk_bild"]["name"], PATHINFO_EXTENSION);
        $uniquefilename = $originaldateiname . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadfile = $uploaddir . $uniquefilename;
    } else {
        echo "Keine Datei hochgeladen oder Fehler beim Hochladen.";
        exit;
    }

    $stmt = $conn->prepare("SELECT workout_name FROM workouts WHERE workout_name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Der Name existiert bereits.";
    } else {
        if (move_uploaded_file($_FILES["wk_bild"]["tmp_name"], $uploadfile)) {
            $stmt = $conn->prepare("INSERT INTO workouts (workout_name,wk_bild,creator_user_id) VALUES (?,?,?);");
            $stmt->bind_param("ssi", $name, $uniquefilename, $user_id);
            #$stmt->execute();

            if ($stmt->execute()) {
                echo "Workout angelegt";

                $workout_id = $conn->insert_id;

                $_SESSION['workout_id'] = $workout_id;

                echo "Workout angelegt mit der ID: " . $workout_id;

            } else {
                echo "Workout nicht angelegt";
            }
        } else {
            echo "Fehler beim Hochladen.";
        }
    }
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User_wk_erstellen</title>
    <link href="">
    <link rel="stylesheet" href="../../css/Module.css">
    <link rel="stylesheet" href="../../css/user_wk_erstellen.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
          rel="stylesheet">
</head>
<body>
<div class="container_main">
    <div class="form_main">
        <h2>Add own Workout</h2>
        <form action="" method="post" enctype="multipart/form-data" id="createWorkoutForm">
            <div class="form_group">
                <label>Workout Titel</label>
                <input type="text" name="workout_name">
            </div>
            <div class="form_group">
                <label>Workout Bild</label>
                <input type="file" name="wk_bild" id="wk_bild" required>
            </div>
            <div class="form_group">
                <input type="submit" name="submit" value="submit" required>
            </div>
        </form>
    </div>
</div>
</body>
</html>