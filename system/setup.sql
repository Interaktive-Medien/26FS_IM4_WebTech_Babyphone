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
  `selected` INT NOT NULL,
  PRIMARY KEY (`id`)
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
INSERT INTO `tracks` (`title`, `selected`)
SELECT * FROM (
  SELECT 'Another brick in the wall', 1 UNION ALL
  SELECT 'Back in black', 1 UNION ALL
  SELECT 'Bohemian rhapsody', 1 UNION ALL
  SELECT 'Clocks', 1 UNION ALL
  SELECT 'Creep', 1 UNION ALL
  SELECT 'Don`t fear the reaper', 1 UNION ALL
  SELECT 'Enter sandman', 1 UNION ALL
  SELECT 'Hotel california', 1 UNION ALL
  SELECT 'I love rock`n`roll', 1 UNION ALL
  SELECT 'Smells like teen spirit', 1 UNION ALL
  SELECT 'Stairway to heaven', 1 UNION ALL
  SELECT 'Sympathy for the devil', 1 UNION ALL
  SELECT 'Under the bridge', 1 UNION ALL
  SELECT 'Where is my mind', 1 UNION ALL
  SELECT 'Wonderwall', 1
) AS `seed`
WHERE NOT EXISTS (SELECT 1 FROM `tracks`);

-- Seed heulhistory once (safe on reruns)
-- Note: Uses the first existing user. If no user exists yet, no rows are inserted.
INSERT INTO `heulhistory` (`user_id`, `starttime`, `endtime`)
SELECT `u`.`id`, `seed`.`starttime`, `seed`.`endtime`
FROM (
  SELECT `id`
  FROM `users`
  ORDER BY `id`
  LIMIT 1
) AS `u`
JOIN (
  SELECT NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR AS `starttime`,
         NOW() - INTERVAL 3 DAY + INTERVAL 2 HOUR + INTERVAL 12 MINUTE AS `endtime`
  UNION ALL
  SELECT NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR,
         NOW() - INTERVAL 2 DAY + INTERVAL 9 HOUR + INTERVAL 7 MINUTE
  UNION ALL
  SELECT NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR,
         NOW() - INTERVAL 1 DAY + INTERVAL 1 HOUR + INTERVAL 18 MINUTE
) AS `seed`
WHERE NOT EXISTS (SELECT 1 FROM `heulhistory`);