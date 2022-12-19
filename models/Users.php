<?php

namespace Models;

class Users extends Model
{
    protected string $table; // nom de la table 
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id
    // 
    protected int $id_user;
    protected string $firstName;
    protected string $lastName;
    protected string $sex;
    protected string $email;
    protected string $password;
    protected string $role;
    protected string $status;

    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "users";
        $this->idName = "id_user";
    }

    /**
     * Getters et Setters 
     */


    public function getId_user(): int
    {
        return $this->id_user;
    }

    public function setId_user(int $value)
    {
        $this->id_user = $value;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $value)
    {
        $this->firstName = $value;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $value)
    {
        $this->lastName = $value;
    }
    public function getSex(): string
    {
        return $this->sex;
    }

    public function setSex(string $value)
    {
        $this->sex = $value;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $value)
    {
        $this->email = $value;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $value)
    {
        $this->password = $value;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $value)
    {
        $this->role = $value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $value)
    {
        $this->status = $value;
    }


    // ///////////////////////////////////////////////////////////////////////////////////////////////

    /******************************************************************************
     * Ajout d'un nouvel user suite à la validation puis au controle du formulaire
     */
    public function addNewUser($datas)
    {
        return $this->create($this->hydrate($datas));
    }


    // /************************************************
    //  * récupération d' utilisateur(s) selon critère(s)'
    //  */
    // public function testo($datas)
    // {
    //     // return $this->findAll();
    //     $params = [
    //         $this->idName => 26,
    //         //'lastName' => "*BRAVO mon gars! **"
    //     ];
    //     return $this->findBy($params);
    //     return $this->delete(32);
    //     return $this->create($model);
    //     $valID = 26;
    //     return $this->update($valID, $model);

    //     //$this->hydrate($datas);
    //     //$this->create($this->hydrate($datas));
    // }
}
