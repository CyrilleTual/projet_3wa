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
            $strReplaceUp = ucfirst($this->route); // passe en majuscule la premiere lettre pour reconstituer le controller appelé

            $nomfichier = str_replace("\\", "/", "controllers\\$strReplaceUp" . 'Controller.php'); // pour en vérifier l'existence

            if (file_exists($nomfichier)) {  // le controlleur existe 

                $controller = "controllers\\$strReplaceUp" . "Controller";

                $classFinal = new $controller();

                if (!empty($this->getAction()) && $this->getAction() !== null) {  // si une action est définie 

                    $_action = $this->getAction();
                    if (\method_exists($classFinal, $this->getAction())) $classFinal->$_action(); // on cheche la methode appellée
                    else new RendersController('homePage'); // methode definie n'existe pas

                }else{
                    new RendersController('homePage'); // pas de methode définie 
                }
            } else {
                new RendersController('homePage'); // pas de controlleur valide
            }
        } else {
            new RendersController('homePage');  /// si pas de ($_GET['route'])  
        }
    }
}
