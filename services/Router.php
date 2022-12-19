<?php

namespace Services;

use Controllers\RendersController;

class Router
{
    private ?string $route;
    private ?string $action;

    public function __construct()
    {
        $this->route = isset($_GET['route']) ? $_GET['route'] : null;
        $this->action = isset($_GET['action']) ? $_GET['action'] : null;
        $this->router();
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    private function router()
    {
        if (!empty($this->route) && $this->route !== null) {
            $strReplaceUp = ucfirst($this->route); // passe en majuscule la premiere lettre 

            // var_dump($strReplaceUp);
            // die;

            $nomfichier = str_replace("\\", "/", "controllers\\$strReplaceUp" . 'Controller.php'); // pour en vérifier l'existence

            if (file_exists($nomfichier)) {


                $controller = "controllers\\$strReplaceUp" . "Controller";



                $classFinal = new $controller();

                if (!empty($this->getAction()) && $this->getAction() !== null) {

                    $_action = $this->getAction();
                    if (\method_exists($classFinal, $this->getAction())) $classFinal->$_action();
                    else new RendersController();
                }
            } else {
                new RendersController();
            }
        } else {
            /**
             * si il n'y a pas de ($_GET['route']) valide de passé on fait appel au Render, s 
             * c'est lui qui géréra aussi les renvoi vers la homePage 
             */

            new RendersController();  /// si pas de ($_GET['route'])  
        }
    }
}
