-- Core users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Available tracks for babyphone soothing playlist
CREATE TABLE IF NOT EXISTS `tracks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction table: which user has selected which tracks (many-to-many)
CREATE TABLE IF NOT EXISTS `user_tracks` (
  `user_id` INT NOT NULL,
  `track_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `track_id`),
  CONSTRAINT `fk_user_tracks_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_tracks_track`
    FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Babyphone crying history linked to a user
CREATE TABLE IF NOT EXISTS `heulhistory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `starttime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_heulhistory_user` (`user_id`),
  CONSTRAINT `fk_heulhistory_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed tracks once (safe on reruns)
INSERT INTO `tracks` (`title`)
SELECT * FROM (
  SELECT 'Another brick in the wall' UNION ALL
  SELECT 'Back in black' UNION ALL
  SELECT 'Bohemian rhapsody' UNION ALL
  SELECT 'Clocks' UNION ALL
  SELECT 'Creep' UNION ALL
  SELECT 'Don`t fear the reaper' UNION ALL
  SELECT 'Enter sandman' UNION ALL
  SELECT 'Hotel california' UNION ALL
  SELECT 'I love rock`n`roll' UNION ALL
  SELECT 'Smells like teen spirit' UNION ALL
  SELECT 'Stairway to heaven' UNION ALL
  SELECT 'Sympathy for the devil' UNION ALL
  SELECT 'Under the bridge' UNION ALL
  SELECT 'Where is my mind' UNION ALL
  SELECT 'Wonderwall'
) AS `seed`
WHERE NOT EXISTS (SELECT 1 FROM `tracks`);

-- Seed user_tracks: every user selects all tracks by default (safe on reruns)
INSERT INTO `user_tracks` (`user_id`, `track_id`)
SELECT `u`.`id`, `t`.`id`
FROM `users` AS `u`
CROSS JOIN `tracks` AS `t`
WHERE NOT EXISTS (
  SELECT 1 FROM `user_tracks` AS `ut`
  WHERE `ut`.`user_id` = `u`.`id` AND `ut`.`track_id` = `t`.`id`
);