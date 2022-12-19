
<?php
session_start();

/**
 * lancement de l'autoload
 */
require_once 'autoload.php';
App\Autoload::register();

/***
 * appel au router (qui basculera sur le render si pas d'action demandées)
 */

$router = new App\Services\Router;
