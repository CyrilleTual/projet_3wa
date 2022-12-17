<?php

namespace App;

class Autoload
{
    /**
     * spl_autoload_register se délenche si appel à une classe inconnue (new) et 
     * appelle la methode inclusionAuto en passant le nom de la classe comme argument
     */
    static function register()
    {
        spl_autoload_register([
            __CLASS__,
            'inclusionAuto'
        ]);
    }


    public static function inclusionAuto($className)
    {
        /**
         * transforme le namespace (de la classe $className)en chemin valide vers la classe
         * __NAMESPACE__ renvoie le namespace courant pour (APP) l'enlever 
         */

        $className = str_replace(__NAMESPACE__ . '\\', '', $className . ".php");

        require_once __DIR__ . "/" . $className;
    }
}
