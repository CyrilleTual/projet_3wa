<?php

namespace App\Models;

class Users extends Model
{
    protected $table; // nom de la table 
    protected $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id
    protected $lastName;
    protected $firstName;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "users";
        $this->idName = "id";
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        // var_dump($this->lastName);
        // die;
        return $this;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }
    public function getName()
    {
        return $this->lastname;
    }



    /************************************************
     * récupération d' utilisateur(s) selon critère(s)'
     */
    public function testo($datas)
    {
        // return $this->findAll();
        $params = [
            'id' => 41,
            //'lastName' => "*BRAVO mon gars! **"
        ];
        return $this->findBy($params);
        // return $this->delete(32);
        // return $this->create($model);
        // $valID = 26;
        // return $this->update($valID, $model);

        //$this->hydrate($datas);
        //$this->create($this->hydrate($datas));
    }
}
