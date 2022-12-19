<?php

namespace Models;


class Model
{
    private  $pdo; // représente l'instance de connexion à la DB

    /******************************************************************
     * création d'un instance PDO pour connextion à la base de données
     */
    public function __construct()
    {
        $config = require 'config/database_dist.php'; // recup des paramètres de connxion
        $this->pdo = new \PDO("mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['db_utf'], $config['username'], $config['password'], [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);
    }

    /***********************************************************************************
     * Définition de méthodes génériques pour les manupulations de la base de données
     */

    /****************************************************************
     * findAll -> retourne tous les enregistrements d'une tabble  (READ)
     * @param : aucun, se sert de l'objet sur lequel la méthode est utilisée
     * @return array Tableau des enregistrements trouvés  
     */
    protected function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM $this->table ");
        $query->execute();
        return $query->fetchAll();
    }
    // utilisation : return $this->findAll(); 

    /***********************************************************************************
     * findBY -> Sélection les enregistrements repondants à un ou plusieurs critères  (READ)
     * @param array $ tableau de critères ['champ1'=>'value1', 'champ2=>value2' etc...]
     * @return array Tableau des enregistrements trouvés  
     */

    protected function findBy(array $params = []): array
    {
        // On boucle pour éclater $params -> stockage des champs et des values indépendament
        $champs = [];
        $valeurs = [];
        foreach ($params as $champ => $valeur) {
            $champs[] = "$champ = ?";
            $valeurs[] = $valeur;
        }
        // On transforme les tableaux en une chaîne de caractères séparés par des AND
        $liste_champs = implode(' AND ', $champs);   //  ex : "user_id = ? AND user_lastName = ?"
        // preparation de la requête
        $query = $this->pdo->prepare("SELECT * FROM $this->table WHERE $liste_champs", $valeurs);
        // execution  de la requête
        $query->execute($valeurs);
        return $query->fetchAll();
        /**
         * Exemple d'utilisation : 
         *  $params = ['id' => 28,'lastName' => "*BRAVO mon gars! **"];
         *  return $this->findBy($params);
         */
    }


    /*****************************************************************************************
     * delete -> Suppression d'un enregistrement selon un id  (DELETE)
     * @param int $idValue : valeur de l'id 
     * @return bool 
     */
    public function delete(int $idValue): bool
    {
        $query = $this->pdo->prepare("DELETE FROM $this->table WHERE  $this->idName = ?");
        return $query->execute([$idValue]);
    }
    // ex :  return $this->delete(32);

    /***************************************************************************************
     * Création d'un enregistrement à partir d'un objet hydraté (CREATE)
     * @param Model : $model Objet hydraté à inserer dans la Db, de la classe Database (par l'extend)
     * @return objet de la classe PDOStatement ou false si problème d'execution 
     */
    protected function create(Model $model): object // rq: idem addone
    {
        $champs     = []; // colonnes du tableau
        $inter      = [];  // signes ? pour préparer la requête
        $valeurs    = []; // tableau des valeurs à insérer 

        // Boucle sur l'objet pour recupérer les propriétés/valeurs mais on enlève les propriétés "pdo" et "table" 
        foreach ($model as $champ => $valeur) {
            if ($valeur     !== null && $champ != 'pdo' && $champ != 'table' && $champ != 'idName') {
                $champs[]   = $champ;
                $inter[]    = "?";
                $valeurs[]  = $valeur;  // ex: array(2) { [0]=> string(4) "tutu" [1]=> string(4) "lolo" }
            }
        }
        // preparation des éléments à insérer dans la requête
        $table          = $this->table; // nom de la table 
        $liste_champs   = implode(',',  $champs); // ex: string(19) "lastName, firstName"
        $liste_inter    = implode(',', $inter);  // ex:  "?, ?"
        // requête 
        $query = $this->pdo->prepare('INSERT INTO ' . $table . '(' . $liste_champs . ') values (' . $liste_inter . ')');
        $query->execute($valeurs);
        return $query;
        // ex d'utilisation : return $this->create($model); 
    }
    /****************************************************************************************
     * Mise à jour d'un enregistrement  (UPDATE )
     * @param int $id : id de l'enregistrement à modifier
     * @param Model $model : Objet à modifier, de la classe Database (par l'extend)
     * @return objet de la classe PDOStatement ou false si problème d'execution 
     */
    public function update(int $id, Model $model)
    {
        $champs     = [];
        $valeurs    = [];
        // Boucle pour recupérer les propriétés/valeurs mais on enlève les propriétés "pdo" et "table" 
        // on va réassigner tous les champs avec les valeurs issues de $model
        foreach ($model as $champ => $valeur) {
            if ($valeur !== null && $champ != 'pdo' && $champ != 'table' && $champ != 'idName') {
                $champs[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
            // $champs si 2 champs : array(2) { [0]=> string(12) "lastName = ?" [1]=> string(13) "firstName = ?" } 
        }
        $valeurs[] = $id; // pour transformer un int en array
        // On transforme le tableau "champs" en une chaine de caractères
        $liste_champs = implode(', ', $champs);
        // Requête
        $query = $this->pdo->prepare("UPDATE $this->table SET $liste_champs  WHERE $this->idName = ?");
        $query->execute($valeurs);
        return $query;
        // ex d'utilisation :  $valID = 33; return $this->update($valID, $model);
    }

    /***************************************************************************************************
     * Hydratation automatique des données -> Setter automatique 
     * Atention demande que les setters soient nommés de la forme "setXxxx" où Xxxx = nom attribut
     * @param array $datas Tableau associatif des données (respecter l'ordre )
     * @return self Retourne l'objet hydraté
     */
    public function hydrate(array $datas): Model
    {
        foreach ($datas as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $setter = 'set' . ucfirst($key);
            // Si le setter correspondant existe.
            // exemple :($setter, $key, $value) donne :(string(12) "setFirstName" string(9) "firstName" string(4) "pole")
            if (method_exists($this, $setter)) {   // On appelle le setter si on le trouve . 
                $this->$setter($value); // hydraration de la propriété
            }
        }
        return $this;
    }
    /**
     * Utilisation : $this->hydrate($datas) ( voir le README pour plus d'indormations)
     * penser aussi que "$this->create($this->hydrate($datas));" est possible 
     */
}
