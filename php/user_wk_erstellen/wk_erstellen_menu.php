<?php
global $target_muscles, $equipment_requierd, $mechanics, $experience_level,$liked,$aditional;
include '../check_login.php';
include '../ex_filter.php';
global $conn;
$workout_id = $_SESSION['workout_id'];
$sql = "SELECT workout_name FROM workouts WHERE workout_id = $workout_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $workout_name = $row['workout_name'];
} else {
    $workout_name = "Workout nicht gefunden";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exercise</title>
    <link href="../../js/wk_creator.js">
    <link rel="stylesheet" href="../../css/Module.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"
          rel="stylesheet">
    <script src="../../js/wk_creator.js"></script>
</head>
<body>
<h2 style="color: white">Add your Exercises to the Workout: <?php echo htmlspecialchars($workout_name)?></h2>
<div class="filter-container">
    <form id="filterForm">
        <label for="targetMuscle">Target Muscle:</label>
        <select id="targetMuscle" onchange="applyFiltersWkCreator()">
            <option value="">All</option>
            <?php
            foreach ($target_muscles as $Muscle) {
                echo "<option value='" . $Muscle . "'>" . $Muscle . "</option>";
            }
            ?>
        </select>

        <label for="equipment">Equipment Required:</label>
        <select id="equipment" onchange="applyFiltersWkCreator()">
            <option value="">All</option>
            <?php
            foreach ($equipment_requierd as $equipment) {
                echo "<option value='" . htmlspecialchars($equipment) . "'>" . htmlspecialchars($equipment) . "</option>";
            }
            ?>
        </select>

        <label for="mechanics">Mechanics:</label>
        <select id="mechanics" onchange="applyFiltersWkCreator()">
            <option value="">All</option>
            <?php
            foreach ($mechanics as $mechanic) {
                echo "<option value='" . htmlspecialchars($mechanic) . "'>" . htmlspecialchars($mechanic) . "</option>";
            }
            ?>
        </select>

        <label for="experienceLevel">Experience Level:</label>
        <select id="experienceLevel" onchange="applyFiltersWkCreator()">
            <option value="">All</option>
            <?php
            foreach ($experience_level as $level) {
                echo "<option value='" . htmlspecialchars($level) . "'>" . htmlspecialchars($level) . "</option>";
            }
            ?>
        </select>

        <label for="added">Hinzugefügt:</label>
        <select id="added" onchange="applyFiltersWkCreator()">
            <option value="">All</option>
            <?php
            foreach ($aditional as $added) {
                $displayText = $added == 1 ? "true" : "false";
                echo '<option value="'.$added.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFilters()">Reset Filters</button>
        <input type="search" class="module_search" placeholder="Search Exercises:..." oninput="applyFiltersWkCreator()">
        <button type="button" id="wk_erstellen_abbrechen" onclick="WK_abbrechen()">Löschen</button>
        <button type="button" id="Workout_Fertigstellen" onclick="load_einzelseite_wk(<?php echo htmlspecialchars($workout_id); ?>)">Workout Fertigstellen</button>
    </form>
</div>
<div class="verfuegbare_ex_moduls">

    <?php
    include 'ex_moduls_wk_erstellen.php';
    ?>

<button id="scrollToTopBtn" title="Nach oben scrollen">⬆️</button>
</div>
</body>
</html>