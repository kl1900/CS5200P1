USE Practicum1;

LOAD DATA INFILE '/var/lib/mysql-files/data/PlayerRank.csv' 
INTO TABLE PlayerRank 
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
