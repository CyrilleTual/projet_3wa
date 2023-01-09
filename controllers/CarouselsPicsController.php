<?php

namespace Controllers;

class CarouselsPicsController
{

    /*****************************************************************************************************
     * Affichage du formulaire d'ajout
     */

    public function displayFormAddPic()
    {   
     
        // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        $data['token'] = $token;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('admin/carouselAdd', $data);

    }


    /*******************************************************************************************************
     * récupération des photos diponibles (staut actif) 
     *  */  
    public function getPicsAvailable()
    {
        $model = new \Models\CarouselPics();
        return $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives
    }



    /*********************************************************************************************
     * Création ou Modification d'un produit traitement du formulaire  - admin
     */

    public function AddOrModifyCarouselPic()
    {

        $errors = []; // tableau des erreurs 

        $addPic = [
            'addDescription'    => '',
            'addPicture'        => '',
            'addStatus'         => ''
        ];

    //     var_dump($_POST);
    //    var_dump($_FILES);
    //     die;

        if (
            array_key_exists('description', $_POST)
            && array_key_exists('status', $_POST)
        ) {

            $addPic = [
                'addDescription'        => trim(($_POST['description'])),
                'addPicture'            => 'default.png',
                'addStatus'             => trim(($_POST['status'])),
            ];


            // verification de la validité du token 

            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            if (isset($_FILES['picture']) && $_FILES['picture']['name'] == '' )
                $errors[] = "Erreur avec le fichier image "; 

            // verification   description et etat.    

            if ($addPic['addDescription'] == '')
                $errors[] = "Merci de renseigner une description";

            if ($addPic['addStatus'] == '')
                $errors[] = "Erreur dans le formulaire";


            if (count($errors) == 0) {

                // On instancie "Products"
                $model = new \Models\CarouselPics();

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
                if ((!isset($_POST['id'])) && isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {
                    //var_dump(' creation');
                    $dossier = "slider"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                    $model = new \Models\Uploads(); // on se sert du model Uploads

                    // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                    // ET qui met à jour le tableau d'erreur (passage par reference de $errors)
                    $addPic['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
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

                        if (count($errors) == 0 && ($_POST['photo_recup'] !== 'default.png')) {
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



                    // On créé notre tableau à mettre dans la BDD tableau de type cle/valaveur
                    $datasCarouselPic = [
                        'description'       => $addPic['addDescription'],
                        'picture'           => $addPic['addPicture'],
                        'status'            => $addPic['addStatus']
                    ];

                    // si il existe $_POST['id'] on est dans le cas de la mofif et non de la création

                    // if (isset($_POST['id'])) {
                    //     $id_product = trim(htmlspecialchars($_POST['id']));
                    //     // On appelle la méthode permettant l'INSERT INTO dans la BDD
                    //     $model = new \Models\Products();
                    //     $model->UpdateProduct($id_product, $datasProd);
                    // }

                    // ici on est dans le cas d'une création 
                    if (!isset($_POST['id'])) {
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\CarouselPics();
                        $model->addNewPic($datasCarouselPic);
                        // On affiche un ou plusieurs messages de validation.
                        $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                    }
                    $_SESSION['message'] = "insertion ok ";
                    new RendersController('homePage');
                    exit();
                }
            }
        }



        /*** --------------------------------------------------------------------------------------------
         * on est ici dans le cas où il y a des erreurs - on va réafficher le formulaire adéquat
         * 
         *  */
        // $data = [];
        // $catAvailable = [];
        // $model = new \Models\Tools();
        // $token = $model->randomChain(20);
        // $_SESSION['auth'] = $token;
        // $modelCat = new \Models\Categories();
        // $catAvailable = $modelCat->getCategoriesByQuery('status', 'actif');
        // $data[0] = $token;
        // $data[1] = $catAvailable;


        // // cas d'un nouveau produit --------------------------------------------------------------------
        // if (!isset($_POST['id'])) {
        //     new RendersController('admin/productsAdd', $data, $errors);
        // }
        // // cas d'une modification ------------------------------------------------------------------
        // if (isset($_POST['id'])) {
        //     //  on recupère le produit à modifier 
        //     $modelProd = new \Models\Products();
        //     $productToModify = $modelProd->findOneProduct($_POST["id"]);
        //     // recuperation de toutes les  categories (pour affichage de la catégorie actuelle)
        //     $model = new \Models\Categories();
        //     $catList = $model->getCategoriesByQuery();
        //     // nom de la catégorie actuelle  // besion de la liste de toutes les categories
        //     foreach ($catList as $key => $valeur) {
        //         if (($productToModify["id_category"]) === ($valeur["id_category"])) {
        //             $currentCat = ($valeur["categoryName"]);
        //         }
        //     }
        //     $data[0] = $token;
        //     $data[1] = $catAvailable;
        //     $data[2] = $productToModify;
        //     $data[3] = $currentCat;
        //     // affichage de la vue de la vue de modification de produit 
        //     new RendersController('admin/productsModify', $data, $errors);
        //}
    }



    /*****************************************************************************
     * sortie du tableau des pics pour le caroussel (requete Ajax) 
     */
    public function ajaxPics()
    {
        //$content = file_get_contents("php://input");
        //$data = json_decode($content, true);
        //$idToSearch = $data['idToFind'];

        $model = new \Models\CarouselPics();
        $picsToDisplay = $model->getPicsByQuery('status', 'actif'); // recup d'un tableau à afficher des photos actives

       
        include 'public/views/test.phtml';
    } 















}