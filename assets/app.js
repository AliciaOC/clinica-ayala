import $ from "jquery";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";

window.$ = $;
window.jQuery = $;

/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

$(document).ready(function() {
    // Función para actualizar la altura del relleno del footer cuando el ancho de la ventana es >= 1400px
    function actualizarRellenoFooter() {
        if ($(window).width() >= 1400) {  
            let alturaFooter = $('footer').outerHeight();  
            $('#relleno_footer').height(alturaFooter); 
            $('#relleno_footer').css('margin-top', alturaFooter * -1);
        }
    }

    actualizarRellenoFooter();

    $(window).resize(function() {
        actualizarRellenoFooter();
    });
});

