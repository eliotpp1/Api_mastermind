<?php

declare(strict_types=1);
/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [


    // Récupère toutes les parties en cours.
    ['GET', '/api/games', 'game@listGames'],

    //  Crée une nouvelle partie avec le code secret, le nom du joueur et le nombre de tentatives initiales.
    ['POST', '/api/games', 'game@create'],


    // Récupère les détails d'une partie spécifique.
    ['GET', '/api/games/{game_id:\d+}', 'game@detailsGame'],


    // Enregistre une tentative du joueur pour une partie spécifique.
    ['POST', '/api/games/{game_id}/attempts', 'game@attempts'],


    //Récupère toutes les tentatives effectuées pour une partie spécifique.
    ['GET', '/api/games/{game_id}/attempts', 'game@attempts'],


    // Supprime une partie spécifique et toutes les tentatives associées.
    ['DELETE', '/api/games/{game_id:\d+}', 'game@deleteGame'],
];
