<?php
session_start();
global $conn;
include 'datenbank_connection.php';


// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Benutzer-Eingaben aus dem Formular
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];
    $confirm_password = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Ungültige E-Mail-Adresse!";
        exit;
    }

    // Passwort bestätigen
    if ($password != $confirm_password) {
        echo "Passwörter stimmen nicht überein!";
        exit;
    }
    // Überprüfen, ob die E-Mail bereits registriert ist
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Diese E-Mail ist bereits registriert!";
        exit;
    }

    // Überprüfen, ob der Benutzername bereits vergeben ist
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Dieser Benutzername ist bereits vergeben!";
        exit;
    }


    // Passwort hashen (WICHTIG für Sicherheit)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        //session_start();
        $_SESSION['user_id'] = $conn->insert_id;

        header("Location: /second_register.html");
        exit;
    } else {
        echo "Fehler: " . $stmt->error;
        exit;
    }

    $stmt->close();

}

// Verbindung schließen
$conn->close();
?>
