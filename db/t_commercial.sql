CREATE TABLE IF NOT EXISTS t_commercial (
	id INT(11) NOT NULL AUTO_INCREMENT,
	code INT(12) DEFAULT NULL,
	raisonSocial VARCHAR(255) DEFAULT NULL,
	nomContact VARCHAR(50) DEFAULT NULL,
	Adresse VARCHAR(255) DEFAULT NULL,
	Rue VARCHAR(50) DEFAULT NULL,
	tel1 VARCHAR(50) DEFAULT NULL,
	tel2 VARCHAR(50) DEFAULT NULL,
	email VARCHAR(50) DEFAULT NULL,
	created DATETIME DEFAULT NULL,
	createdBy VARCHAR(50) DEFAULT NULL,
	updated DATETIME DEFAULT NULL,
	updatedBy VARCHAR(50) DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;