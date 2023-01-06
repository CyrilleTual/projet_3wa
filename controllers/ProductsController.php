<?php

namespace Controllers;

class ProductsController
{
    /*******************************************************************************************************
     * récupération des produits diponibles (actifs)
     *  */  
    public function getProductsAvailable()
    {
        $model = new \Models\Products();
        return $model->getProductsByQuery('status', 'actif'); // recup d'un tableau à afficher des produits actives
    }

    /*****************************************************************************************************
     * Affichage du formulaire de synthèse des produits actis ou non 
     */
    public function displayFormProducts()
    {
        $data = [];
        $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
   
        $model = new \Models\Products();
        $valuesToDisplay = $model->getProductsByQuery(); // recup d'un tableau à afficher 
        $data[0] = $token;
        $data[1] = $valuesToDisplay;

        new RendersController('admin/productsDisplay', $data);
    }

    /*****************************************************************************************************
     * Affichage du formulaire de création d'un nouveau produit 
     * -> necessite la liste des catégories  $cat[]
     */
    public function displayFormAddProducts()
    {
        $data = [];

        $catAvailable = [];

        // pour recevoir les données à afficher sous forme d'un array .
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // recupération des catégories disponibles 

        $model = new \Models\Categories();
        $catAvailable = $model->getCategoriesByQuery('status', 'actif'); // recup d'un tableau à afficher des cats actives

        $data[0] = $token;
        $data[1] = $catAvailable;

        // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 
        new RendersController('admin/productsAdd', $data);
    }

    /*****************************************************************************************************
     * VERIFICATION ET SOUMISSION DU FORMULAIRE DE  ajout de produit
     */

    //public function addProduct()
    // {

    //     $errors = []; // tableau des erreurs 

    //     $addProduct = [
    //         'addCat'            => '',
    //         'addName'           => '',
    //         'addRef'            => '',
    //         'addTeaser'         => '',
    //         'addDescription'    => '',
    //         'addInfos'          => '',
    //         'addPicture'        => '',
    //         'addStatus'         => ''
    //     ];

    //     if (
    //         array_key_exists('category', $_POST)
    //         && array_key_exists('productName', $_POST)
    //         && array_key_exists('reference', $_POST)
    //         && array_key_exists('teaser', $_POST)
    //         && array_key_exists('description', $_POST)
    //         && array_key_exists('infos', $_POST)
    //         && array_key_exists('status', $_POST)
    //     ) {

    //         $addProduct = [
    //             'addCat'                => trim($_POST['category']),
    //             'addName'               => trim($_POST['productName']),
    //             'addRef'                => trim($_POST['reference']),
    //             'addTeaser'             => trim($_POST['teaser']),
    //             'addDescription'        => trim($_POST['description']),
    //             'addInfos'              => trim($_POST['infos']),
    //             'addPicture'            => 'default.png',
    //             'addStatus'              => trim($_POST['status']),
    //         ];

    //         // verification des 4 champs obligatoires :  catégorie, nom, ref, et etat.

    //         if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
    //             $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

    //         if ($addProduct['addCat'] == '')
    //             $errors[] = "Un problème est survenu lors de l'envoi du formulaire";

    //         if ($addProduct['addName'] == '')
    //             $errors[] = "Veuillez renseigner un nom SVP!";

    //         if ($addProduct['addRef'] == '')
    //             $errors[] = "Veuillez renseigner une Référence SVP !";

    //         if ($addProduct['addStatus'] == '')
    //             $errors[] = "Un problème est survenu lors de l'envoi du formulaire";

    //         if (count($errors) == 0) {





    //             // On instancie "Products"
    //             $model = new \Models\Products();

    //             // Vérifier si l'utilisateur a chargé une image dans le formulaire
    //             // Si NON --> On garde "default.png"

    //             // Si OUI --> Vérifier sa taille ( inférieur à 2Mo )
    //             //        --> Vérifier l'extension ( si on veut une image, on doit recevoir une image )
    //             //        --> Vérifier le MIME ( si le contenu correspond à l'extension )
    //             // Si TOUT est bon --> On UPLOAD le ficher
    //             // Sinon,on N'UPLOAD PAS et on affiche le message d'erreur ( PAS DE INSERT INTO )
    //             if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {



    //                 $dossier = "img_of_products"; // Nom du dossier dans lequel on va mettre l'image uploadée.


    //                 $model = new \Models\Uploads(); // on se sert du model Uploads

    //                 // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok

    //                 $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
    //             }

    //             if (count($errors) == 0) {

    //                 // On créé notre tableau de datasProdProd à mettre dans la BDD tableau de type cle/valaveur
    //                 $datasProd = [
    //                     'id_category'       => $addProduct['addCat'],
    //                     'productName'       => $addProduct['addName'],
    //                     'productRef'        => $addProduct['addRef'],
    //                     'teaser'            => $addProduct['addTeaser'],
    //                     'description'       => $addProduct['addDescription'],
    //                     'infos'             => $addProduct['addInfos'],
    //                     'picture'           => $addProduct['addPicture'],
    //                     'status'            => $addProduct['addStatus']
    //                 ];

    //                 // On instancie notre model "Product"
    //                 $model = new \Models\Products();
    //                 // On appelle la méthode permettant l'INSERT INTO dans la BDD
    //                 $model->addNewProduct($datasProd);

    //                 // On affiche un ou plusieurs messages de validation.
    //                 $valids[] = 'Votre demande de création de compte a bien été enregistrée.';


    //                 // var_dump('178 product controller');
    //                 // die;


    //                 $model = new \Models\Tools();
    //                 $token = $model->randomChain(20);
    //                 $_SESSION['auth'] = $token;

    //                 unset($addUser);
    //                 // $addUser = [
    //                 //     'addNom'                => '',
    //                 //     'addPrenom'             => '',
    //                 //     'addEmail'              => '',
    //                 //     'addPassword'           => '',
    //                 //     'addPassword_confirme'  => '',
    //                 //     'addPicture'            => ''
    //                 // ];

    //                 new RendersController('homePage');
    //                 exit();
    //             }
    //         }
    //     }
    //     var_dump($errors);
    //     var_dump('stop ligne 210 ProductController');
    //     die;
    //     $model = new \Models\Tools();
    //     $token = $model->randomChain(20);
    //     $_SESSION['auth'] = $token;

    //     $template = "formRegister.phtml";
    //     include_once 'views/layout.phtml';
    // }

    /*********************************************************************************************
     * Modification d'un produit Affichage du formulaire  - admin
     */
    public function editProduct()
    {

        //  on recupère le produit à modifier 
        $model = new \Models\Products();

        //$productToModify=$model->findOneProduct($_POST["id"]);

        $productToModify = $model->findOneProduct($_GET["id"]);

        // recuperation de toutes les  categories (pour affichage de la catégorie actuelle)
        $model = new \Models\Categories();
        $catList = $model->getCategoriesByQuery();

        // recupération des catégories disponibles 
        $model = new \Models\Categories();
        $catAvailable = $model->getCategoriesByQuery('status', 'actif'); // recup d'un tableau à afficher des cats actives

        // mise en place d'un token
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // nom de la catégorie actuelle  // besion de la liste de toutes les categories

        foreach ($catList as $key => $valeur) {
            if (($productToModify["id_category"]) === ($valeur["id_category"])) {
                $currentCat = ($valeur["categoryName"]);
            }
        }

        $data[0] = $token;
        $data[1] = $catAvailable;
        $data[2] = $productToModify;
        $data[3] = $currentCat;

        // affichage de la vue de la vue de modification de produit 
        new RendersController('admin/productsModify', $data);
    }



    /*********************************************************************************************
     * Création ou Modification d'un produit traitement du formulaire  - admin
     */

    public function AddOrModifyProductProcess()
    { 

        $errors = []; // tableau des erreurs 

        $addProduct = [

            'addCat'            => '',
            'addName'           => '',
            'addRef'            => '',
            'addTeaser'         => '',
            'addDescription'    => '',
            'addInfos'          => '',
            'addPicture'        => '',
            'addStatus'         => ''
        ];

        if (
            array_key_exists('category', $_POST)
            && array_key_exists('productName', $_POST)
            && array_key_exists('reference', $_POST)
            && array_key_exists('teaser', $_POST)
            && array_key_exists('description', $_POST)
            && array_key_exists('infos', $_POST)
            && array_key_exists('status', $_POST)) 
            {

                $addProduct = [

                    'addCat'                => trim(($_POST['category'])),
                    'addName'               => trim(($_POST['productName'])),
                    'addRef'                => trim(($_POST['reference'])),
                    'addTeaser'             => trim(($_POST['teaser'])),
                    'addDescription'        => trim(($_POST['description'])),
                    'addInfos'              => trim(($_POST['infos'])),
                    'addPicture'            => 'default.png',
                    'addStatus'             => trim(($_POST['status'])),
                ];


                // verification de la validité du token 

                if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                    $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

                    // verification des 4 champs obligatoires :  catégorie, nom, ref, et etat.    

                if ($addProduct['addCat'] == '')
                    $errors[] = "Un problème est survenu lors de l'envoi du formulaire";

                if ($addProduct['addName'] == '')
                    $errors[] = "Veuillez renseigner un nom SVP!";

                if ($addProduct['addRef'] == '')
                    $errors[] = "Veuillez renseigner une Référence SVP !";

                if ($addProduct['addStatus'] == '')
                    $errors[] = "Un problème est survenu lors de l'envoi du formulaire";


                if (count($errors) == 0) {

                    // On instancie "Products"
                    $model = new \Models\Products();

                    // Vérifier si l'utilisateur a chargé une image dans le formulaire
                    // Si NON --> On garde "default.png"

                    // Si OUI --> Vérifier sa taille ( inférieur à 2Mo )
                    //        --> Vérifier l'extension ( si on veut une image, on doit recevoir une image )
                    //        --> Vérifier le MIME ( si le contenu correspond à l'extension )
                    // Si TOUT est bon --> On UPLOAD le ficher
                    // Sinon,on N'UPLOAD PAS et on affiche le message d'erreur ( PAS DE INSERT INTO )
                

                    /**************************************************
                     * Traitement de l'image - cas d'une création 
                     */
                    if ((!isset($_POST['id'])) && isset($_FILES['picture']) && $_FILES['picture']   ['name'] !== '') {

                        //var_dump(' creation');

                
                        $dossier = "img_of_products"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                        $model = new \Models\Uploads(); // on se sert du model Uploads

                        // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                        // ET qui met à jour le tableau d'erreur (passage par reference de $errors)
                        $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                    }

                    /*******************************************************
                     * Traitement de l'image - cas d'une mofification 
                     */
                    if (isset($_POST['id'])) {

                        //var_dump(' Modification');

                        // avec un nouveau fichier image //
                        if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {

                            // var_dump(' Avec Nouveau fichier image');

                           
                            
                            // on traite la nouvelle image 
                            $dossier = "img_of_products"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                            $model = new \Models\Uploads(); // on se sert du model Uploads
                         // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                       
                            $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                       

                            // si il n' y a pas d'erreur dans le nouveau fichier image on efface l'ancienne.
                            // SAUF s'il s'agit de la photo par defaut 

                            if (count($errors) == 0 && ($_POST['photo_recup']!=='default.png')) {
                                $toErase = "public/uploads/" . (($_POST['photo_recup']));
                            
                                if (file_exists($toErase)) {
                                    unlink($toErase);
                                } 
                            } elseif (count($errors) !== 0) {  // sinon on garde l'ancienne
                            
                            $addProduct['addPicture'] = ($_POST['photo_recup']);
                          
                            }

                        } else {  // sans nouveau fichier image, on garde l'ancienne photo

                        // var_dump(' Sans Nouveau fichier image');
                        
                            $addProduct['addPicture'] = ($_POST['photo_recup']);
                            // var_dump($addProduct['addPicture']);
                        }
                    }
                    

                    if (count($errors) == 0) {

                    

                        // On créé notre tableau de datasProd à mettre dans la BDD tableau de type cle/valaveur
                        $datasProd = [
                            'id_category'       => $addProduct['addCat'],
                            'productName'       => $addProduct['addName'],
                            'productRef'        => $addProduct['addRef'],
                            'teaser'            => $addProduct['addTeaser'],
                            'description'       => $addProduct['addDescription'],
                            'infos'             => $addProduct['addInfos'],
                            'picture'           => $addProduct['addPicture'],
                            'status'            => $addProduct['addStatus']
                        ];

                        // si il existe $_POST['id'] on est dans le cas de la mofif et non de la création
                        
                        if (isset($_POST['id'])){
                            $id_product = trim(htmlspecialchars($_POST['id']));
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\Products();
                        $model->UpdateProduct( $id_product, $datasProd);
                        }

                        // ici on est dans le cas d'une création 
                        if (!isset($_POST['id'])) {
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\Products();
                        $model->addNewProduct($datasProd);
                        // On affiche un ou plusieurs messages de validation.
                        $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                    
                        }

                        
                        $model = new \Models\Tools();
                        $token = $model->randomChain(20);
                        $_SESSION['auth'] = $token;

                        unset($addUser);
                        // $addUser = [
                        //     'addNom'                => '',
                        //     'addPrenom'             => '',
                        //     'addEmail'              => '',
                        //     'addPassword'           => '',
                        //     'addPassword_confirme'  => '',
                        //     'addPicture'            => ''
                        // ];

                        $_SESSION['message']="insertion ok ";

                        new RendersController('homePage');
                        exit();
                    }
                }
            }


            
        /*** --------------------------------------------------------------------------------------------
     * on est ici dans le cas où il y a des erreurs - on va réafficher le formulaire adéquat
     * 
     *  */
        $data = [];
        $catAvailable = [];
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        $modelCat = new \Models\Categories();
        $catAvailable = $modelCat->getCategoriesByQuery('status', 'actif');
        $data[0] = $token;
        $data[1] = $catAvailable;
    
     
     // cas d'un nouveau produit --------------------------------------------------------------------
     if (!isset($_POST['id'])){
        new RendersController('admin/productsAdd', $data, $errors);
     }
     // cas d'une modification ------------------------------------------------------------------
     if (isset($_POST['id'])){
            //  on recupère le produit à modifier 
            $modelProd = new \Models\Products();
            $productToModify = $modelProd->findOneProduct($_POST["id"]);
            // recuperation de toutes les  categories (pour affichage de la catégorie actuelle)
            $model = new \Models\Categories();
            $catList = $model->getCategoriesByQuery();
            // nom de la catégorie actuelle  // besion de la liste de toutes les categories
            foreach ($catList as $key => $valeur) {
                if (($productToModify["id_category"]) === ($valeur["id_category"])) {
                    $currentCat = ($valeur["categoryName"]);
                }
            }
            $data[0] = $token;
            $data[1] = $catAvailable;
            $data[2] = $productToModify;
            $data[3] = $currentCat;
            // affichage de la vue de la vue de modification de produit 
            new RendersController('admin/productsModify', $data, $errors);
     }
    }

    /*************************************************************************************************************
     * Affichage des produits - public
     */

    public function displayProductsOfOneCategory()
    {
        // verification de l'existance d'une cat en GET
        if (!array_key_exists('cat', $_GET) or !is_numeric($_GET['cat']) or (($_GET['cat'])<1))  {
            new RendersController('homePage');
            exit;
        }

        $idCat = trim($_GET['cat']);

        // verifie si la catégorie existe et si oui on recupère directement le nom de la catégorie.
        $modelCat = new \Models\Categories();
        $categorie = ($modelCat->getOneCategoriesByQuery('id_category', $idCat));  

        if (!empty($categorie)){

            // mise en place d'un token pour sécuriser la soumission du formulaire (future fonctionnilté commande)
            $model = new \Models\Tools();
            $token = $model->randomChain(20);
            $_SESSION['auth'] = $token;

            // tableau regroupant les datas envoyées à la vue via le render
            $data = [];

            // on selectionne l'ensemble des produits actif de la bonne catégorie
            $ProductsToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
            $model = new \Models\Products();
            $ProductsToDisplay = $model->getProductsPublic('products.status', 'products.id_category', 'actif', $idCat); // recup d'un tableau à afficher

            $newProductsToDisplay = [];
            foreach ($ProductsToDisplay as $key => $value) {
                //id des produits actifs
                $idProd = $value['id_product'];
                //on va chercher les items disponibles ( actifs ) pour un produit par 
                $modelitem  = new \Models\items();
                $itemsDispo = $modelitem->getItemsPublic('items.status', 'items.id_product', 'actif', $idProd);
                // on raccroche le tableau des items dispo au produit en ajoutant la clé 'items'
                $value['items'] = $itemsDispo;
                $newProductsToDisplay[] = $value;
            }
            $data[0] = $token;
            $data[1] = $newProductsToDisplay;
            $data[2] = $categorie;

            new RendersController('displayProductsByCat', $data);
        } else {
            new RendersController('homePage'); // categorie sans produit actif -> pas 
        }



    }





}

