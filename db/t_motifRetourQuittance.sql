CREATE TABLE IF NOT EXISTS t_motifretourquittance (
	id INT(11) NOT NULL AUTO_INCREMENT,
	code INT(12) DEFAULT NULL,
	libelle VARCHAR(255) DEFAULT NULL,
	created DATETIME DEFAULT NULL,
	createdBy VARCHAR(50) DEFAULT NULL,
	updated DATETIME DEFAULT NULL,
	updatedBy VARCHAR(50) DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;