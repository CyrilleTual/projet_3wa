<?php

namespace Controllers;

class ProductsController
{

    /*****************************************************************************************************
     * Affichage du formulaire des Items
     */
    public function displayFormProducts()
    {
        $data = [];
        $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $data[0] = $token;
        $data[1] = $valuesToDisplay;

        $model = new \Models\Items();

        $valuesToDisplay = $model->getItemsByQuery(); // recup d'un tableau à afficher 

        $data[0] = $token;
        $data[1] = $valuesToDisplay;
        // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 

        new RendersController('admin/formItems', $data);
    }
}
