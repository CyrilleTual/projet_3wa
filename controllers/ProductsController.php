<?php

namespace Controllers;

class ProductsController
{

    /*****************************************************************************************************
     * Affichage du formulaire de synthèse des produits 
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

        $model = new \Models\Products();

        //$valuesToDisplay = $model->findAllCat(); // recup d'un tableau à afficher 
        $valuesToDisplay = $model->getProductsByQuery(); // recup d'un tableau à afficher 

        $data[0] = $token;
        $data[1] = $valuesToDisplay;

        // var_dump($data);
        // die;
        // affichage de la vue d'affichage en passant $token et $ valuesToDisplay par le render sous $data 

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
     * VERIFICATION ET SOUMISSION DU FORMULAIRE DE CREATION DE COMPTE
     */

    public function addProduct()
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
            && array_key_exists('status', $_POST)
        ) {

            $addProduct = [
                'addCat'                => trim($_POST['category']),
                'addName'               => trim($_POST['productName']),
                'addRef'                => trim($_POST['reference']),
                'addTeaser'             => trim($_POST['teaser']),
                'addDescription'        => trim($_POST['description']),
                'addInfos'              => trim($_POST['infos']),
                'addPicture'            => 'silhouette.png',
                'addStatus'              => trim($_POST['status']),
            ];

            // verification des 4 champs obligatoires :  catégorie, nom, ref, et etat.

            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

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
                if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {



                    $dossier = "img_of_Products"; // Nom du dossier dans lequel on va mettre l'image uploadée.


                    $model = new \Models\Uploads(); // on se sert du model Uploads

                    // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok

                    $addProduct['addPicture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                }

                if (count($errors) == 0) {

                    // On créé notre tableau de datas à mettre dans la BDD tableau de type cle/valaveur
                    $datas = [
                        'id_category'       => $addProduct['addCat'],
                        'productName'       => $addProduct['addName'],
                        'productRef'        => $addProduct['addRef'],
                        'teaser'            => $addProduct['addTeaser'],
                        'description'       => $addProduct['addDescription'],
                        'infos'             => $addProduct['addInfos'],
                        'picture'           => $addProduct['addPicture'],
                        'status'            => $addProduct['addStatus']
                    ];

                    // On instancie notre model "Product"
                    $model = new \Models\Products();
                    // On appelle la méthode permettant l'INSERT INTO dans la BDD
                    $model->addNewProduct($datas);

                    // On affiche un ou plusieurs messages de validation.
                    $valids[] = 'Votre demande de création de compte a bien été enregistrée.';


                    var_dump('186 product controller');
                    die;


                    $model = new \Models\Results();
                    $token = $model->generateChainAleatoire(20);
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

                    $template = "formRegister.phtml";
                    include_once 'views/layout.phtml';
                }
            }
        }
        var_dump('stop ligne 210 ProductController');
        die;
        $model = new \Models\Results();
        $token = $model->generateChainAleatoire(20);
        $_SESSION['auth'] = $token;

        $template = "formRegister.phtml";
        include_once 'views/layout.phtml';
    }
}
