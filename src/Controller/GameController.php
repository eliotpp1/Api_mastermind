<?php

declare(strict_types=1); // strict mode

namespace App\Controller;

use App\Helper\HTTP;
use App\Model\Game;
use App\Model\Attempt;

class GameController extends Controller
{



    /**
     * Récupère toutes les parties en cours.
     * @route [get] /
     */
    public function listGames()
    {
        $this->options();

        $games = Game::getInstance()->findAll();
        if ($games) {
            // Préparer une réponse avec le code 200 pour indiquer un succès
            $response = [
                'status' => 200,
                'data' => $games
            ];
            echo json_encode($response);
        } else {
            // Si aucune partie n'est trouvée, retourner une erreur 404
            $response = [
                'status' => 404,
                'message' => 'No games found'
            ];
            echo json_encode($response);
        }
    }

    /**
     * Créer une nouvelle partie avec le code secret, le nom du joueur et le nombre de tentatives initiales.
     * @route [post] /
     *
     */
    public function create()
    {

        $this->options();

        // Couleurs disponibles pour le code secret
        $colors = ['R', 'B', 'G', 'Y', 'O', 'P'];

        // Générer un code secret de 4 couleurs parmi les 6 disponibles
        $secretCode = '';
        for ($i = 0; $i < 4; $i++) {
            $secretCode .= $colors[array_rand($colors)];
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (Game::getInstance()->create([
            'player_name' => $input['player_name'],
            'secret_code' => $secretCode,
            'attempts_remaining' => $input['attempts_remaining'],
            'status' => 'en cours',
            'created_at' => date('Y-m-d H:i:s')
        ])) {
            HTTP::error(
                201,
                'Game created successfully with secret code: ' . $secretCode
            );
        } else {
            HTTP::error(400, 'Game not created');
        }
    }


    /**
     * Récupère les détails d'une partie spécifique.
     * @route [get] /
     *
     */

    public function detailsGame(int|string $game_id)
    {
        $this->options();

        $game_id = (int) $game_id;
        $game = Game::getInstance()->find($game_id);
        if ($game) {
            HTTP::success(200, $game);
        } else {
            HTTP::error(404, 'Game not found');
        }
    }


    /**
     * Enregistre une tentative du joueur pour une partie spécifique.
     * @route [get]  /api/games/{game_id}/attempts
     * @route [post] /api/games/{game_id}/attempts
     */
    public function attempts(int|string $game_id)
    {
        if ($this->isGetMethod()) {
            $this->listAttempts($game_id);
        } else {
            $this->createAttempt($game_id);
        }
    }

    /**
     * Récupère toutes les tentatives effectuées pour une partie spécifique.
     * @route [get] /api/games/{game_id}/attempts
     */
    public function listAttempts(int|string $game_id)
    {
        $this->options();

        $game_id = (int) $game_id;
        $attempts = Attempt::getInstance()->findAllBy(['game_id' => $game_id]);
        if ($attempts) {
            HTTP::success(200, $attempts);
        } else {
            HTTP::error(404, 'No attempts found');
        }
    }

    /**
     * Enregistre une tentative du joueur pour une partie spécifique.
     * @route [post] /api/games/{game_id}/attempts
     */
    public function createAttempt(int|string $game_id)
    {
        $this->options();

        $game_id = (int) $game_id;

        // Récupérer les données JSON envoyées
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['attempt_code']) || strlen($input['attempt_code']) !== 4) {
            HTTP::error(400, 'Invalid attempt data');
            return;
        }

        $attempt_code = strtoupper($input['attempt_code']);

        // Récupérer la partie
        $game = Game::getInstance()->find($game_id);
        if (!$game) {
            HTTP::error(404, 'Game not found');
            return;
        }

        $secret_code = $game['secret_code'];
        $attempts_remaining = $game['attempts_remaining'];

        // Comparer le code de tentative avec le code secret
        $correct_position = 0;
        $correct_color = 0;
        $used_positions_secret = [];
        $used_positions_attempt = [];

        // Calcul des positions correctes
        for ($i = 0; $i < 4; $i++) {
            if ($attempt_code[$i] === $secret_code[$i]) {
                $correct_position++;
                $used_positions_secret[$i] = true;
                $used_positions_attempt[$i] = true;
            }
        }

        // Calcul des couleurs correctes en position incorrecte
        for ($i = 0; $i < 4; $i++) {
            if (!isset($used_positions_attempt[$i])) {
                for ($j = 0; $j < 4; $j++) {
                    if (!isset($used_positions_secret[$j]) && $attempt_code[$i] === $secret_code[$j]) {
                        $correct_color++;
                        $used_positions_secret[$j] = true;
                        break;
                    }
                }
            }
        }

        // Décrémenter les tentatives restantes
        $attempts_remaining--;
        $status = $correct_position === 4 ? 'gagné' : ($attempts_remaining > 0 ? 'en cours' : 'perdu');

        // Mise à jour de la partie dans la base de données
        Game::getInstance()->update($game_id, [
            'attempts_remaining' => $attempts_remaining,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Enregistrer la tentative dans la table `attempts`
        $attempt = Attempt::getInstance()->create([
            'game_id' => $game_id,
            'attempt_code' => $attempt_code,
            'correct_position' => $correct_position,
            'correct_color' => $correct_color,
            'attempted_at' => date('Y-m-d\TH:i:s\Z')
        ]);

        if ($attempt) {
            // Préparer la réponse
            $response = [
                'attempt_id' => $attempt,
                'game_id' => $game_id,
                'attempt_code' => $attempt_code,
                'correct_position' => $correct_position,
                'correct_color' => $correct_color,
                'attempted_at' => date('Y-m-d\TH:i:s\Z'),
                'attempts_remaining' => $attempts_remaining,
                'status' => $status
            ];
            HTTP::success(201, $response);
        } else {
            HTTP::error(500, 'Failed to create attempt');
        }
    }

    /**
     * Supprime une partie spécifique et toutes les tentatives associées.
     * @route [delete] /api/games/{game_id:\d+}
     */

    public function deleteGame(int|string $game_id)
    {
        $this->options();

        $game_id = (int) $game_id;
        $game = Game::getInstance()->find($game_id);
        if ($game) {
            // Supprimer les tentatives associées
            Attempt::getInstance()->delete($game_id);
            // Supprimer la partie
            Game::getInstance()->delete($game_id);
            $success = "Game deleted successfully";
            HTTP::success(200, ['message' => $success]);
        } else {
            HTTP::error(404, 'Game not found');
        }
    }

    public function options(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
        header('Content-Type: application/json');
    }
}
