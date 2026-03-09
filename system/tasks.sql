
-- do this before creating the table, will enable 👀 emoji support
ALTER DATABASE ff5lmy_homely CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS tasks (
    id INT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    name VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    emoji VARCHAR(10) NOT NULL
) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

INSERT INTO tasks (id, category, name, score, emoji) VALUES
    (1, 'Putzen', 'Saugen', 200, '🧹'),
    (2, 'Putzen', 'Aufnehmen', 200, '🪣'),
    (3, 'Putzen', 'Abstauben', 400, '🧽'),
    (4, 'Putzen', 'Bad', 300, '🛁'),
    (5, 'Putzen', 'Küche', 300, '🍳'),
    (6, 'Putzen', 'Fenster', 500, '🪟'),
    (7, 'Kochen', 'Frühstück', 250, '🥐'),
    (8, 'Kochen', 'Mittagessen', 500, '🍲'),
    (9, 'Kochen', 'Abendessen', 500, '🍽️'),
    (10, 'Waschen', 'Waschen', 250, '🧺'),
    (11, 'Waschen', 'Aufhängen', 250, '👗'),
    (12, 'Waschen', 'Verräumen', 250, '🧴');
