CREATE TABLE `devices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(45) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `video` varchar(150) DEFAULT NULL,
  `message` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mac_UNIQUE` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
