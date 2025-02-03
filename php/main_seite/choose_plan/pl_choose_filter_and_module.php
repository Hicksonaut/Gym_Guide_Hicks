<?php
global $ziel_pl, $muscle_pl, $level_pl, $equipment_pl, $is_universal_pl, $liked_pl, $trainingstage_pl;
include '../../check_login.php';
include '../../pl_filter.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plans</title>
</head>
<body>
<div class="filter-container">
    <form id="filterform">

        <div class="mobile-filter-controls">
            <button type="button" id="mobileFilterToggle" class="mobile-only">☰ Filter</button>
            <div class="mobile-search-container mobile-only">
                <button type="button" id="Pl_erstellen_mobile"  onclick="load_pl_choose_days()">Fertig</button>
            </div>
        </div>


        <div id="filterContent">
        <label for="Trainingsziel">Trainingsziel</label>
        <select id="trainingsziel" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($ziel_pl as $goal) {
                echo '<option value="'.$goal.'">'.$goal.'</option>';
            }
            ?>
        </select>

        <label for="Body_part">Zielmuskel</label>
        <select id="body_part" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($muscle_pl as $body) {
                echo '<option value="'.$body.'">'.$body.'</option>';
            }
            ?>
        </select>

        <label for="Level">Level</label>
        <select id="level" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($level_pl as $lev) {
                echo '<option value="'.$lev.'">'.$lev.'</option>';
            }
            ?>
        </select>

        <label for="Equipment_requierd">Benötigte Ausrüstung</label>
        <select id="equipment" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($equipment_pl as $equipment_req) {
                echo '<option value="'.$equipment_req.'">'.$equipment_req.'</option>';
            }
            ?>
        </select>

        <label for="is_universal">Universal</label>
        <select id="is_universal" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($is_universal_pl as $universal) {
                $displayText = $universal == 1 ? "true" : "false";
                echo '<option value="'.$universal.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <label for="trainingstage">Tage</label>
        <select id="trainingstage" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($trainingstage_pl as $tage) {
                echo '<option value="'.$tage.'">'.$tage.'</option>';
            }
            ?>
        </select>

        <label for="liked">Favorite</label>
        <select id="liked" onchange="applyFilters('plan')">
            <option value="">All</option>
            <?php
            foreach ($liked_pl as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo '<option value="'.$like.'">'.$displayText.'</option>';
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFilters('plan')">Reset</button>
        </div>

        <button type="button" id="PL_erstellen" class="desktop-only" onclick="load_pl_choose_days()">Fertig</button>
    </form>
</div>

<?php
include 'pl_choose_module.php';
?>

<?php
include '../../Impressum/impressum_link_zeile.php';
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