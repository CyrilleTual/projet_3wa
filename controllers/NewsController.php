<?php

namespace Controllers;

class NewsController
{
    /********************************************************
     * Methode de vérification de la validité d'id passé en GET 
     */
    public function idNewsByGetIsOK()
    {
        // verification de l'existance d'un id en GET , que c'est un numeric et > 0 
        if (!array_key_exists('id', $_GET) or !is_numeric($_GET['id']) or (($_GET['id']) < 1)) {
            new RendersController('page404');
            exit;
        }
        $id = trim($_GET['id']);
        // verifie si la photo existe dans la DB et on la recupère 
        $model = new \Models\News();
        $product = ($model->findOneNews($id));

        if (!empty($product)) {
            return ($product);
        } else {
            new RendersController('page404'); // pas de produit matchant avec l'id reçu en GET
            exit;
        }
    }



    /**********************************************************
     * Affichage du formulaire de gestion des news - admin
     */
    public function displayFormNews()
    {
        $data = [];
        $valuesToDisplay = []; // pour recevoir les données à afficher sous forme d'un array .
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $model = new \Models\News();
        $valuesToDisplay = $model->getNewsByQuery(); // recup d'un tableau à afficher 
        $data["token"] = $token;
        $data["values"] = $valuesToDisplay;

        new RendersController('admin/newsDisplay', $data);
    }

    /*********************************************************
     * Affichage du formulaire d'ajout des news - admin
     */

    public function displayFormAddNews()
    {
        // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        $data['token'] = $token;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('admin/newsAddOrModify', $data);
    }

    /*************************************************************
     * Modification d'une news -  Affichage du formulaire  - admin
     */
    public function modifyNews()
    {
        // on verifie la validité de l'id passé en get et si c'est ok on recupère la photo 
        $ToModify = self::idNewsByGetIsOK();

        // mise en place d'un token
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        $data['token'] = $token;
        $data['new'] = $ToModify;

        // affichage de la vue de la vue de modification de la photo
        new RendersController('admin/newsAddOrModify', $data);
    }

    /**********************************************************************
     * Création ou Modification News - traitement du formulaire  - admin
     */

    public function AddOrModifyNews()
    {
        $errors = []; // tableau des erreurs 
        $news = []; // tableau des donnees recoltées
        //     'title'    => '',
        //     'text'     => '',
        //     'picture'  => '',
        //     'status'   => ''
        // ];

        //var_dump($_POST,$_FILES);


        // verification des elements passés en Post dans le formulaire

        if (
            array_key_exists('title', $_POST)
            && array_key_exists('text', $_POST)
            && array_key_exists('status', $_POST)
        ) {

            $news = [
                'title'         => trim(($_POST['title'])),
                'text'          => trim(($_POST['text'])),
                'picture'       => '',
                'status'        => trim(($_POST['status'])),
            ];

            // verification de la validité du token 
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";


            // pour que l'on accepte une news il faut qu'il y ait au moins un des 3 éléments
            //  titre,  texte ou image de présent 

            // dans le cas d'une modif on teste si il existe une ancienne photo
            $oldPic = ((isset($_POST['photo_recup'])) ? (($_POST['photo_recup']) == ''):true);

            if (($news['title'] == '') && ($news['text'] == '')&& (isset($_FILES['picture']) && $_FILES['picture']['name'] == '')&& $oldPic )
                $errors[] = "Merci de renmplir au moins une information ";

            if ($news['status'] == '')
                $errors[] = "Erreur dans le formulaire";

            // si pas d'erreurs à ce niveau . 
            if (count($errors) == 0) {
                

                $model = new \Models\News();

                /**************************************************
                 * Traitement de l'image - cas d'une création 
                 */
                if ((!isset($_POST['id'])) && isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {
                    //var_dump(' creation');
                    $dossier = "newsPics"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                    $model = new \Models\Uploads(); // on se sert du model Uploads
                    // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                    // ET qui met à jour le tableau d'erreur (passage par reference de $errors)
                    $news['picture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);
                }

                /*******************************************************
                 * Traitement de l'image - cas d'une mofification 
                 */
                if (isset($_POST['id'])) {
                    // avec un nouveau fichier image //
                    if (isset($_FILES['picture']) && $_FILES['picture']['name'] !== '') {

                        //var_dump(' Avec Nouveau fichier image');
              
                        // on traite la nouvelle image 
                        $dossier = "newsPics"; // Nom du dossier dans lequel on va mettre l'image uploadée.
                        $model = new \Models\Uploads(); // on se sert du model Uploads
                        // on appelle  la methode de controle du fichier image qui renvoie le nom concatené avec un uid si tout est ok
                        $news['picture'] = $model->uploadFile($_FILES['picture'], $dossier, $errors);

                        // si il n' y a pas d'erreur dans le nouveau fichier image on efface l'ancienne.

                        if (count($errors) == 0) {
                            $toErase = "public/uploads/".(($_POST['photo_recup']));
                            if (file_exists($toErase)) {
                                unlink($toErase);
                            }
                        } elseif (count($errors) !== 0) {  // sinon on garde l'ancienne
                            $news['picture'] = ($_POST['photo_recup']);
                        }
                        
                    } else {  // sans nouveau fichier image, on garde l'ancienne photo
                        // var_dump(' Sans Nouveau fichier image');
                        $news['picture'] = ($_POST['photo_recup']);
                        // var_dump($addProduct['addPicture']);
                    }
                }

                if (count($errors) == 0) {

                    // On créé notre tableau à mettre dans la BDD tableau de type cle/valaveur
                    $datasToInsert = [
                        'title'             => $news['title'],
                        'text'              => $news['text'],
                        'picture'           => $news['picture'],
                        'status'            => $news['status']
                    ];

                    // ici on est dans le cas d'une création 
                    if (!isset($_POST['id'])) {
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\News();
                        $model->addNewNews($datasToInsert);
                        // On affiche un ou plusieurs messages de validation.
                        $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                    }

                    // si il existe $_POST['id'] on est dans le cas de la mofif et non de la création

                    if (isset($_POST['id'])) {
                        $id_news = trim(htmlspecialchars($_POST['id']));
                        // On appelle la méthode permettant l'INSERT INTO dans la BDD
                        $model = new \Models\News();
                        $model->updateNews($id_news, $datasToInsert);
                    }
                    // on appelle la page de gestion 
                    self::displayFormNews();
                    exit();
                }
            }
            // traitement des erreurs 
            // cas d'une création :
            if (!isset($_POST['id'])) {
                // mise en place d'un nouveau token pour sécuriser la soumission du formulaire 
                $model = new \Models\Tools();
                $token = $model->randomChain(20);
                $_SESSION['auth'] = $token;
                $data['token'] = $token;
                //var_dump($errors);
                // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
                new RendersController('admin/newsAddOrModify', $data, $errors);
                exit();
            }
            // Cas d'une modification on renvoie vers la page de gestion 
            self::displayFormNews();
            exit();
        }
    }



    /********************************************************
     * Delete d'une news - traitement de la demande - admin
     */
    public function deleteNews(){
        $errors = []; // tableau des erreurs 

        // recupération et vérification de la validité de l'id passé en Get exit si pas bon 
        $news = self::idNewsByGetIsOK();
        if (count($errors) == 0) {
            // appel de la methode d'effacement dans la BD
            $model = new \Models\News();
            $eraseok = ($model->delOneNews($news['id_news'])); 
            // effacement de la photo stockée si existe et delete ok pour PDO
            if ($eraseok && (!($news['picture'])=='')) {
                $toErase = "public/uploads/".($news['picture']);
                if (file_exists($toErase)) {
                    unlink($toErase);
                }
            }
        }
        self::displayFormNews();
        exit();

    }

    /*****************************************************************************
     * sortie du tableau 6 dernières news actives pour affichage sur la homePage (requete Ajax) 
     */
    public function ajaxNews()
    {
        $model = new \Models\News();
        $newsToDisplay = $model->getNewsByQuery('status', 'actif',' DESC','4'); 
        include 'public/views/news.phtml';
    } 















}