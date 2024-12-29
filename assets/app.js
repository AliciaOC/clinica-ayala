import $ from "jquery";
import Cookies from "js-cookie";
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

    //función para mostrar el toast cuando es la primera vez que se accede a la página. Con la librería js-cookie
    if (!Cookies.get('visitada')) {
        $("body").attr("style", "overflow: hidden;");
        $("#toast_primera_vez button.btn-close").click(function() {
            $("body").attr("style", "overflow: auto;");
            $("#toast_primera_vez").hide();
        });

        // Crear la cookie para evitar que el toast se muestre nuevamente
        Cookies.set('visitada', 1);
    }
});

