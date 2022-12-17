<?php

namespace App\Models;

class Users extends Model
{
    public $table;
    public $lastName;
    public $firstName;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "users";  // nom de la table 
    }

    public function setName($lastName)
    {
        $this->lastName = $lastName;
    }
    public function setFirtsName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getName()
    {
        return $this->lastname;
    }




    /************************************************
     * récupération d' utilisateur(s) selon critère(s)'
     */
    public function testo($model)
    {
        //return $this->create($model);
        $valID = 35;
        return $this->update("id", $valID, $model);
    }






    // /************************************************
    //  * récupération d' utilisateur(s) selon critère(s)'
    //  */
    // public function getAllUsers()
    // {
    //     $table = "users";
    //     $params = [
    //         'id' => 117,
    //     ];
    //     return $this->findBy($table, $params);
    // }
    // //findBy(string $table, array $params = []): array







    /************************************************
     * récupération de toutes les données d'un user
     */
    public function getUserByEmail($email)
    {
        $laRequeteAExecuter = "SELECT * FROM user WHERE user_mail = ? ";
        return $this->findOne($laRequeteAExecuter, [$email]);
    }
    /******************************************************************************
     * Ajout d'un nouvel user suite à la validation puis au controle du formulaire
     */
    public function addNewUser($data)
    {
        $this->addOne(
            'user', // nom table
            'user_lastname, user_firstname, user_mail, user_avatar, user_password', // nom des colonnes
            '?,?,?,?,?', // param fictifs ( même nomber que de colonnes)
            $data // le tableau des données 
        );
    }
}
