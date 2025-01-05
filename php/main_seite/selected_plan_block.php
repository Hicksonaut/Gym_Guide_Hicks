<?php
session_start();
global $conn;
include __DIR__ . '/../../php/datenbank_connection.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} elseif (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
    $_SESSION['user_id'] = $user_id;
} else {
    echo "Kein Benutzer angemeldet!<br>";
    exit();
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
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();




       echo "<div class='card'>
            <div class='card-header'>
                <h2 class='card-title'>Selected Plan</h2>
                <span onclick='load_pl_choose_filter_and_module()'>All Plans</span>
            </div>
            <div class='card-body'>";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='card-item-space-between' onclick='load_einzelseite_pl(".htmlspecialchars($row['plan_id']).")'>";
                echo "<img class='card-img' src='../../img/Plan_bilder/". htmlspecialchars($row['Bild']) ."' alt='PlanBild'>";
                echo "<h3>".htmlspecialchars($row['name'])."</h3>";
                echo "</div>";
            }
        } else {
            echo "<div class='card-item-center' onclick='load_pl_choose_filter_and_module()'>";
            echo "<img class='card-svg' src='../../svg/plus.svg' alt='check'>";
            echo "<p>Bitte Wähle ein Plan aus.</p>";
            echo "</div>";
        }

echo"       </div>
        </div>";