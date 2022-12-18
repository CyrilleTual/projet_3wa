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

        $datas = [
            'lastName' => 'bon',
            'firstName' => 'Jean',
        ];

        $result = $model->testo($datas);


        var_dump($result);

        new RendersController('formConnect');
    }
}
