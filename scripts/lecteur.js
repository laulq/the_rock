// Garde en mémoire le volume dans une constante extérieure au lecteur
const audio = document.getElementById('lecteur-audio'); 

init();

function init() {
    const boutonPlay  = document.getElementById('bouton-play');
    const volumeRange = document.getElementById('volume');
    const boutonMute  = document.getElementById('mute');

    audio.volume = volumeRange.value / 100;

    boutonPlay.addEventListener('click', toggleAudio);
    volumeRange.addEventListener('input', changerVolume);
    boutonMute.addEventListener('click', toggleMute);

    // Reprend la radio en cours si elle existait
    const radioSauvegardee = localStorage.getItem('radio_url');
    const nomSauvegarde    = localStorage.getItem('radio_nom');
    const imageSauvegardee = localStorage.getItem('radio_image');

    if (radioSauvegardee) {
        audio.src = radioSauvegardee;
        document.getElementById('lecteur-nom-radio').textContent = nomSauvegarde ?? '';
        document.getElementById('lecteur-image').src             = imageSauvegardee ?? '';

        // Relance automatiquement car l'utilisateur a déjà interagi
        audio.play().catch(function(erreur) {
            console.warn('Autoplay bloqué, cliquez play :', erreur);
        });
    }
}

// Pause ou relance la radio
function toggleAudio(evt) {
    evt.preventDefault(); // Comme les <a> sont des href="#", empêche la page de scroll tout en haut

    if (audio.paused) {
        audio.play();
    } else {
        audio.pause();
    }
}

// Change le volume selon le slider
function changerVolume() {
    const volumeRange = document.getElementById('volume'); // Récupère la range du slider sous forme d'int entre 0 et 100

    audio.volume = volumeRange.value/100;
    audio.muted = false; // Active le son (s'il a déjà été désactivé par toggleMute)
}

// Active ou désactive le son
function toggleMute(evt) {
    evt.preventDefault(); // Comme les <a> sont des href="#", empêche la page de scroll tout en haut

    const volumeRange = document.getElementById('volume');

    audio.muted = !audio.muted;
    volumeRange.value = audio.muted?0 : audio.volume*100; // Revient au volume gardé en mémoire avant le mute
}

// Lance une radio récupérée depuis le site
function lancerRadio(url, nom, image) {
    audio.src = url;
    audio.load();

    audio.play().catch(function(erreur) {
        console.error('Lecture bloquée :', erreur);
    });

    document.getElementById('lecteur-nom-radio').textContent = nom;
    document.getElementById('lecteur-image').src = image;

    // Sauvegarde pour persistance entre pages
    localStorage.setItem('radio_url', url);
    localStorage.setItem('radio_nom', nom);
    localStorage.setItem('radio_image', image);
}