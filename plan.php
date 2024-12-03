<?php
global $ziel_pl, $muscle_pl, $level_pl, $equipment_pl, $is_universal_pl, $liked_pl, $trainingstage_pl;
include 'php/check_login.php';
include 'php/pl_filter.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Workouts</title>
    <link href="js/plan.js">
    <link rel="stylesheet" href="css/Module.css">
</head>
<body>
<div class="filter-container">
    <form id="filterform">
        <label for="Trainingsziel">Trainingsziel</label>
        <select id="trainingsziel" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($ziel_pl as $goal) {
                echo '<option value="'.$goal.'">'.$goal.'</option>';
            }
            ?>
        </select>

        <label for="Body_part">Zielmuskel</label>
        <select id="body_part" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($muscle_pl as $body) {
                echo '<option value="'.$body.'">'.$body.'</option>';
            }
            ?>
        </select>

        <label for="Level">Level</label>
        <select id="level" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($level_pl as $lev) {
                echo '<option value="'.$lev.'">'.$lev.'</option>';
            }
            ?>
        </select>

        <label for="Equipment_requierd">Benötigte Ausrüstung</label>
        <select id="equipment" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($equipment_pl as $equipment_req) {
                echo '<option value="'.$equipment_req.'">'.$equipment_req.'</option>';
            }
            ?>
        </select>

        <label for="is_universal">Universal</label>
        <select id="is_universal" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($is_universal_pl as $universal) {
                $displayText = $universal == 1 ? "true" : "false";
                echo '<option value="'.$universal.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <label for="trainingstage">Tage</label>
        <select id="trainingstage" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($trainingstage_pl as $tage) {
                echo '<option value="'.$tage.'">'.$tage.'</option>';
            }
            ?>
        </select>

        <label for="liked">Favorite</label>
        <select id="liked" onchange="applyFiltersPl()">
            <option value="">All</option>
            <?php
            foreach ($liked_pl as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo '<option value="'.$like.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFiltersPl()">Reset</button>
        <button type="button" id="PL_erstellen" onclick="load_user_erstellt_plan()">Add New</button>
    </form>
</div>

<?php
include 'php/pl_moduls.php';
?>

<?php
include 'php/Impressum/impressum_link_zeile.php';
?>

<button id="scrollToTopBtn" title="Nach oben scrollen">⬆️</button>
</body>
</html>