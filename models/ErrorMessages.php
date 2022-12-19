<?php

namespace App\Models;


class ErrorMessages
{
    public function getMessages()
    {
        $messagesErrors = [
            /* 0 */
            "Erreur lors de l'envoie du formulaire",
            /* 1 */ "Ce message indique que quelque chose pose un p'tit problème",
            /* 2 */ "Veuillez renseigner votre date de naissance",

        ];
        return $messagesErrors;
    }
}


/**
 * 
 * Utilisation : Dans le controller :
 * 
 * $errors = []; // initialisation du tableau des erreurs 
 * 
 * $errorsArray = NEW \Models\ErrorMessages(); // 
 * $messagesErrors = $errorsArray->getMessage();
 * 
 * et quand une erreur se produit on alimente le tableau des erreurs :
 * 
 * $errors[] = $messagesErrors [x];  où X est le numero du message d'erreur
 * 
 */
