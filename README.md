# BlogApi
## Proyecto realizado en:
 - [Symfony 6.2](https://symfony.com/releases/6.2)
 - [PHP 8.1](https://www.php.net/releases/8.1/en.php)

## Tecnologías usadas:
 - [jms/serializer-bundle](https://github.com/schmittjoh/JMSSerializerBundle)
 - [friendsofsymfony/rest-bundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
 - [symfony/maker-bundle](https://github.com/symfony/maker-bundle)
 - [symfony/orm-pack](https://symfony.com/components/ORM%20Pack)
 - [lexik/jwt-authentication-bundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
 - [stof/doctrine-extensions-bundle](https://github.com/stof/StofDoctrineExtensionsBundle)
 - [symfony/http-client](https://github.com/symfony/http-client)
 - [symfony/test-pack](https://github.com/symfony/test-pack)
 - [nelmio/api-doc-bundle](https://github.com/nelmio/NelmioApiDocBundle)
 - [hautelook/alice-bundle](https://packagist.org/packages/hautelook/alice-bundle)
 - [symfony/flex](https://github.com/symfony/flex)
 - [symfony/twig-bundle](https://github.com/symfony/twig-bundle)
 - [friendsofphp/php-cs-fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)

Estas son algunas de las tecnologías usadas para desarrollar el proyecto.

## Descripción del proyecto
Este proyecto consta de una api REST securizada mediante un token JWT (en diversas zonas), que sirve post para ser consumidos por clientes. Esta api está diseñada para trabajar sola bajo el puerto 8080 de localhost (se detalla más adelante), tiene los endpoints que sirven json al cliente y consume los datos en json. Tiene tanto un registro como un login de usuarios, todo esto para garantizar que los usuarios que creen post o categorías estén registrados y seguros.

## Endpoints de la api
 - /api/blogposts_show(GET) -> Lista todos los post de BBDD en orden cronológico
 - /api/blogpost(POST) -> Crea un nuevo post en la BBDD (securizado)
 - /api/blogpost_show/{id}(GET) -> Permite ver un post mediante su id
 - /api/categories(GET) -> Permite ver las categorías disponibles (securizada)
 - /api/category(POST) -> Permite crear una categoría (securizada)
 - /api/login_check_api -> Checkea si el login mediante JWT
 - /api/register -> Registra un usuario
 - /api/doc -> Documentación de la api mediante Nelmio (en construcción)

## Preparando el proyecto
Hay que tener en cuenta que este proyecto va a llevar por detras una base de datos mysql. Lo primero que haremos será prepararla en las variables de entorno .env y del .env.test.local:
```sh
###> doctrine/doctrine-bundle ###
# Format described at https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
# IMPORTANT: You MUST configure your server version, either here or in config/packages/doctrine.yaml
#
# DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
DATABASE_URL="mysql://root:rootpassword@127.0.0.1:3306/blogApi?serverVersion=8&charset=utf8mb4"
# DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
###< doctrine/doctrine-bundle ###
```
Colocaremos el nombre de la BBDD, usuario y contraseña que deseemos. también tenemos que mirar que la variable de entorno esté en dev:
```sh
APP_ENV=dev
```
Tras esto podremos empezar a crear la BBDD tanto normal como la de test. Para ello lo primero que ahremos será crear la BBDD para dev con el siguiente comando:
```sh
php bin/console doctrine:database:create
```
Posteriormente haremos las migraciones:
```sh
php bin/console doctrine:migrations:migrate
```
Para concluir crearemos también las de test:
```sh
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```
Adicionalmente habría que cargar los datos para el modelo de test y así poder realizar los test unitarios correctamente. Para ello debemos lanzar el siguiente comando:
```sh
php bin/console hautelook:fixtures:load --env=test --no-bundles
```
También se puede realizar el mismo comando con --env=dev para tener datos primarios en la api.

## Test Unitarios
Todos los test unitarios se encuentran bajo la carpeta Test. Mediante el comando:
```sh
php bin/phpunit
```
Se puede comprobar que todo funciona correctamente (para ello se ha tenido que cargar los fixtures en el paso anterior).

## Api Doc (queda por hacer)
Se ha intentado hasta cierto punto documentar la api con Nelmio. Debido al tiempo no se ha podido solucionar una problemática de bearer token y otra problemática que está chocando con api platform. Aún así las rutas sin securizar como son la ver todos los post y la de ver un post por id funcionan correctamente.

## PHP-CS-FIXER
Se ha utilizado para corregir el código php utilizado en el proyecto. Se han colocado 2 scrips a nivel de composer para hacerlo más manejable:
 - composer sniff (compara el código php con la plantilla de fixer)
 ```sh
composer sniff
```
 - composer format (formatea el código acorde a la plantilla fixer)
  ```sh
composer format
```

## Arrancando el proyecto
Para arrancar el poryecto se ha probado con lo mínimo indispensable. Para ello se ha utilizado el propio symfony cli.
```sh
symfony server:start --port=8080
```
El puerto es muy importante debido a que el cliente proporcionado apunta a dicho puerto.

## Curiosidades del proyecto
 - Este proyecto no contiene ni plantillas ni formularios, esto es debido a que es una api de consumo.
 - El username del usuario registrado coincide con el de autor
 - La configuración de phpunit se toca desde su propio xml.
 - Todas las anotaciones de rutas están inline para que mientras se codifique se pueda hacer cualquier cambio.
 - No se ha hecho ninguna captura de excepciones por parte de la api debido a que debe ser el cliente quien lo haga.

## Mejoras
Las posibles mejoras pasan por los siguientes puntos:
 - Modo de edición de post filtrado por el autor (solo el mismo autor creador puede tocarlo)
 - Edición de usuarios (ahora mismo solamente los usuarios creados mediante fixtures tienen dirección)
 - Acabar la documentación Nelmio y solucionar el problema con api platform (choque de documentaciones)