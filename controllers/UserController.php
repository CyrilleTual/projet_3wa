<?php

namespace App\Controllers;

class UserController
{
    // fonction test de la Bv
    public function test()
    {

        $model = new \App\Models\Users();
        $model->setName("**sdfsdfdsf****");
        $model->setFirtsName("loloy");

        $result =  $model->testo($model);
        //var_dump($result);

        new RendersController('formConnect');
    }
}
