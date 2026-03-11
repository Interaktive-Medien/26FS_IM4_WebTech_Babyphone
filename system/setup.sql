-- Core users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Physical babyphone devices (device_code is printed on the device)
CREATE TABLE IF NOT EXISTS `devices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `device_code` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_devices_code` (`device_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction table: which user is connected to which device (many-to-many)
CREATE TABLE IF NOT EXISTS `user_has_device` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `device_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_device` (`user_id`, `device_id`),
  CONSTRAINT `fk_user_has_device_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_has_device_device`
    FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Available tracks for babyphone soothing playlist
CREATE TABLE IF NOT EXISTS `tracks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Junction table: which tracks are selected on which device (many-to-many)
CREATE TABLE IF NOT EXISTS `device_tracks` (
  `device_id` INT NOT NULL,
  `track_id` INT NOT NULL,
  PRIMARY KEY (`device_id`, `track_id`),
  CONSTRAINT `fk_device_tracks_device`
    FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_device_tracks_track`
    FOREIGN KEY (`track_id`) REFERENCES `tracks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Babyphone crying history linked to a device (only the device writes these)
CREATE TABLE IF NOT EXISTS `heulhistory` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `device_id` INT NOT NULL,
  `starttime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_heulhistory_device` (`device_id`),
  CONSTRAINT `fk_heulhistory_device`
    FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
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

-- Seed device_tracks: every device selects all tracks by default (safe on reruns)
INSERT INTO `device_tracks` (`device_id`, `track_id`)
SELECT `d`.`id`, `t`.`id`
FROM `devices` AS `d`
CROSS JOIN `tracks` AS `t`
WHERE NOT EXISTS (
  SELECT 1 FROM `device_tracks` AS `dt`
  WHERE `dt`.`device_id` = `d`.`id` AND `dt`.`track_id` = `t`.`id`
);
