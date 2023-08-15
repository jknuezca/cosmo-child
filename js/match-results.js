function openMatchDate(evt, date) {
    hideAllElementsByClassName("tabcontent");
    deactivateAllElementsByClassName("tablink");
    
    displayElementById(date);
    activateCurrentElement(evt);
}

function hideAllElementsByClassName(className) {
    const elements = document.getElementsByClassName(className);
    for (const element of elements) {
        element.style.display = "none";
    }
}

function deactivateAllElementsByClassName(className) {
    const elements = document.getElementsByClassName(className);
    for (const element of elements) {
        element.classList.remove("active");
    }
}

function displayElementById(id) {
    document.getElementById(id).style.display = "block";
}

function activateCurrentElement(evt) {
    evt.currentTarget.classList.add("active");
}

document.addEventListener('DOMContentLoaded', () => {
    const tableBodies = document.querySelectorAll('.match-results-table tbody');
    
    tableBodies.forEach(tbody => {
        tbody.addEventListener('click', function(event) {
            const row = event.target.closest('tr');
            if (row) {
                const permalink = row.getAttribute('data-permalink');
                if (permalink) {
                    window.location.href = permalink;
                }
            }
        });
    });
});