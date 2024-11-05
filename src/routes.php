<?php

declare(strict_types=1);

/*
-------------------------------------------------------------------------------
les routes
-------------------------------------------------------------------------------
 */

return [

    // Test de mise en place de la route
    ['GET', '/', 'game@index'],

    // Récupère toutes les parties en cours.
    ['GET', '/s5/api/v1/games', 'game@listGames'],

    // Crée une nouvelle partie avec le code secret, le nom du joueur et le nombre de tentatives initiales.
    ['POST', '/s5/api/v1/games', 'game@create'],

    // Récupère les détails d'une partie spécifique.
    ['GET', '/s5/api/v1/games/{game_id:\d+}', 'game@detailsGame'],

    // Enregistre une tentative du joueur pour une partie spécifique.
    ['POST', '/s5/api/v1/games/{game_id:\d+}/attempts', 'game@createAttempt'],

    // Récupère toutes les tentatives effectuées pour une partie spécifique.
    ['GET', '/s5/api/v1/games/{game_id:\d+}/attempts', 'game@listAttempts'],

    // Supprime une partie spécifique et toutes les tentatives associées.
    ['DELETE', '/s5/api/v1/games/{game_id:\d+}', 'game@deleteGame'],

    // Gère les requêtes OPTIONS pour l'API
    ['OPTIONS', '/s5/api/v1/games', 'game@options'],
    ['OPTIONS', '/s5/api/v1/games/{game_id:\d+}', 'game@options'],
    ['OPTIONS', '/s5/api/v1/games/{game_id:\d+}/attempts', 'game@options'],
];
