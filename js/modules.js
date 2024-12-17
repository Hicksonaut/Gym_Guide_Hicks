function applyFilters(type) {

    let elements, filters, attributes;

    switch (type) {
        case 'workout':
            elements = document.getElementsByClassName('module-card');
            filters = {
                trainingsziel: document.getElementById('trainingsziel').value.toUpperCase(),
                body_part: document.getElementById('body_part').value.toUpperCase(),
                equipment: document.getElementById('equipment').value.toUpperCase(),
                is_universal: document.getElementById('is_universal').value.toUpperCase(),
                level: document.getElementById('level').value.toUpperCase(),
                liked: document.getElementById('liked').value.toUpperCase()

            };
            attributes = {
                trainingsziel: '.module-attribut-border-three:nth-child(1)',
                body_part: '.module-attribut-border-three:nth-child(2)',
                equipment: '.module-attribut-border-three:nth-child(3)',
                is_universal: 'data-is-universal',
                level: 'data-level',
                liked: 'data-liked'
            };
            break;
        case 'exercise':
            elements = document.getElementsByClassName('module-card');
            filters = {
                targetMuscle: document.getElementById('targetMuscle').value.toUpperCase(),
                equipment: document.getElementById('equipment').value.toUpperCase(),
                mechanics: document.getElementById('mechanics').value.toUpperCase(),
                experienceLevel: document.getElementById('experienceLevel').value.toUpperCase(),
                liked: document.getElementById('liked').value.toUpperCase(),
                searchQuery: document.getElementById('exercise_search').value.toUpperCase()
            };
            attributes = {
                targetMuscle: '.module-attribut-border-four:nth-child(1)',
                equipment: '.module-attribut-border-four:nth-child(2)',
                mechanics: '.module-attribut-border-four:nth-child(3)',
                experienceLevel: '.module-attribut-border-four:nth-child(4)',
                liked: 'data-likedex',
                name: '.module-name'
            };
            break;
        case 'plan':
            elements = document.getElementsByClassName('module-card');
            filters = {
                trainingsziel: document.getElementById('trainingsziel').value.toUpperCase(),
                body_part: document.getElementById('body_part').value.toUpperCase(),
                equipment: document.getElementById('equipment').value.toUpperCase(),
                is_universal: document.getElementById('is_universal').value.toUpperCase(),
                level: document.getElementById('level').value.toUpperCase(),
                liked: document.getElementById('liked').value.toUpperCase(),
                tage: document.getElementById('trainingstage').value.toUpperCase()
            };
            attributes = {
                trainingsziel: '.module-attribut-border-three:nth-child(1)',
                body_part: '.module-attribut-border-three:nth-child(2)',
                equipment: '.module-attribut-border-three:nth-child(3)',
                is_universal: 'data-is-universal',
                level: 'data-level',
                liked: 'data-liked',
                tage: 'data-tage'
            }
            break;
        case 'plan_erstellen':
            elements = document.getElementsByClassName('module-card');
            filters = {
                trainingsziel: document.getElementById('trainingsziel').value.toUpperCase(),
                body_part: document.getElementById('body_part').value.toUpperCase(),
                equipment: document.getElementById('equipment').value.toUpperCase(),
                is_universal: document.getElementById('is_universal').value.toUpperCase(),
                level: document.getElementById('level').value.toUpperCase(),
                searchQuery: document.getElementById('plan_erstellen_search').value.toUpperCase()
            };
            attributes = {
                trainingsziel: '.module-attribut-border-three:nth-child(1)',
                body_part: '.module-attribut-border-three:nth-child(2)',
                equipment: '.module-attribut-border-three:nth-child(3)',
                is_universal: 'data-is-universal',
                level: 'data-level',
                liked: 'data-liked'
            };
            break;
        case 'workout_erstellen':
            elements = document.getElementsByClassName('module-card');
            filters = {
                targetMuscle: document.getElementById('targetMuscle').value.toUpperCase(),
                equipment: document.getElementById('equipment').value.toUpperCase(),
                mechanics: document.getElementById('mechanics').value.toUpperCase(),
                experienceLevel: document.getElementById('experienceLevel').value.toUpperCase(),
                searchQuery: document.getElementById('workout_erstellen_search').value.toUpperCase(),
                added: document.getElementById('added').value.toUpperCase()
            };
            attributes = {
                targetMuscle: '.module-attribut-border-four:nth-child(1)',
                equipment: '.module-attribut-border-four:nth-child(2)',
                mechanics: '.module-attribut-border-four:nth-child(3)',
                experienceLevel: '.module-attribut-border-four:nth-child(4)',
                name: '.module-name',
                added: '.module-added'
            };
            break;
        default:
            console.error('Invalid filter type');
            return;
    }

    for (let i = 0; i < elements.length; i++) {
        let element = elements[i];
        let display = "";

        for (let key in filters) {
            if (filters[key] !== "") {
                let attributeValue;

                // Sonderfall für "searchQuery"
                if (key === 'searchQuery') {
                    // Suche in den definierten Attributen
                    attributeValue = Object.values(attributes).map(attr =>
                        attr.startsWith('data-')
                            ? element.dataset[attr.slice(5)]?.toUpperCase() || ''
                            : element.querySelector(attr)?.innerText.toUpperCase() || ''
                    ).join(' ');

                    // Wenn "searchQuery" nicht gefunden wird, setze Anzeige auf "none"
                    if (!attributeValue.includes(filters[key])) {
                        display = "none";
                        break;
                    }
                }
                // Standardfall für andere Filter
                else if (attributes[key]?.startsWith('data-')) {
                    attributeValue = element.dataset[attributes[key].slice(5)]?.toUpperCase() || '';
                    if (filters[key] !== attributeValue) {
                        display = "none";
                        break;
                    }
                } else {
                    attributeValue = element.querySelector(attributes[key])?.innerText.toUpperCase() || '';
                    if (filters[key] !== attributeValue) {
                        display = "none";
                        break;
                    }
                }
            }
        }

        element.style.display = display;
    }
}

function resetFilters(type) {
    let filterIds;

    switch (type) {
        case 'workout':
            filterIds = ['trainingsziel', 'body_part', 'equipment', 'is_universal', 'level', 'liked'];
            break;
        case 'exercise':
            filterIds = ['targetMuscle', 'equipment', 'mechanics', 'experienceLevel', 'liked'];
            break;
        case 'plan':
            filterIds = ['trainingsziel','body_part','equipment','is_universal','level','liked','trainingstage']
            break;
        case 'workout_erstellen':
            filterIds = ['targetMuscle', 'equipment', 'mechanics', 'experienceLevel', 'added'];
            break;
        case 'plan_erstellen':
            filterIds = ['trainingsziel', 'body_part', 'equipment', 'is_universal', 'level', 'added'];
            break;
        default:
            console.error('Invalid filter type');
            return;
    }

    // Reset all filter values
    filterIds.forEach(id => {
        document.getElementById(id).value = "";
    });

    // Reset search input if it exists (for exercises)
    const searchInput = document.getElementById('exercise_search');
    if (searchInput) {
        searchInput.value = "";
    }

    // Show all module cards
    const moduleCards = document.getElementsByClassName('module-card');
    for (let i = 0; i < moduleCards.length; i++) {
        moduleCards[i].style.display = "";
    }
}

function toggleLike(element, id, type) {
    event.stopPropagation();

    element.classList.toggle('active');
    const liked = element.classList.contains('active') ? 1 : 0;

    let bodyData, phpFile, iconPath;

    switch (type) {
        case 'workout':
            bodyData = `workout_id=${encodeURIComponent(id)}&liked=${encodeURIComponent(liked)}`;
            phpFile = '../php/wk_like.php';
            iconPath = liked ? "../svg/heart_filled.svg" : "../svg/heart-svgrepo-com.svg";
            break;
        case 'exercise':
            bodyData = `exercise_id=${encodeURIComponent(id)}&liked=${encodeURIComponent(liked)}`;
            phpFile = 'php/like.php';
            iconPath = liked ? "svg/heart_filled.svg" : "/svg/heart-svgrepo-com.svg";
            break;
        case 'plan':
            bodyData = `plan_id=${encodeURIComponent(id)}&liked=${encodeURIComponent(liked)}`;
            phpFile = 'php/pl_like.php';
            iconPath = liked ? "svg/heart_filled.svg" : "/svg/heart-svgrepo-com.svg";
            break;
        default:
            console.error('Invalid like type');
            return;
    }

    const icon = element.querySelector('img.heart-icon');
    icon.src = iconPath;

    fetch(phpFile, {
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
                console.error("Fehler beim Aktualisieren des Like-Status");
                console.log(data);
            }
        })
        .catch(error => console.error("Fehler beim Senden der Anfrage", error));
}

document.addEventListener("DOMContentLoaded", function() {
    // Zeige den Button nur, wenn der Benutzer nach unten scrollt
    window.onscroll = function() {
        var scrollToTopBtn = document.getElementById("scrollToTopBtn");

        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            scrollToTopBtn.style.display = "block"; // Zeige den Button an
        } else {
            scrollToTopBtn.style.display = "none"; // Verstecke den Button
        }
    };

    // Funktion zum Scrollen nach oben
    var scrollToTopBtn = document.getElementById("scrollToTopBtn");
    if (scrollToTopBtn) {
        scrollToTopBtn.onclick = function() {
            window.scrollTo({ top: 0, behavior: 'smooth' }); };
    }
});




