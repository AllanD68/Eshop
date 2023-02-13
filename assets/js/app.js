/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';



// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// npx encore dev-server --hot


// <div id="slider"></div>

// on selectionne le slider
const slider = document.getElementById('price-slider');

// On appelle la bibliothèque du slider
import noUiSlider from 'nouislider'
// On appelle le css du slider
import 'nouislider/distribute/nouislider.css'
//On import notre fichier Filter
import Filter from './modules/Filter'

//On appelle l'élement qu'on souhaite mettre en ajax
new Filter(document.querySelector('.js-filter'));

// on appelle le js seulement si le slider est présent 
if (slider) {

    //On créé le slider
    //On selectionne le champ min et max
    const min = document.getElementById('min')
    const max = document.getElementById('max')
        // permet de tombé seulement sur des valeurs termiant par 5 ou 0
        //On arrondi à la virgule inferieur 
    const minValue = Math.floor(parseFloat(slider.dataset.min, 5) / 10) * 10
        //On arrondi à la virgule superieur 
    const maxValue = Math.ceil(parseFloat(slider.dataset.max, 5) / 10) * 10
    const range = noUiSlider.create(slider, {
        // On définit les valeurs de depart et d'arrivé ( ou les valeurs min et max en foction de la bdd ) donc les valeurs directement dans les champs
        start: [min.value || minValue, max.value || maxValue],
        connect: true,
        // les valeur du slider change de 5 par 5
        step: 5,
        // On recupère les valeurs min et max du twig
        range: {
            'min': minValue,
            'max': maxValue
        }
    })

    //  recupére les differentes valeurs et le numero du curseur qu'on a bougé 
    range.on('slide', function(values, handle) {
        // si on bouge le cursor  min (0)
        if (handle === 0) {
            // on recupère une valeur sous form d'entier
            min.value = Math.round(values[0])
        } // si on bouge le cursor  max (0)
        if (handle === 1) {
            max.value = Math.round(values[1])
        }
    })

    //Quand l'action avec le curseur est terminé 
    range.on('end', function(values, handle) {
        //On emet sur le champ min un evenement de changement
        min.dispatchEvent(new Event('change'))

    })

}