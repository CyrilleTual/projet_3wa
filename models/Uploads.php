<?php

namespace Models;

const UPLOADS_DIR = 'public/uploads/';

// Extensions acceptées pour les images
const FILE_EXT_IMG = ['jpg', 'jpeg', 'gif', 'png'];

// Constante MIME_TYPES permettant de vérifier les fichiers uploadés

const MIME_TYPES = array(

    'txt' => 'text/plain',
    'htm' => 'text/html',
    'html' => 'text/html',
    'php' => 'text/html',
    'css' => 'text/css',
    'js' => 'application/javascript',
    'json' => 'application/json',
    'xml' => 'application/xml',
    'swf' => 'application/x-shockwave-flash',
    'flv' => 'video/x-flv',

    // images
    'png' => 'image/png',
    'jpe' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'jpg' => 'image/jpeg',
    'gif' => 'image/gif',
    'bmp' => 'image/bmp',
    'ico' => 'image/vnd.microsoft.icon',
    'tiff' => 'image/tiff',
    'tif' => 'image/tiff',
    'svg' => 'image/svg+xml',
    'svgz' => 'image/svg+xml',

    // archives
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'exe' => 'application/x-msdownload',
    'msi' => 'application/x-msdownload',
    'cab' => 'application/vnd.ms-cab-compressed',

    // audio/video
    'mp3' => 'audio/mpeg',
    'qt' => 'video/quicktime',
    'mov' => 'video/quicktime',

    // adobe
    'pdf' => 'application/pdf',
    'psd' => 'image/vnd.adobe.photoshop',
    'ai' => 'application/postscript',
    'eps' => 'application/postscript',
    'ps' => 'application/postscript',

    // ms office
    'doc' => 'application/msword',
    'rtf' => 'application/rtf',
    'xls' => 'application/vnd.ms-excel',
    'ppt' => 'application/vnd.ms-powerpoint',

    // open office
    'odt' => 'application/vnd.oasis.opendocument.text',
    'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
);

class Uploads
{

    //Définit le répertoire dans lequel télécharger les fichiers utilisateurs
    // public function uploadFile(array $file, string $dossier, array &$errors, string $folder = UPLOADS_DIR, array $fileExtensions = FILE_EXT_IMG)
    public function uploadFile(array $file, string $dossier, array &$errors, string $folder = UPLOADS_DIR, array $fileExtensions = FILE_EXT_IMG)
    {

        $filename = '';
        // On récupère l'extension du fichier pour vérifier si elle est dans $fileExtensions  ( $file -> [] qui vient du formulaire)
        $tmpNameArray = explode(".", $file["name"]);  // ["name"]=> string(47) "Capture d’écran 2022-12-22 à 17.20.14.png" 
        $tmpExt = end($tmpNameArray); // string(3) "png"



        if ($file["error"] === UPLOAD_ERR_OK) {  // on check la cle error du champ de formilaire de type file 
            $tmpName = $file["tmp_name"];        // chemin vers le fichier temporaire

            if (in_array($tmpExt, $fileExtensions)) {    // check si extention du fichier téléchargé est dans notre tableau
                $filename = uniqid() . '-' . basename($file["name"]);
                if (!move_uploaded_file($tmpName, $folder . $dossier . "/" . $filename)) {
                    $errors[] = "Le fichier n'a pas été enregistré correctement";
                }
                // mime_content_type
                // Détecte le type de contenu d'un fichier.
                // On vérifie le contenue de fichier, pour voir s'il appartient aux MIMES autorises.
                if (!in_array(mime_content_type($folder . $dossier . "/" . $filename), MIME_TYPES, true)) {
                    // var_dump(mime_content_type($folder.$filename));
                    $errors[] = "Le fichier n'a pas été enregistré correctement car son contenu ne correspond pas à son extension !";
                }
            } else {
                $errors[] = "Ce type de fichier n'est pas autorisé !";
            }
        } else if ($file["error"] == UPLOAD_ERR_INI_SIZE || $file["error"] == UPLOAD_ERR_FORM_SIZE) {
            //fichier trop volumineux
            $errors[] = "Le fichier est trop volumineux";
        } else {
            $errors[] = "Une erreur a eu lieu au moment de l'upload";
        }
        $filename = $dossier . "/" . $filename;
        return $filename;
    }
}
