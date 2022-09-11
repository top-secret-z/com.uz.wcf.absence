-- add columns in user table
ALTER TABLE wcf1_user ADD absentFrom INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_user ADD absentTo INT(10) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_user ADD absentReason VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE wcf1_user ADD absentAuto TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE wcf1_user ADD absentRepID INT(10) DEFAULT NULL;

ALTER TABLE wcf1_user ADD FOREIGN KEY (absentRepID) REFERENCES wcf1_user (userID) ON DELETE SET NULL;

-- plugin
DROP TABLE IF EXISTS wcf1_stat_absence;
CREATE TABLE wcf1_stat_absence (
	statID			INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	count			INT(10),
	time			INT(10)
);
