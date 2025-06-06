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
    <link rel="stylesheet" href="css/Module.css">
</head>
<body>
<div class="filter-container">
    <form id="filterform">

        <div class="mobile-filter-controls">
            <button type="button" id="mobileFilterToggle" class="mobile-only">☰ Filter</button>
            <div class="mobile-search-container mobile-only">
                <button type="button" id="Wk_erstellen_mobile"  onclick="load_user_erstellt_workout()">Add New</button>
            </div>
        </div>

        <div id="filterContent">
        <label for="Trainingsziel">Trainingsziel</label>
        <select id="trainingsziel" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($trainingsziel as $goal) {
                echo '<option value="'.$goal.'">'.$goal.'</option>';
            }
            ?>
        </select>

        <label for="Body_part">Zielmuskel</label>
        <select id="body_part" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($body_part as $body) {
                echo '<option value="'.$body.'">'.$body.'</option>';
            }
            ?>
        </select>

        <label for="Level">Level</label>
        <select id="level" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($Level as $lev) {
                echo '<option value="'.$lev.'">'.$lev.'</option>';
            }
            ?>
        </select>

        <label for="Equipment_requierd">Benötigte Ausrüstung</label>
        <select id="equipment" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($equipment as $equipment_req) {
                echo '<option value="'.$equipment_req.'">'.$equipment_req.'</option>';
            }
            ?>
        </select>

        <label for="is_universal">Universal</label>
        <select id="is_universal" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($is_universal as $universal) {
                $displayText = $universal == 1 ? "true" : "false";
                echo '<option value="'.$universal.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <label for="liked">Favorite</label>
        <select id="liked" onchange="applyFilters('workout')">
            <option value="">All</option>
            <?php
            foreach ($liked as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo '<option value="'.$like.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButtonWk" onclick="resetFilters('workout')">Reset</button>
        </div>

        <button type="button" id="Wk_erstellen" class="desktop-only" onclick="load_user_erstellt_workout()">Add New</button>
    </form>
</div>

<?php
include 'php/wk_moduls.php';
?>

<?php
include 'php/Impressum/impressum_link_zeile.php';
?>

<script>
    // Mobile Filter Toggle
    document.getElementById('mobileFilterToggle').addEventListener('click', function() {
        const filterContent = document.getElementById('filterContent');
        filterContent.classList.toggle('show');
    });

    // Schließen bei Klick außerhalb
    document.addEventListener('click', function(e) {
        const filterContent = document.getElementById('filterContent');
        const toggleBtn = document.getElementById('mobileFilterToggle');

        if (!toggleBtn.contains(e.target) && !filterContent.contains(e.target)) {
            filterContent.classList.remove('show');
        }
    });
</script>
<button id="scrollToTopBtn" title="Nach oben scrollen">⬆️</button>
</body>
</html>