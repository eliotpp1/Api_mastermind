<?php

namespace App\Helper;

class HTTP
{
    /**
     * Retourne l'URL complète.
     *
     * @param string $url
     * @return string
     * TODO vérifier le type de sortie string ?
     */
    public static function url(
        string $url = ''
    ): string {
        // ajouter le slash si nécéssaire
        $url = substr($url, 0, 1) != '/' ? '/' . $url : $url;
        echo APP_ROOT_URL_COMPLETE . $url;
    }

    /**
     * Redirige vers une route.
     *
     * @param string $url  la route vers laquelle la redirection doit s'opérer
     * @return void
     */
    public static function redirect(
        string $url = '/'
    ): void {
        header('Location: ' . APP_ROOT_URL_COMPLETE . $url);
    }

    /**
     * Gère une réponse d'erreur HTTP avec un message JSON.
     *
     * @param int $code Le code HTTP de l'erreur
     * @param string $message Le message de l'erreur
     * @return void
     */
    public static function error(int $code, string $message): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['code' => $code, 'error' => $message]);
    }


    // Ajout dans App\Helper\HTTP
    public static function success(int $status, array $data): void
    {
        http_response_code($status);
        echo json_encode(['status' => $status, 'data' => $data]);
    }
}
