-- Créer la table `games` pour stocker les informations des parties
CREATE TABLE games (
    game_id INTEGER PRIMARY KEY AUTOINCREMENT,  -- Identifiant unique pour chaque partie
    player_name TEXT NOT NULL,                  -- Nom du joueur
    secret_code TEXT NOT NULL,                  -- Code secret de la partie (ex. "RGBY")
    attempts_remaining INTEGER NOT NULL,        -- Nombre de tentatives restantes
    status TEXT CHECK(status IN ('en cours', 'gagné', 'perdu')) DEFAULT 'en cours', -- Statut de la partie
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date de création de la partie
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Date de la dernière mise à jour
);

-- Créer la table `attempts` pour enregistrer chaque tentative d'un joueur
CREATE TABLE attempts (
    attempt_id INTEGER PRIMARY KEY AUTOINCREMENT, -- Identifiant unique de chaque tentative
    game_id INTEGER,                              -- Référence à l'identifiant de la partie (clé étrangère)
    attempt_code TEXT NOT NULL,                   -- Code proposé par le joueur, ex. "RBGY"
    correct_position INTEGER NOT NULL,            -- Nombre de pions bien placés (noirs)
    correct_color INTEGER NOT NULL,               -- Nombre de pions de la bonne couleur mais mal placés (blancs)
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Date et heure de la tentative
    FOREIGN KEY (game_id) REFERENCES games (game_id) -- Clé étrangère vers la table `games`
);
