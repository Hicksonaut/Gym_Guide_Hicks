function applyFiltersWk() {
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

function resetFiltersWk() {
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

function toggleLikeWk(element, workout_id) {
    element.classList.toggle('active');
    const liked = element.classList.contains('active') ? 1 : 0;
    const bodyData = `workout_id=${encodeURIComponent(workout_id)}&liked=${encodeURIComponent(liked)}`;


    const icon = element.querySelector('img.heart-icon-wk')
    if (liked) {
        icon.src = "/svg/heart_filled.svg"
    } else {
        icon.src = "/svg/heart-svgrepo-com.svg"
    }

    fetch('php/likeWk.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyData
    })
        .then(response => response.text())
        .then(text => {
            const data = JSON.parse(text);
            if (data.Status === "Success") {
                console.log("Like-Status erfolgreich aktualisiert");
            } else {
                console.log("Fehler: ", data);
            }
        })
        .catch(error => console.log("Fehler beim Senden der Anfrage", error))
}