<?php
session_start();
include 'php/check_login.php';
global $conn;
include 'php/datenbank_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $target_muscle = $_POST["target_muscle"];
    $exercise_type = $_POST["exercise_type"];
    $equipment = $_POST["equipment"];
    $mechanics = $_POST["mechanics"];
    $force_type = $_POST["force_type"];
    $experience = $_POST["experience_level"];

    $uploaddir = "img/Exercise_bilder/";

    if (!is_dir($uploaddir)) {
        mkdir($uploaddir, 0777, true); // Ordner mit Schreibrechten für alle Benutzer erstellen
    }

    if (isset($_FILES["bild"]) && $_FILES["bild"]["error"] === UPLOAD_ERR_OK) {
        $originaldateiname = pathinfo($_FILES["bild"]["name"], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES["bild"]["name"], PATHINFO_EXTENSION);
        $uniquefilename = $originaldateiname . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
        $uploadfile = $uploaddir . $uniquefilename;
    } else {
        echo "Keine Datei hochgeladen oder Fehler beim Hochladen.";
        exit;
    }

    $stmt = $conn-> prepare("SELECT name FROM exercises WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();


    if ($stmt->num_rows > 0) {
        echo "Übung mit diesem Namen existiert bereits.";
    } else {
        // Bild hochladen
        if (move_uploaded_file($_FILES["bild"]["tmp_name"], $uploadfile)) {
            // Datensatz in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO exercises (name, description, bild_ex, target_muscle, exercise_type, equipment_requierd, mechanics, force_type, experience_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $name, $description, $uniquefilename, $target_muscle, $exercise_type, $equipment, $mechanics, $force_type, $experience);

            if ($stmt->execute()) {
                echo "Übung erfolgreich hinzugefügt!";
            } else {
                echo "Fehler beim Einfügen in die Datenbank: " . $stmt->error;
            }
        } else {
            echo "Fehler beim Hochladen des Bildes.";
        }
    }
}

?>


<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Add Exercises</title>
    <style>
        body {
            background: #0b0b0b;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container_p {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            width: 100%;
            color: white;
        }
        .add_field {
            background: rgb(35,35,35);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 300px rgba(35, 35, 35, 0.9);
        }
        .element_group {
            align-items: center;
            margin-top: 10px;
        }
        h2 {
            margin-bottom: 20px;
            font-weight: bold;
        }
        input {
            padding: 6px;
            border-radius: 5px;
            font-size: 16px;
            margin: 10px 0;
            width: 90%;
            background: #D18239;
            border: none;
            color: white;
        }
    </style>
</head>
<body>
<div class="container_p">
    <div class="add_field">
        <h2>Add Exercises</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="element_group">
                <label>Name der Übung</label>
                <input type="text" name="name" >
            </div>
            <div class="element_group">
                <label>Description</label>
                <input type="text" name="description" >
            </div>
            <div class="element_group">
                <label>Bild</label>
                <input type="file" name="bild" id="bild" >
            </div>
            <div class="element_group">
                <label>Target Muskel</label>
                <input type="text" name="target_muscle" >
            </div>
            <div class="element_group">
                <label>Übungs Typ</label>
                <input type="text" name="exercise_type" >
            </div>
            <div class="element_group">
                <label>Equipment?</label>
                <input type="text" name="equipment" >
            </div>
            <div class="element_group">
                <label>Mechanics</label>
                <input type="text" name="mechanics" >
            </div>
            <div class="element_group">
                <label>force_type</label>
                <input type="text" name="force_type" >
            </div>
            <div class="element_group">
                <label>Experinece Level</label>
                <input type="text" name="experience_level" >
            </div>
            <div class="element_group">
                <input type="submit" name="submit" value="submit">
            </div>
        </form>
    </div>
</div>
</body>
</html>

