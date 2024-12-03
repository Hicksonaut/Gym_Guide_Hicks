function applyFiltersPl() {
    var trainingsziel = document.getElementById('trainingsziel').value.toUpperCase();
    var body_part = document.getElementById('body_part').value.toUpperCase();
    var equipment = document.getElementById('equipment').value.toUpperCase();
    var is_universal = document.getElementById('is_universal').value.toUpperCase();
    var level = document.getElementById('level').value.toUpperCase();
    var liked = document.getElementById('liked').value.toUpperCase();
    var tage = document.getElementById('trainingstage').value;

    var plans = document.getElementsByClassName('module-card');

    for (var i = 0; i < plans.length; i++) {
        var plan = plans[i];

        var txtziel = plan.querySelector('.module-attribut-border-four:nth-child(1)').innerText.toUpperCase();
        var txtbody = plan.querySelector('.module-attribut-border-four:nth-child(2)').innerText.toUpperCase();
        var txtequipment = plan.querySelector('.module-attribut-border-four:nth-child(3)').innerText.toUpperCase();

        var txtuniversal = plan.dataset.isUniversal.toUpperCase();
        var txtlevel = plan.dataset.level.toUpperCase();
        var txtliked = plan.dataset.liked.toUpperCase();
        var txttage = plan.dataset.tage;

        if (
            (trainingsziel === "" || trainingsziel === txtziel) &&
            (body_part === "" || body_part === txtbody) &&
            (equipment === "" || equipment === txtequipment) &&
            (is_universal === "" || is_universal === txtuniversal) &&
            (level === "" || level === txtlevel) &&
            (liked === "" || liked === txtliked) &&
            (tage === "" || tage === txttage)
        ) {
            plan.style.display = "";
        } else {
            plan.style.display = "none";
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
    document.getElementById('trainingstage').value = "";

    var plans = document.getElementsByClassName('module-card');
    for (var i = 0; i < plans.length; i++) {
        plans[i].style.display = "";
    }
}

function toggleLikePl(element, plan_id) {
    event.stopPropagation();

    element.classList.toggle('active');
    const liked = element.classList.contains('active') ? 1 : 0;
    const bodyData = `plan_id=${encodeURIComponent(plan_id)}&liked=${encodeURIComponent(liked)}`;

    const icon = element.querySelector('img.heart-icon')
    if (liked) {
        icon.src = "../svg/heart_filled.svg"
    } else {
        icon.src = "../svg/heart-svgrepo-com.svg"
    }

    fetch('../php/likePl.php', {
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