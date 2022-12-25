<?php

namespace Models;

class Items extends Model
{
    protected string $table; // nom de la table -- ne pas toucher
    protected string $idName;  // nom du champ d'identifiant pour les methodes se servant de l'id -- don't touch
    // 
    protected int $id_item;
    protected string $itemRef;
    protected int $id_product;
    protected string $pack;
    protected float $price;
    protected int $stock;
    protected int $id_vat;
    protected string $status;


    public function __construct()
    {
        parent::__construct();  // ne pas oublier sinon destruction de l'instance PDO !
        $this->table = "items";
        $this->idName = "id_item";
    }



    /****************************************************************************************
     * @param string $byColum : liste sous forme de string des clés des colonnes du WHERE
     * @param string $datas : listes des valeurs correspondantes aux clés de $byColumn
     * @param string $order :  ordre du tri 
     * @param int $ limit : nombre maxi d'enregistrements retournés
     * @return array : tableau des enregistrements trouvés 
     */

    public function ItemsByQuery(string $byColumn = '1', string $datas = '1', string $order = " DESC ", int $limit = 500): array
    {
        $sql = 'SELECT  products.id_product, products.productRef, products.teaser, products.description, products.picture, products.status,
                        categories.id_category, categories.categoryName
                FROM ' . $this->table . '
                INNER JOIN categories
                ON products.id_category = categories.id_category
                WHERE ' . $byColumn . ' = ? ORDER BY ' . $this->table . '.' . $this->idName . $order . ' LIMIT ' . $limit;
        return $this->findByQuery($sql, [$datas]);
    }
}
