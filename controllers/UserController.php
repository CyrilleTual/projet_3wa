<?php

namespace App\Controllers;

class UserController
{
    // fonction test de la Bv
    public function test()
    {

        $model = new \App\Models\Users();
        // $model->setName("bernardo");
        // $model->setFirtsName("zoro");
        $errors = []; // initialisation du tableau des erreurs 


        $errorsArray = new \App\Models\ErrorMessages(); // 
        $messagesErrors = $errorsArray->getMessages();

        $datas = [
            'lastName' => 'bon',
            'firstName' => 'Jean',
        ];

        $result = $model->testo($datas);

        var_dump($errors);



        $errors[] = $messagesErrors[1];

        // var_dump($result);
        var_dump($errors);

        new RendersController('formConnect');
    }
}
