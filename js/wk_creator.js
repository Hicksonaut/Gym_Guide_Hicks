function applyFiltersWkCreator() {
    var targetMuscle = document.getElementById('targetMuscle').value.toUpperCase();
    var equipment = document.getElementById('equipment').value.toUpperCase();
    var mechanics = document.getElementById('mechanics').value.toUpperCase();
    var experienceLevel = document.getElementById('experienceLevel').value.toUpperCase();
   // var searchQuery = document.getElementById('exercise_search').value.toUpperCase();
    var added = document.getElementById('added').value.toUpperCase();

    var exercises = document.getElementsByClassName('exercise-card');

    for (var i = 0; i < exercises.length; i++) {
        var exercise = exercises[i];

        var txtMuscle = exercise.querySelector('.exercise-attribut-border:nth-child(1)').innerText.toUpperCase();
        var txtEquipment = exercise.querySelector('.exercise-attribut-border:nth-child(2)').innerText.toUpperCase();
        var txtMechanics = exercise.querySelector('.exercise-attribut-border:nth-child(3)').innerText.toUpperCase();
        var txtExperience = exercise.querySelector('.exercise-attribut-border:nth-child(4)').innerText.toUpperCase();
        var txtName = exercise.querySelector('.exercise-name').innerText.toUpperCase();
        var txtadded = exercise.dataset.added.toUpperCase();

        if (
            (targetMuscle === "" || targetMuscle === txtMuscle) &&
            (equipment === "" || equipment === txtEquipment) &&
            (mechanics === "" || mechanics === txtMechanics) &&
            (experienceLevel === "" || experienceLevel === txtExperience) &&
            (added === "" || added === txtadded)
        //    (searchQuery === "" || txtName.includes(searchQuery) || txtMuscle.includes(searchQuery) || txtEquipment.includes(searchQuery) || txtMechanics.includes(searchQuery) || txtExperience.includes(searchQuery))
        ) {
            exercise.style.display = "";
        } else {
            exercise.style.display = "none";
        }
    }
}

function resetFilters() {
    document.getElementById('targetMuscle').value = "";
    document.getElementById('equipment').value = "";
    document.getElementById('mechanics').value = "";
    document.getElementById('experienceLevel').value = "";
    document.getElementById('added').value = "";

    var exercises = document.getElementsByClassName('exercise-card');
    for (var i = 0; i < exercises.length; i++) {
        exercises[i].style.display = "";
    }
}

function addToList(exerciseId) {
    var workoutID = "<?php echo $_SESSION['workout_id']; ?>";

    if (!workoutID) {
        alert("kein Workout ausgewählt.");
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST","../php/user_wk_erstellen/update_link_workout_exercise.php",true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Erfolg: Rückmeldung anzeigen oder UI anpassen
            var response = xhr.responseText.trim();
            var card = document.querySelector(`.module-card[data-exercise-id='${exerciseId}']`);
            var icon = card.querySelector(".plus-icon-img");

            if (response == "added") {
                icon.src = "../svg/check-circle.svg";
                icon.title = "check";
            } else if (response == "removed") {
                icon.src = "../svg/plus.svg";
                icon.title = "plus";
            } else {
                alert("Fehler: " + response);
            }
        }
    };
    updateWorkoutDetails();
    // Anfrage mit Daten (workout_id und exercise_id) senden
    xhr.send("workout_id=" + workoutID + "&exercise_id=" + exerciseId);
}

function updateWorkoutDetails() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/user_wk_erstellen/update_workout_details.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            //alert(response); // Show success or error message
        }
    };

    // Send the request with the workout_id
    xhr.send();
}

function WK_abbrechen() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/user_wk_erstellen/delete_workout.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            console.log("Server Response:", response); // Debug-Ausgabe
            if (response == "deleted") {
                //("Workout abgebrochen und gelöscht.");
                // Optionale UI-Aktualisierung, z.B. Weiterleitung oder Entfernen der UI-Elemente
            } else {
               // alert("Fehler beim Abbrechen des Workouts: " + response);
            }
        }
    };

    // Anfrage mit Daten (workout_id) senden
    xhr.send();
    loadworkout()
}

