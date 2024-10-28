<?php
global $trainingsziel, $body_part, $Level, $equipment, $is_universal, $liked;
include 'php/check_login.php';
include 'php/wk_filter.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workouts</title>
    <link href="js/workout.js">
    <link rel="stylesheet" href="css/workout.css">
</head>
<body>
<div class="filter-container-Wk">
    <form id="filterform">
        <label for="Trainingsziel">Trainingsziel</label>
        <select id="trainingsziel" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($trainingsziel as $goal) {
                echo '<option value="'.$goal.'">'.$goal.'</option>';
            }
            ?>
        </select>

        <label for="Body_part">Zielmuskel</label>
        <select id="body_part" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($body_part as $body) {
                echo '<option value="'.$body.'">'.$body.'</option>';
            }
            ?>
        </select>

        <label for="Level">Level</label>
        <select id="level" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($Level as $lev) {
                echo '<option value="'.$lev.'">'.$lev.'</option>';
            }
            ?>
        </select>

        <label for="Equipment_requierd">Benötigte Ausrüstung</label>
        <select id="equipment" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($equipment as $equipment_req) {
                echo '<option value="'.$equipment_req.'">'.$equipment_req.'</option>';
            }
            ?>
        </select>

        <label for="is_universal">Universal</label>
        <select id="is_universal" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($is_universal as $universal) {
                $displayText = $universal == 1 ? "true" : "false";
                echo '<option value="'.$universal.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <label for="liked">Favorite</label>
        <select id="liked" onchange="applyFiltersWk()">
            <option value="">All</option>
            <?php
            foreach ($liked as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo '<option value="'.$like.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButtonWk" onclick="resetFiltersWk()">Reset</button>
        <button type="button" id="Wk_erstellen" onclick="load_user_erstellt_workout()">Add New</button>
    </form>
</div>

<?php
include 'php/wk_moduls.php';
?>

</body>
</html>