function choose_plan(planId) {
    const activePlan = document.querySelector(".module-card .plus-icon img[src='../../../svg/check-circle.svg']");
    if (activePlan) {
        activePlan.src = "../../../svg/plus.svg";
        activePlan.alt = "plus";
        activePlan.title = "plus";
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/main_seite/choose_plan/pl_update_choose_plan.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = xhr.responseText.trim();
            var card = document.querySelector(`.module-card[data-plan-id='${planId}']`);
            var icon = card.querySelector(".plus-icon-img");
            if (response === "added") {
                icon.src = "../../../svg/check-circle.svg";
                icon.title = "check";
            } else if (response === "removed") {
                icon.src = "../../../svg/plus.svg";
                icon.title = "plus";
            } else {
                alert("Fehler: " + response);
            }
        }
    };
    xhr.send("plan_id=" + planId);
}

document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll('input[type="radio"]');
    buttons.forEach(button => {
        if (button.checked) {
            const label = document.querySelector(`label[for="${button.id}"]`);
            label.classList.add('active');
        }
    });
});
