<?php
session_start();
global $conn;
include 'datenbank_connection.php';

// Benutzer meldet sich an
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verifizierung der E-Mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Ungültige E-Mail-Adresse";
        exit;
    }

    // Passwort und E-Mail überprüfen mit der DB
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    if (!$stmt) {
        die("Fehler bei der Vorbereitung der SQL-Abfrage: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $datenbank_password);
        $stmt->fetch();

        // Passwort verifizieren
        if (password_verify($password, $datenbank_password)) {
            // Passwort korrekt, Session starten
            $_SESSION['user_id'] = $id;
            $_SESSION['logged_in'] = true;

            // Wenn "Erinnere mich" angehakt ist, setze ein Cookie
            if (isset($_POST['remember_me'])) {
                setcookie("user_id", $id, time() + (86400 * 30), "/", "", false, true); // 30 Tage // wenn ich hier auf https wechsle ein true hinzufügen
                echo "cookie wurde gesetzt";
            } else {
                echo "cookie nicht wurde gesetzt";
            }

            // Weiterleitung zur Hauptseite
            header("Location: /index.php");
            exit;
        } else {
            echo "Falsches Passwort.";
            exit;
        }
    } else {
        echo "E-Mail nicht gefunden.";
        exit;
    }
}

$conn->close();
?>