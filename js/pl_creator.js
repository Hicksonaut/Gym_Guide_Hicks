function applyFiltersPlCreator() {
    var trainingsziel = document.getElementById('trainingsziel').value.toUpperCase();
    var body_part = document.getElementById('body_part').value.toUpperCase();
    var equipment = document.getElementById('equipment').value.toUpperCase();
    var is_universal = document.getElementById('is_universal').value.toUpperCase();
    var level = document.getElementById('level').value.toUpperCase();
    var liked = document.getElementById('liked').value.toUpperCase();

    var workouts = document.getElementsByClassName('workout-card');

    for (var i = 0; i < workouts.length; i++) {
        var workout = workouts[i];

        var txtziel = workout.querySelector('.workout-attribut-border:nth-child(1)').innerText.toUpperCase();
        var txtbody = workout.querySelector('.workout-attribut-border:nth-child(2)').innerText.toUpperCase();
        var txtequipment = workout.querySelector('.workout-attribut-border:nth-child(3)').innerText.toUpperCase();

        var txtuniversal = workout.dataset.isUniversal.toUpperCase();
        var txtlevel = workout.dataset.level.toUpperCase();
        var txtliked = workout.dataset.liked.toUpperCase();

        if (
            (trainingsziel === "" || trainingsziel === txtziel) &&
            (body_part === "" || body_part === txtbody) &&
            (equipment === "" || equipment === txtequipment) &&
            (is_universal === "" || is_universal === txtuniversal) &&
            (level === "" || level === txtlevel) &&
            (liked === "" || liked === txtliked)
        ) {
            workout.style.display = "";
        } else {
            workout.style.display = "none";
        }
    }
}

function resetFiltersPl() {
    document.getElementById('trainingsziel').value = "";
    document.getElementById("body_part").value = "";
    document.getElementById('equipment').value = "";
    document.getElementById('is_universal').value = "";
    document.getElementById('level').value = "";
    document.getElementById('liked').value = "";

    var workouts = document.getElementsByClassName('workout-card');
    for (var i = 0; i < workouts.length; i++) {
        workouts[i].style.display = "";
    }
}

function addToListPl(workoutId) {
    var planID = "<?php echo $_SESSION['plan_id']; ?>";

    if (!planID) {
        alert("kein Plan ausgewählt.");
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST","../php/user_pl_erstellen/update_link_plan_workout.php",true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Erfolg: Rückmeldung anzeigen oder UI anpassen
            var response = xhr.responseText;
            if (response == "added") {
                alert("Workout hinzugefügt.");
            } else if (response == "removed") {
                alert("Workout entfernt.");
            } else {
                /* alert("Fehler: " + response);*/
            }
        }
    };
    updatePlanDetails();
    // Anfrage mit Daten (workout_id und exercise_id) senden
    xhr.send("plan_id=" + planID + "&workout_id=" + workoutId);
}

function updatePlanDetails() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/user_pl_erstellen/update_plan_details.php", true);
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

function Pl_abbrechen() {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/user_pl_erstellen/delete_plan.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText;
            console.log("Server Response:", response); // Debug-Ausgabe
            if (response == "deleted") {
                alert("Plan abgebrochen und gelöscht.");
                // Optionale UI-Aktualisierung, z.B. Weiterleitung oder Entfernen der UI-Elemente
            } else {
                // alert("Fehler beim Abbrechen des Workouts: " + response);
            }
        }
    };

    // Anfrage mit Daten (workout_id) senden
    xhr.send();
    loadplan()
}

