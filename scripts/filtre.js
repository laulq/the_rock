init();

function init() {
    const boutonsFiltres = document.querySelectorAll('.dropdown-btn');

    for (let unBouton of boutonsFiltres) {
        unBouton.addEventListener('click', ouvrirDropdown);
    }

    document.addEventListener('click', fermerTousDropdowns);
}

function ouvrirDropdown(evt) {
    const dropdown = evt.currentTarget.closest('.dropdown');

    // Ferme les autres dropdowns ouverts
    const dropdownsOuverts = document.querySelectorAll('.dropdown.open');
    for (let unDropdown of dropdownsOuverts) {
        if (unDropdown !== dropdown) d.classList.remove('open');
    }

    dropdown.classList.toggle('open');
    evt.stopPropagation();
}

function fermerTousDropdowns() {
    const dropdownsOuverts = document.querySelectorAll('.dropdown.open');
    for (let unDropdown of dropdownsOuverts) {
        unDropdown.classList.remove('open');
    }
}