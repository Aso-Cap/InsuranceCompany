CREATE TABLE IF NOT EXISTS t_client (
	id INT(11) NOT NULL AUTO_INCREMENT,
	codeClient INT(12) DEFAULT NULL,
	typeClient VARCHAR(50) DEFAULT NULL,
	civilite VARCHAR(50) DEFAULT NULL,
	nom VARCHAR(255) DEFAULT NULL,
	adresse VARCHAR(255) DEFAULT NULL,
	rue VARCHAR(100) DEFAULT NULL,
	ville VARCHAR(50) DEFAULT NULL,
	activite VARCHAR(100) DEFAULT NULL,
	email VARCHAR(50) DEFAULT NULL,
	debit DECIMAL(12,2) DEFAULT NULL,
	credit DECIMAL(12,2) DEFAULT NULL,
	tel1 VARCHAR(50) DEFAULT NULL,
	fax VARCHAR(50) DEFAULT NULL,
	permis VARCHAR(50) DEFAULT NULL,
	datePermis DATE DEFAULT NULL,
	tel2 VARCHAR(50) DEFAULT NULL,
	codeRegion INT(12) DEFAULT NULL,
	codeCommercial INT(12) DEFAULT NULL,
	situationFamiliale VARCHAR(50) DEFAULT NULL,
	cin VARCHAR(50) DEFAULT NULL,
	dateNaissance DATE DEFAULT NULL,
	solvabilite INT(12) DEFAULT NULL,
	nombreIncident INT(12) DEFAULT NULL,
	created DATETIME DEFAULT NULL,
	createdBy VARCHAR(50) DEFAULT NULL,
	updated DATETIME DEFAULT NULL,
	updatedBy VARCHAR(50) DEFAULT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;