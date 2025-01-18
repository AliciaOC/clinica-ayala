# clinica-ayala
Proyecto final de la FP DAW. Es una aplicación para clínicas

Pasos para desplegar en local:
1. Adaptar el archivo env.example y dejarlo en .env. 
2. Tener composer en el equipo
3. Ejecutar composer install
4. php bin/console doctrine:database:create
5. php bin/console doctrine:migrations:migrate
6. php bin/console doctrine:fixtures:load

*Requiere al menos PHP 8.2.*
*Es muy recomendable utilizar Symfony CLI en lugar de Apache en local, (especialmente si usas windows), por cuestiones de velocidad*

Enlace a producción: https://aliciaortegacastro.es/