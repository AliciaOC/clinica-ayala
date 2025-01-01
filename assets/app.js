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
    actualizarRellenoFooter();

    $(window).resize(function() {
        actualizarRellenoFooter();
    });
    
    function actualizarRellenoFooter() {
        if ($(window).width() >= 1400) {  
            let alturaFooter = $('footer').outerHeight();  
            $('#relleno_footer').height(alturaFooter); 
            $('#relleno_footer').css('margin-top', alturaFooter * -1);
        }
    }

    //Mostrar el toast cuando es la primera vez que se accede a la página. Con la librería js-cookie
    if (!Cookies.get('visitada')) {
        $("body").attr("style", "overflow: hidden;");
        $("#toast_primera_vez button.btn-close").click(function() {
            $("body").attr("style", "overflow: auto;");
            $("#toast_primera_vez").hide();
        });

        // Crear la cookie para evitar que el toast se muestre nuevamente
        Cookies.set('visitada', 1);
    }

    //Controla las imágenes del carrusel de forma responsiva
    elegirImagen();

    function elegirImagen() {
        let width = $(window).width();

        $(window).resize(function() {
            setTimeout(elegirImagen, 100);
        });

        $('.imagen_diapositiva').each(function () {
            let img = $(this);
            //restauro los estilos que hayan podido quitarse en una redimensión anterior
            img.siblings('.carousel-caption').children('p').show();
            img.siblings('.carousel-caption').children('h5').css('margin-bottom', '1rem');

            if (width >= 1200) {
                img.attr('src', img.data('src-large'));
            } else if (width >= 768) {
                img.attr('src', img.data('src-medium'));
            } else {
                img.attr('src', img.data('src-small'));
                //Aplico cambios en los estilos del contenido del div hermano .carousel-caption
                img.siblings('.carousel-caption').children('p').hide();
                img.siblings('.carousel-caption').children('h5').css('margin-bottom', '0');
            }
        });
    }

    //Confirmación de una acción irreversible
    $('.enlace_accion').click(function (e) {
        e.preventDefault(); // Evita que el enlace se siga automáticamente
        const modal = $('#modalConfirm');
    
        // Muestra el modal
        modal.removeClass('hidden');
    
        // Maneja la confirmación
        $('#confirmBtn').click(function () {
            modal.addClass('hidden'); // Oculta el modal
            window.location.href = $(e.target).attr('href'); // Navega al enlace
        });
    
        // Maneja la cancelación
        $('#cancelBtn').click(function () {
            modal.addClass('hidden'); // Oculta el modal sin realizar la acción
        });
    });
});

