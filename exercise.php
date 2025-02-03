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
    <link rel="stylesheet" href="css/Module.css">
</head>
<body>
<div class="filter-container">
    <form id="filterForm">
        <div class="mobile-filter-controls">
        <!-- Mobile -->
        <button type="button" id="mobileFilterToggle" class="mobile-only">☰ Filter</button>
        <div class="mobile-search-container mobile-only">
            <input type="search" class="module_search" id="exercise_search" placeholder="Search..." oninput="applyFilters('exercise')">
        </div>
        </div>

        <div id="filterContent">
        <label for="targetMuscle">Target Muscle:</label>
        <select id="targetMuscle" onchange="applyFilters('exercise')">
            <option value="">All</option>
            <?php
            foreach ($target_muscles as $Muscle) {
                echo "<option value='" . $Muscle . "'>" . $Muscle . "</option>";
            }
            ?>
        </select>

        <label for="equipment">Equipment Required:</label>
        <select id="equipment" onchange="applyFilters('exercise')">
            <option value="">All</option>
            <?php
            foreach ($equipment_requierd as $equipment) {
                echo "<option value='" . htmlspecialchars($equipment) . "'>" . htmlspecialchars($equipment) . "</option>";
            }
            ?>
        </select>

        <label for="mechanics">Mechanics:</label>
        <select id="mechanics" onchange="applyFilters('exercise')">
            <option value="">All</option>
            <?php
            foreach ($mechanics as $mechanic) {
                echo "<option value='" . htmlspecialchars($mechanic) . "'>" . htmlspecialchars($mechanic) . "</option>";
            }
            ?>
        </select>

        <label for="experienceLevel">Experience Level:</label>
        <select id="experienceLevel" onchange="applyFilters('exercise')">
            <option value="">All</option>
            <?php
            foreach ($experience_level as $level) {
                echo "<option value='" . htmlspecialchars($level) . "'>" . htmlspecialchars($level) . "</option>";
            }
            ?>
        </select>

        <label for="liked">Favorites</label>
        <select id="liked" onchange="applyFilters('exercise')">
            <option value="">All</option>
            <?php
            foreach ($liked as $like) {
                $displayText = $like == 1 ? "true" : "false";
                echo "<option value='" . $like . "'>" . $displayText . "</option>";
            }
            ?>
        </select>

        <button type="button" id="resetButton" onclick="resetFilters('exercise')">Reset Filters</button>
        </div>

        <input type="search" class="module_search desktop-only" id="exercise_search" placeholder="Search Exercises:..." oninput="applyFilters('exercise')">
    </form>
</div>

<?php
include 'php/ex_moduls.php';
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
