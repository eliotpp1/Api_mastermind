<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Avatar;

class AvatarController extends Controller
{
    /**
     * Page d'accueil pour lister tous les avatars.
     * @route [get] /
     *
     */
    public function index()
    {
        // récupérer les informations sur les avatars
        $avatars = Avatar::getInstance()->findAll();
        // dans les vues TWIG, on peut utiliser la variable avatars
        $this->display('avatars/index.html.twig', compact('avatars'));
    }

    /**
     * Afficher le formulaire de saisie d'un nouvel avatar ou traiter les
     * données soumises présentent dans $_POST.
     * @route [get]  /avatars/ajouter
     * @route [post] /avatars/ajouter
     *
     */
    public function create()
    {
        if ($this->isGetMethod()) {
            $this->display('avatars/create.html.twig');
        } else {
            // dd($_POST);
            // 1. préparer le nom du fichier (le nom original est modifié)
            $filename = '';
            // traiter l'éventuelle image de l'avatar
            if (!empty($_FILES['illustration']) && $_FILES['illustration']['type'] == 'image/webp') {
                // récupérer le nom et emplacement du fichier dans sa zone temporaire
                $source = $_FILES['illustration']['tmp_name'];
                // récupérer le nom originel du fichier
                $filename = $_FILES['illustration']['name'];
                // ajout d'un suffixe unique
                // récupérer séparément le nom du fichier et son extension
                $filename_name = pathinfo($filename, PATHINFO_FILENAME);
                $filename_extension = pathinfo($filename, PATHINFO_EXTENSION);
                // produire un suffixe unique
                $suffix = uniqid();
                $filename = $filename_name . '_' . $suffix . '.' . $filename_extension;
                // construire le nom et l'emplacement du fichier de destination
                $destination = APP_ASSETS_DIRECTORY . 'image' . DS . 'avatar' . DS . $filename;
                // placer le fichier dans son dossier cible (le fichier de la zone temporaire est effacé)
                move_uploaded_file($source, $destination);
            }
            // 2. exécuter la requête d'insertion
            Avatar::getInstance()->create([
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'display_name' => trim($_POST['display_name']),
                'illustration' => $filename,
            ]);
            HTTP::redirect('/');
        }
    }

    /**
     * Effacer un avatar.
     * @route [get] /avatars/effacer/{id}
     *
     */
    public function delete(
        int|string $id
    ) {
        // 1. effacer
        // à compléter ...
        // 2. revenir sur la page d'accueil
        // à compléter ...
    }
}
