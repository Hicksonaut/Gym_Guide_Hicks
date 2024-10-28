<?php
session_start();
include 'php/check_login.php';
global $conn;
include 'php/datenbank_connection.php';

$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
if (!$stmt) {
    die("Fehler");
}

$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Gym Tracker</title>
    <link rel="icon" href="img/Logo.png">
    <link rel="stylesheet" href="css/main.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
          rel="stylesheet">
</head>

<body>
<div class="navbar_left" id="ID_Navbar_Left">
    <img src="svg/Logo.svg" alt="test" id="Logo_Icon_Top_Left">
    <img src="svg/home.svg" alt="Home" id="Home_Icon" class="icon">
    <h3 class="Navbar_Left_Text">Startseite</h3>
    <br>
    <img src="svg/graph.svg" alt="Stats" class="icon">
    <h3 class="Navbar_Left_Text">Stats</h3>
    <br>
    <img src="svg/calender.svg" alt="Ball" class="icon">
    <h3 class="Navbar_Left_Text">Kalender</h3>
    <br>
    <a href="#" id="exerciseLink" onclick="loadexercise()">
        <img src="svg/exersice.svg" alt="Exercise" class="icon">
        <h3 class="Navbar_Left_Text">Übungen</h3>
    </a>
    <br>
    <a href="#" id="workoutLink" onclick="loadworkout()">
        <img src="svg/workout.svg" alt="Workout" class="icon">
        <h3 class="Navbar_Left_Text">Workouts</h3>
    </a>
    <br>
    <a href="php/logout.php">
        <img src="svg/Log_Out.svg" alt="Log_Out" id="Log_Out_Icon" class="icon">
    </a>
</div>

<div class="navbar_top">
    <div style="display: flex">
        <div class="hamburger_menu" onclick="myFunction(this)">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <div class="Welcome_Message">
            <h4>Good Morning</h4>
            <h3>Welcome Back <?php echo htmlspecialchars($username) ?></h3>
            <!--
            hier den user austuaschen
            -->
        </div>
    </div>
    <img src="svg/Logo.svg" alt="Logo" id="Logo_Icon">
    <div class="User_Profile_Navbar_Top" onclick="UserFunction()">
        <img src="svg/User_Profile.svg" alt="User_Icon" class="avatar">
        <span class="username"><?php echo htmlspecialchars($username); ?></span>
        <span class="arrow">&#9660;</span>
    </div>
</div>

<div class="User_Profile_Navbar_Window" style="padding-top: 12px">
    <a href="#platzhalter">Einstellungen </a>
    <a href="#platzhalter">Freunde</a>
    <a href="#Platzhalter">Hilfe</a>
    <a href="#Platzhalter">Benachrichtigung</a>
    <a href="php/logout.php">Abmelden</a>
</div>

<!-- Ab hier kommt der Main Teil alles andere da rüber ist nur Navbars und andere Pop Ups -->

<div class="Main_Content" id="mainContent">
    <!--<div class="flex_1">
        <div class="Termine_der_Woche">

        </div>
        <div class="Kleine_Flex_Box">
            <div class="Gewicht_Anzeige">
                <img src="svg/weight.svg" alt="Waage" id="Waage_Icon">
                <div>
                    <p class="Text_in_Flex_Box">Gewicht</p>
                    <p class="Text_in_Flex_Box">+5.2kg</p>
                </div>
                <img src="svg/test_graph.svg" alt="Graph" class="Graph_Icon">
            </div>
            <div class="Kalorien_Anzeige">
                <img src="svg/calories.svg" alt="Calories" id="Kalorien_Icon">
                <div>
                    <p class="Text_in_Flex_Box">Kalorien</p>
                    <p class="Text_in_Flex_Box">-1.124</p>
                </div>
                <img src="svg/test_graph.svg" alt="Graph" class="Graph_Icon">
            </div>
        </div>
    </div>
    -->

</div>

<script>

    function myFunction(x) {
        x.classList.toggle("change");
        var navbar = document.querySelector('.navbar_left');
        var User_navbar = document.querySelector('.User_Profile_Navbar_Window');

        navbar.classList.toggle('show');

        if (User_navbar.classList.contains('show')) {
            User_navbar.classList.remove('show');
        }
    }

    function UserFunction() {
        var navbar = document.querySelector('.navbar_left');
        var User_navbar = document.querySelector('.User_Profile_Navbar_Window');
        var Hamburger_Icon = document.querySelector('.hamburger_menu');

        User_navbar.classList.toggle('show');

        if (navbar.classList.contains('show')) {
            navbar.classList.remove('show');
            Hamburger_Icon.classList.toggle('change');
        }
    }

    function loadexercise(){
        loadContent('exercise.php')
    }

    function loadworkout() {
        loadContent('workout.php');
    }

    function load_user_erstellt_workout() {
        loadContent('/php/user_wk_erstellen/user_wk_erstellen.php');
    }

    function load_wk_erstellen_menu() {
        loadContent('/php/user_wk_erstellen/wk_erstellen_menu.php');
    }

    function load_einzelseite_ex(exerciseId){
        loadContent(`/php/einzelseiten/ex_einzelseite.php?exercise_id=${exerciseId}`);
    }


    function loadContent(url) {
        fetch(url).then(response => response.text()).then(text => {
            document.getElementById('mainContent').innerHTML = text;
        }).catch(error => {
            console.error('Error loading content: ' + error); // Fehlerstatus anzeigen
        })
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Stelle sicher, dass dieses Skript erst läuft, wenn das Formular dynamisch geladen wurde.
        document.getElementById('mainContent').addEventListener('submit', function (e) {
            if (e.target && e.target.id === 'createWorkoutForm') {
                e.preventDefault(); // Standard-Formular-Übertragung verhindern

                let formData = new FormData(e.target); // Daten aus dem Formular holen

                fetch('php/user_wk_erstellen/user_wk_erstellen.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.text())
                    .then(data => {
                        console.log(data); // Hier kann eine Erfolgsmeldung angezeigt werden
                        load_wk_erstellen_menu(); // Seite neu laden oder andere Aktion
                    })
                    .catch(error => {
                        console.error('Fehler beim Erstellen des Workouts:', error);
                        alert("Fehler beim Erstellen des Workouts.");
                    });
            }
        });
    });

</script>
<script src="js/exersice.js"></script>
<script src="js/workout.js"></script>
<script src="js/wk_creator.js"></script>


</body>
</html>