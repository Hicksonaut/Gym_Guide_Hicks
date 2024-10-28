function applyFilters() {
    var targetMuscle = document.getElementById('targetMuscle').value.toUpperCase();
    var equipment = document.getElementById('equipment').value.toUpperCase();
    var mechanics = document.getElementById('mechanics').value.toUpperCase();
    var experienceLevel = document.getElementById('experienceLevel').value.toUpperCase();
    var liked = document.getElementById('liked').value.toUpperCase();
    var searchQuery = document.getElementById('exercise_search').value.toUpperCase();

    var exercises = document.getElementsByClassName('exercise-card');

    for (var i = 0; i < exercises.length; i++) {
        var exercise = exercises[i];

        var txtMuscle = exercise.querySelector('.exercise-attribut-border:nth-child(1)').innerText.toUpperCase();
        var txtEquipment = exercise.querySelector('.exercise-attribut-border:nth-child(2)').innerText.toUpperCase();
        var txtMechanics = exercise.querySelector('.exercise-attribut-border:nth-child(3)').innerText.toUpperCase();
        var txtExperience = exercise.querySelector('.exercise-attribut-border:nth-child(4)').innerText.toUpperCase();
        var txtName = exercise.querySelector('.exercise-name').innerText.toUpperCase();
        var txtliked = exercise.dataset.likedex.toUpperCase();

        if (
            (targetMuscle === "" || targetMuscle === txtMuscle) &&
            (equipment === "" || equipment === txtEquipment) &&
            (mechanics === "" || mechanics === txtMechanics) &&
            (experienceLevel === "" || experienceLevel === txtExperience) &&
            (liked === "" || liked === txtliked) &&
            (searchQuery === "" || txtName.includes(searchQuery) || txtMuscle.includes(searchQuery) || txtEquipment.includes(searchQuery) || txtMechanics.includes(searchQuery) || txtExperience.includes(searchQuery))
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
    document.getElementById('liked').value = "";

    var exercises = document.getElementsByClassName('exercise-card');
    for (var i = 0; i < exercises.length; i++) {
        exercises[i].style.display = "";
    }
}

function toggleLike(element, exercise_id) {
    event.stopPropagation();

    element.classList.toggle('active');
    const liked = element.classList.contains('active') ? 1 : 0;
    const bodyData = `exercise_id=${encodeURIComponent(exercise_id)}&liked=${encodeURIComponent(liked)}`;

    const icon = element.querySelector('img.heart-icon')
    icon.src = liked ? "svg/heart_filled.svg" : "/svg/heart-svgrepo-com.svg";


    fetch('php/like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyData
    })
        .then(response => response.text())
        .then(text => {
            const data = JSON.parse(text);
            if (data.Status === "Success") { // Hier nach Status suchen
                console.log("Like-Status erfolgreich aktualisiert");
            } else {
                console.error("Fehler beim Aktualisieren des Like-Status");
                console.log(data); // Gebe die Daten aus, wenn es einen Fehler gibt
            }
        })
        .catch(error => console.error("Fehler beim Senden der Anfrage", error));
}


