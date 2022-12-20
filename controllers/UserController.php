<?php

namespace Controllers;

class UserController
{

    /**
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

    /**
     * Traitement, validation et soumission du form de création de compte
     */
    public function register()
    {
        $model = new \Models\Users();

        /**
         * Controle du token 
         */

        $token_post = ($_POST['token']);
        $token_session = ($_SESSION['auth']);
        var_dump($token_post, $token_session);

        $datas = [
            'firstName' => trim(ucfirst($_POST['firstName'])),
            'lastName'  => trim(strtoupper($_POST['lastName'])),
            'sex'       => trim(($_POST['sex'])),
            'email'     => trim(($_POST['email'])),
            'password'  => trim($_POST['password']),
        ];

        $result = $model->addNewUser($datas);
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
