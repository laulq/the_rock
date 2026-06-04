<section id="lecteur" class="lecteur">
    <div id="lecteur-wrapper" class="lecteur-wrapper">
        <div id="lecteur-controles" class="lecteur-controles">

            <figure id="lecteur-infos" class="lecteur-infos">
                <!-- Radio cliquable ? Pour accéder à ses infos, à une liste... Si oui, je change pour mettre + qu'une image -->
                <img src="placeholder_radio_actuelle" alt="pochette radio actuelle">
                <figcaption>
                    <p class="lecteur-nom-radio">Placeholder nom radio</p>
                    <p class="lecteur-nom-musique">Placeholder musique</p>
                </figcaption>
            </figure>

            <div id="lecteur-controles-bouton" class="lecteur-controles-bouton">
                <a id="bouton-play" class="bouton-play" href="#">
                    <img src="placeholder_bouton" alt="play/pause">
                </a>
            </div>

            <!-- Qu'est-ce qui va prendre la place de la barre de lecture ? -->

            <div id="lecteur-controles-volume" class="lecteur-controles-volume">
                <a id="mute" class="lecteur-mute" href="#">
                    <img src="placeholder_volume" alt="volume">
                </a>
                <div id="lecteur-volume" class="lecteur-volume">
                    <label for="volume" class="sr-only">Volume</label>
                    <input type="range" id="volume" class="lecteur-volume-range" min="0" max="100" step="1">
                </div>
            </div>

        </div>
    </div>
</section>