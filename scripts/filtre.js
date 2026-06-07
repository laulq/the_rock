document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const dropdown = btn.closest('.dropdown');

        // Ferme les autres dropdowns ouverts
        document.querySelectorAll('.dropdown.open').forEach(d => {
            if (d !== dropdown) d.classList.remove('open');
        });

        dropdown.classList.toggle('open');
        e.stopPropagation();
    });
});

// Clic ailleurs → ferme tout
document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown.open')
        .forEach(d => d.classList.remove('open'));
});