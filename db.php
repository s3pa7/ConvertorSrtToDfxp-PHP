CREATE TABLE `file_uploaded` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`uploaded_file_name` varchar(45) NOT NULL,
`uploaded_file_size` varchar(45) NOT NULL,
`uploaded_file_hash` varchar(45) NOT NULL,
`created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
KEY `uploaded_file_name` (`uploaded_file_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;