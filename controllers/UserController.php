<?php

namespace Controllers;

class UserController
{

    /*****************************************************************************************************
     * Affichage du formulaire d'enregistrement d'un nouvel user
     */
    public function displayFormRegister()
    {
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;

        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('formRegister', $token);
    }

    /*****************************************************************************************************
     * Traitement, validation et soumission du form de création de compte
     */
    public function register()
    {
        // initialisation des variables 
        $valids = [];
        $addUser = [
            'addNom'                => '',
            'addPrenom'             => '',
            'addEmail'              => '',
            'addPassword'           => '',
            'addPassword_confirme'  => '',
        ];

        // initialisation du tableau des erreurs 
        $errors = [];
        $errorsArray = new \Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();



        // vérification du remplissage du formulaire 
        if (
            array_key_exists('firstname', $_POST)
            && array_key_exists('lastname', $_POST)
            && array_key_exists('email', $_POST)
            && array_key_exists('password', $_POST)
            && array_key_exists('passwordConfirm', $_POST)
        ) {

            $addUser = [
                'addPrenom'             => trim(ucfirst($_POST['firstname'])),
                'addNom'                => trim(strtoupper($_POST['lastname'])),
                'addEmail'              => trim(strtolower($_POST['email'])),
                'addPassword'           => trim($_POST['password']),
                'addPassword_confirme'  => trim($_POST['passwordConfirm']),
            ];
            // Mise en oeuvre des contrôles :
            //1) validité du token 
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = "Une erreur est apparue lors de l'envoi du formulaire !";

            //2) validité du nom
            if ($addUser['addNom'] == '')
                $errors[] = "Veuillez renseigner votre nom SVP !";

            // 3) validité du prénom ;
            if ($addUser['addPrenom'] == '') {
                $errors[] = "Veuillez renseigner votre prénom SVP !";
                $_POST['firstname'] = " "; // pour effacer reaffichage 
            }


            // 4) validité de l'email
            if (!filter_var($addUser['addEmail'], FILTER_VALIDATE_EMAIL))
                $errors[] =  'Veuillez renseigner un email valide SVP !';

            // 5) validité du mot de passe    
            if (empty($addUser['addPassword']))
                $errors[] = "Veuillez renseigner votre mot de passe !";

            if ($addUser['addPassword'] !== $addUser['addPassword_confirme'])
                $errors[] = "Vous n'avez pas confirmé correctement votre mot de passe !";

            // si le formulaire est valide on va tester si l'adresse mail existe dans la DB: 
            $model = new \Models\Users();
            $userExist = $model->getUserByEmail($addUser['addEmail']); // retourne un tableau contenant l'user si existe ou tableau vide
            if (!empty($userExist)) {
                $errors[] = 'Cette adresse e-mail existe déjà !';
            }


            /// si tout est ok on traite l'insertion dans la Db
            if (count($errors) == 0) {

                // on securise le PWD
                $datas = [
                    'firstname' => trim(ucfirst($_POST['firstname'])),
                    'lastname'  => trim(strtoupper($_POST['lastname'])),
                    'email'     => trim(($_POST['email'])),
                    'password'  => trim($_POST['password']),
                ];
                //$password = password_hash($addUser['addPassword'], PASSWORD_DEFAULT);

                $datas['password'] = password_hash($datas['password'], PASSWORD_DEFAULT);

                $result = $model->addNewUser($datas); // retoune null si echec du trairement

                // si l'insersion dans la DB est ok on recuprère toutes les informations de l'utilisateur et on creer la session 
                $userExist = $model->getUserByEmail($addUser['addEmail']); // retourne un tableau contenant l'user si existe ou tableau vide
                if (empty($userExist)) {
                    $errors[] = 'Un problème est survenu';
                } else {
                    $userExist = $userExist[0]; // recupere l'user seul 

                    // creationde session
                    $_SESSION['user'] = [
                        'id'            => $userExist['id_user'],
                        'firstName'     => $userExist['firstname'],
                        'lastName'      => $userExist['lastname'],
                        'email'         => $userExist['email'],
                        'role'          => $userExist['role'],
                        'status'        => $userExist['status'],
                    ];

                    $_SESSION['message']  = "bienvenue vous êtes maintenant enregistré et connecté ! ";
                    new RendersController('homePage');
                    exit();
                }
                ///////////////////////////////////////////////////////////////////////
                /////////////////////////////////////////////////////////////////////////

            }
        }
        // reaffichage de la vue avec regeration du token et passage du tableau d'erreurs
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token 
        new RendersController('formRegister', $token, $errors);
    }


    /*****************************************************************************************************
     * Affichage du formulaire de connexion 
     */
    public function displayFormConnect()
    {
        // mise en place d'un token pour sécuriser la soumission du formulaire 
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token qui sera transmis par le render sous $data 
        new RendersController('formConnect', $token);
    }

    /*****************************************************************************************************
     * Traitement, validation du login de session 
     */
    public function checkAndConnect()
    {
        $model = new \Models\Users();

        $errors = []; // initialisation du tableau des erreurs 
        $errorsArray = new \Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();
        /**
         * et quand une erreur se produit on alimente le tableau des erreurs :
         * $errors[] = $messagesErrors [x];  où X est le numero du message d'erreur
         */

        // initilisation du tableau de récupération des datas
        $authUser = [
            'email'              => '',
            'password'           => ''
        ];
        // recupération des datas si les clés existent 
        if (array_key_exists('email', $_POST) && array_key_exists('password', $_POST)) {

            // on peuple le tableau des datas 
            $authUser = [
                'email' => trim(strtolower($_POST['email'])),
                'password' => ($_POST['password']),
            ];

            // verif de la validité du email par fonction native pph filter_var sur FILTER_VALIDATE_EMAIL
            if (!filter_var($authUser['email'], FILTER_VALIDATE_EMAIL))
                $errors[] = $messagesErrors[6];

            // verif mdp bien renseigne
            if (empty($authUser['password']))
                $errors[] = $messagesErrors[7];

            // verif token
            if (isset($_SESSION['auth']) && $_SESSION['auth'] != $_POST['token'])
                $errors[] = $messagesErrors[0];

            // en l'absence d'erreur  

            if (count($errors) == 0) {

                // on va tester l'existance du user dans la Db

                $model = new \Models\Users();
                $userExist = $model->getUserByEmail($authUser['email']); // retourne un tableau contenant l'user si existe ou tableau vide
                if (empty($userExist)) {
                    $errors[] = $messagesErrors[8];
                    // var_dump($errors); -> array(1) { [0]=> string(21) "Erreur identification" }
                } else {
                    $userExist = $userExist[0]; // recupere l'user seul 
                    // on verifie la concordence des mots de passe 
                    $pwdForm = $authUser['password'];
                    $pwdDataBase = $userExist["password"];
                    //$passwordOK = password_verify($authUser['password'], $userExist['password']);
                    $passwordOK = password_verify($pwdForm, $pwdDataBase);

                    //$passwordOK = ($pwdForm === $pwdDataBase); // pour test pwd non cryptés

                    if ($userExist !== false && $passwordOK) {

                        //verification du statut actif de l'utilisateur
                        $status = $userExist["status"];

                        if ($status !== 'actif') {
                            $_SESSION['message']  = "Vous n'êtes pas autorisé à vous connecter";
                            new RendersController('homePage');
                            exit();
                        } else {
                            // creationde session
                            $_SESSION['user'] = [
                                'id'            => $userExist['id_user'],
                                'firstName'     => $userExist['firstname'],
                                'lastName'      => $userExist['lastname'],
                                'email'         => $userExist['email'],
                                'role'          => $userExist['role'],
                                'status'        => $userExist['status'],
                            ];

                            $_SESSION['message']  = "bien connecté ! ";
                            new RendersController('homePage');
                            exit();
                        }
                    } else {
                        $errors[] = $messagesErrors[8];
                    }

                    //////////////////////////////////////////////////////////////////////////////////
                }
            }
        }


        // reaffichage de la vue avec regeration du token et passge du tableau d'erreurs
        $model = new \Models\Tools();
        $token = $model->randomChain(20);
        $_SESSION['auth'] = $token;
        // affichage de la vue d'affichage en passant $token 
        new RendersController('formConnect', $token, $errors);
    }

    /*****************************************************************************************************
     * Deconnexion / fin de session
     */
    public function logoutUser()
    {
        unset($_SESSION['user']); // methode de cyrille
        // $_SESSION['user'] = []; // methode de micke
        session_destroy();

        new RendersController('homePage');
        exit();
    }






















    /*****************************************************************************
     ***************************************************************************** 
     */
    /**
     * TEST  de la selection selon plusieurs critères sur une table 
     */
    public function test()
    {

        $model = new \Models\Users();

        $errors = []; // initialisation du tableau des erreurs 
        $errorsArray = new \Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();

        $datas = [
            'lastName' => 'bonez',
            'firstName' => 'Jean',
        ];
        $result = $model->getUsersByQueryArray($datas, "id_user DESC ", 5);

        // $result = $model->testo();

        var_dump($result);

        //$errors[] = $messagesErrors[1];

        new RendersController('formConnect');
    }
}
