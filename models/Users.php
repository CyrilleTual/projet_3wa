<?php

namespace Models;

class Users extends Model
{
    protected string $table; // nom de la table 
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id
    // 
    protected int $id_user;
    protected string $firstname;
    protected string $lastname;

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

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $value)
    {
        $this->firstname = $value;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $value)
    {
        $this->lastname = $value;
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
     * @param array $datas => tableau des propriétés / valeurs du noubel user
     */
    public function addNewUser(array $datas)
    {
        return $this->create($this->hydrate($datas));
    }



    public function getUserByEmail($email)
    {
        $params = ['email' => $email];
        return ($this->findBy($params));
    }

    /****************************************************************************************
     * Exemple de methode de sélection complexe utilisant une requête préparée 
     * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
     * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */

    public function getUsersByQuery(string $byColumn = '1', string $datas = '1', string $order = "id_user DESC ", int $limit = 500): array
    {
        $sql = 'SELECT
                    *
                FROM `users`
                WHERE ' . $byColumn . ' = ? ORDER BY users.' . $order . ' LIMIT ' . $limit;
        return $this->findByQuery($sql, [$datas]);
    }

    /****************************************************************************************
     * Exemple de methode de sélection complexe utilisant une requête préparée 
     * @param array $params : tableau critères/valeurs du WHERE
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */
    public function getUsersByQueryArray(array $params, string $order = "id_user DESC ", int $limit = 500): array
    {
        // on appel la methode split pour  transformer le tableau de critère (méthode du model Model)
        [$liste_champs, $valeurs] = $this->split($params);
        // var_dump($liste_champs, $valeurs);  donne : string(30) "lastname = ? AND firstname = ?" array(2) { [0]=> string(5) "bonez" [1]=> string(4) "Jean" }
        $sql = 'SELECT
                    *
                FROM `users`
                WHERE ' . $liste_champs . '  ORDER BY users.' . $order . ' LIMIT ' . $limit;
        return $this->findByQuery($sql, $valeurs);
    }


































    /************************************************
     * récupération d' utilisateur(s) selon critère(s)'
     */
    public function testo()
    {
        // return $this->findAll();
        $params = [
            $this->idName => 12,
            //'lastname' => "*BRAVO mon gars! **"
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
