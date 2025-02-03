<?php
global $trainingsziel, $body_part, $Level, $equipment, $is_universal,$liked;
include '../check_login.php';
include '../wk_filter.php';
global $conn;
$plan_id = $_SESSION['plan_id'];
$sql = "SELECT name FROM workoutplan WHERE plan_id = $plan_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $name = $row['name'];
} else {
    $name = "Plan nicht gefunden";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exercise</title>
    <link href="../../js/pl_creator.js">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
          rel="stylesheet">
    <script src="../../js/pl_creator.js"></script>
</head>
<body>
<h2 style="color: white">Add your Workouts to the Plan: <?php echo htmlspecialchars($name)?></h2>
<div class="filter-container">
    <form id="filterForm">

        <div class="mobile-filter-controls">
            <button type="button" id="mobileFilterToggle" class="mobile-only">☰ Filter</button>
            <div class="mobile-search-container mobile-only">
                <input type="search" id="plan_erstellen_search" class="module_search"
                       placeholder="Search..." oninput="applyFilters('plan_erstellen')">
            </div>
        </div>

        <div id="filterContent">
        <label for="Trainingsziel">Trainingsziel</label>
        <select id="trainingsziel" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($trainingsziel as $goal) {
                echo '<option value="'.$goal.'">'.$goal.'</option>';
            }
            ?>
        </select>

        <label for="Body_part">Zielmuskel</label>
        <select id="body_part" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($body_part as $body) {
                echo '<option value="'.$body.'">'.$body.'</option>';
            }
            ?>
        </select>

        <label for="Level">Level</label>
        <select id="level" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($Level as $lev) {
                echo '<option value="'.$lev.'">'.$lev.'</option>';
            }
            ?>
        </select>

        <label for="Equipment_requierd">Benötigte Ausrüstung</label>
        <select id="equipment" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($equipment as $equipment_req) {
                echo '<option value="'.$equipment_req.'">'.$equipment_req.'</option>';
            }
            ?>
        </select>

        <label for="is_universal">Universal</label>
        <select id="is_universal" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($is_universal as $universal) {
                $displayText = $universal == 1 ? "true" : "false";
                echo '<option value="'.$universal.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <label for="liked">Favorite</label>
        <select id="liked" onchange="applyFilters('plan_erstellen')">
            <option value="">All</option>
            <?php
            foreach ($liked as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo '<option value="'.$like.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFilters('plan_erstellen')">Reset Filters</button>
        </div>


        <!-- Desktop Elements -->
        <div class="desktop-only">
            <input type="search" id="plan_erstellen_search_desktop" class="module_search" placeholder="Search Exercises..." oninput="applyFilters('plan_erstellen')">
        </div>

        <!-- Mobile Button Group -->
        <div class="mobile-button-group mobile-only">
            <button type="button" id="wk_erstellen_abbrechen" onclick="Pl_abbrechen()">Löschen</button>
            <button type="button" id="Workout_Fertigstellen"
                    onclick="load_einzelseite_pl(<?php echo htmlspecialchars($plan_id); ?>)">Fertigstellen</button>
        </div>

        <!-- Desktop Buttons -->
        <div class="desktop-only">
            <button type="button" id="wk_erstellen_abbrechen" onclick="Pl_abbrechen()">Löschen</button>
            <button type="button" id="Workout_Fertigstellen"
                    onclick="load_einzelseite_pl(<?php echo htmlspecialchars($plan_id); ?>)">Workout Fertigstellen</button>
        </div>
    </form>
</div>
<div class="verfuegbare_wk_moduls">

    <?php
    include 'wk_moduls_pl_erstellen.php';

    include "../Impressum/impressum_link_zeile.php";

    ?>

    <button id="scrollToTopBtn" title="Nach oben scrollen">⬆️</button>
</div>
</body>
</html>