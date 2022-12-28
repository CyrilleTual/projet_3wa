<?php

namespace Controllers;

class ItemsController
{

    /*****************************************************************************************************
     * Affichage du formulaire des Items
     */
    public function displayItems()
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

       // $idProduct=$_POST['idProduct'];
        $idProduct = $_GET["id"];

    
        $valuesToDisplay = $model->getItemsByQuery("items . id_product",$idProduct); // recup d'un tableau à afficher 


        $data[0] = $token;
        $data[1] = $valuesToDisplay;
        // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 

        new RendersController('admin/itemsDisplay', $data);
    }
}
