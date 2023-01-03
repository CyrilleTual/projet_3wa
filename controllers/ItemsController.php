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
        $data[2] = $idProduct; // indispensable pour creer un article si il n'y en a pas de préexistant 
        // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 

        new RendersController('admin/itemsDisplay', $data);
    }


    /*****************************************************************************************************
     * Affichage du formulaire de création d'un nouvel item (article)
     */

    public function createItem()
    {  
        // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // on recupère l'id du produit associé passé en GET depuis la page des produits pour verifier si il existe bien 
        // et recupère le nom du produit  

        ////////////////////////   attention securiser  $idProduct doit etre un int --

        $idProduct = $_GET["idProduct"];
        $model = new \Models\Products();
        $product = $model->findOneProduct($idProduct);

        // si retour = false : pas d'enregistrement trouvé 

        if ($product == FALSE){         
            new RendersController();
            exit;
        }

        // on récupère la table des TVA 
        $model = new \Models\Vat();
        $vatToDisplay = $model->getVatByQuery('status','actif'); // recup d'un tableau à afficher 

        $data[0] = $token;
        $data[1] = $vatToDisplay;
        $data[2] = $product;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('admin/itemsAdd', $data);

    }

    /*****************************************************************************************************
     * Préparation et affichage du formulaire de modification d'un item (article)
     * -> Même formulaire que pour la création 
     */

    public function modifyItem()
    {

        //  on recupère l'item à  modifier 
        $modelItem = new \Models\Items();
        $item = $modelItem->findOneItem($_GET["id"]);

        // on recupère le produit associé 
        $model = new \Models\Products();
        $product = $model->findOneProduct($item['id_product']);

        // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // on récupère la table des TVA 
        $model = new \Models\Vat();
        $vatToDisplay = $model->getVatByQuery('status', 'actif'); // recup d'un tableau à afficher 

        $data[0] = $token;
        $data[1] = $vatToDisplay;
        $data[2] = $product;
        $data[3] = $item;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('admin/itemsAdd', $data);
       
    }



    /*******************************************************************************
     * traitement du formulaire de création/ modification d'un item (article)
     */

     public function createItemProcess()
    {
       
        // initialisation des variables 
        
        $addItem = [
            'id_product' => '',
            'itemRef'       => '',
            'pack'      => '',
            'price'     => '',
            'stock'     => '',
            'id_vat'       => '',
            'status'    => '',
        ];

        // initialisation du tableau des erreurs 
        $errors = [];
        $errorsArray = new \Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();

        // vérification du remplissage du formulaire 
        if (
            array_key_exists('idProduct', $_POST)
            && array_key_exists('ref', $_POST)
            && array_key_exists('pack', $_POST)
            && array_key_exists('price', $_POST)
            && array_key_exists('stock', $_POST)
            && array_key_exists('vat', $_POST)
            && array_key_exists('status', $_POST)
        ) {


            $addItem = [
                'id_product'    => trim($_POST['idProduct']),
                'itemRef'       => trim($_POST['ref']),
                'pack'          => trim($_POST['pack']),
                'price'         => trim($_POST['price']),
                'stock'         => trim($_POST['stock']),
                'id_vat'        => trim($_POST['vat']),
                'status'        => trim($_POST['status'])
            ];

            // Mise en oeuvre des contrôles :
            //1) validité du token 
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            //2) validité de la référence
            if($addItem['itemRef'] == '')
                $errors[] = "Veuillez renseigner une référence SVP !";

            // si nouvel item  et pas d'erreur -> création de l'item
            if ((!isset($_POST['id'])) && (count($errors) == 0)){
                $model = new \Models\Items();
                $model->addNewItem($addItem);
                new RendersController('homePage');
                exit();           
            }

            // si il s'agit d'une mise à jour -> methode Update
            if ((isset($_POST['id'])) && (count($errors) == 0)) {
                $model = new \Models\Items();
                $model->updateItem(($_POST['id']),$addItem);
                new RendersController('homePage');
                exit();
            }

        }


        /********************************************************************
         *  si erreurs, régénération de la vue 
         */

        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $idProduct = ($addItem['id_product']);
        $modelProd = new \Models\Products();
        $product = $modelProd->findOneProduct($idProduct);

        if ($product == FALSE) {
            new RendersController();
            exit;
        }

        // on récupère la table des TVA 
        $model = new \Models\Vat();
        $vatToDisplay = $model->getVatByQuery('status', 'actif'); // recup d'un tableau à afficher 

        $data[0] = $token;
        $data[1] = $vatToDisplay;
        $data[2] = $product;

        // si il s' agit d'une MAJ on recupère en plus l'item concerné 
        if ((isset($_POST['id']))) {
            $modelItem = new \Models\Items();
            $item = $modelItem->findOneItem($_POST["id"]);
            $data[3] = $item;
        }

        // affichage de la vue d'affichage en passant les valeurs 
        new RendersController('admin/itemsAdd', $data, $errors);


        }

     













}
