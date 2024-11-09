<?php
global $target_muscles, $equipment_requierd, $mechanics, $experience_level, $liked;
include 'php/check_login.php';
include 'php/ex_filter.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exercise</title>
    <link rel="stylesheet" href="css/Exercise.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
          rel="stylesheet">
    <script src="js/exersice.js"></script>
</head>
<body>
<div class="filter-container">
    <form id="filterForm">
        <label for="targetMuscle">Target Muscle:</label>
        <select id="targetMuscle" onchange="applyFilters()">
            <option value="">All</option>
            <?php
            foreach ($target_muscles as $Muscle) {
                echo "<option value='" . $Muscle . "'>" . $Muscle . "</option>";
            }
            ?>
        </select>

        <label for="equipment">Equipment Required:</label>
        <select id="equipment" onchange="applyFilters()">
            <option value="">All</option>
            <?php
            foreach ($equipment_requierd as $equipment) {
                echo "<option value='" . htmlspecialchars($equipment) . "'>" . htmlspecialchars($equipment) . "</option>";
            }
            ?>
        </select>

        <label for="mechanics">Mechanics:</label>
        <select id="mechanics" onchange="applyFilters()">
            <option value="">All</option>
            <?php
            foreach ($mechanics as $mechanic) {
                echo "<option value='" . htmlspecialchars($mechanic) . "'>" . htmlspecialchars($mechanic) . "</option>";
            }
            ?>
        </select>

        <label for="experienceLevel">Experience Level:</label>
        <select id="experienceLevel" onchange="applyFilters()">
            <option value="">All</option>
            <?php
            foreach ($experience_level as $level) {
                echo "<option value='" . htmlspecialchars($level) . "'>" . htmlspecialchars($level) . "</option>";
            }
            ?>
        </select>

        <label for="liked">Favorites</label>
        <select id="liked" onchange="applyFilters()">
            <option value="">All</option>
            <?php
            foreach ($liked as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo "<option value='" . $like . "'>" . $displayText . "</option>";
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFilters()">Reset Filters</button>
        <input type="search" id="exercise_search" placeholder="Search Exercises:..." oninput="applyFilters()">
    </form>
</div>

<?php
include 'php/ex_moduls.php';
?>

<?php
include 'php/Impressum/impressum_link_zeile.php';
?>

<button id="scrollToTopBtn" title="Nach oben scrollen">⬆️</button>
</body>
</html>
