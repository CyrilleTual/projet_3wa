<?php

namespace App\Models;


abstract class DbConnect // ne doit pas être instanciée 
{
    private  $pdo;
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
}