<?php

declare(strict_types=1);

namespace Controllers;

class DisplaysController
{
    public function displayOneProduct(){
        $idCat = $_GET ['cat'];
        var_dump('id de la catégorie : ', $idCat);
        die;
    }
}
