CREATE TABLE ⁠ heulhistory ⁠ (⁠ id ⁠ INT NOT NULL AUTO_INCREMENT , ⁠ starttime ⁠ TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ⁠ endtime ⁠ TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (⁠ id ⁠)) ENGINE = InnoDB;

CREATE TABLE ⁠ tracks ⁠ (⁠ id ⁠ INT NOT NULL AUTO_INCREMENT , ⁠ title ⁠ VARCHAR(255) NOT NULL , ⁠ selected ⁠ INT NOT NULL , PRIMARY KEY (⁠ id ⁠)) ENGINE = InnoDB;

INSERT INTO ⁠ tracks ⁠(⁠ title ⁠, ⁠ selected ⁠) VALUES 
('Another brick in the wall',1),
('Back in black',1),
('Bohemian rhapsody',1),
('Clocks',1),
('Creep',1),
('Don`t fear the reaper',1),
('Enter sandman',1),
('Hotel california',1),
('I love rock`n`roll',1),
('Smells like teen spirit',1),
('Stairway to heaven',1),
('Sympathy for the devil',1),
('Under the bridge',1),
('Where is my mind',1),
('Wonderwall',1);