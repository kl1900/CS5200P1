# CS5200P1

a good place to find pgn files is here:https://www.chessgames.com/perl/chess.pl?page=44&pid=130615 
select a game and download pgn of the game

you can use python to parse the pgn file into the format for this project
```
python3 parse_pgn.py <game_id> <date> <file_name>.pgn
```
e.g.
```
python3 parse_pgn.py 1 2025-12-12 keymer_wei_yi_2025.pgn
```
generates a csv file with the p

## running mysql container

**change your local folder accordingly**
```
docker run --name mysql-container --network cs5200-network \
  -e MYSQL_ROOT_PASSWORD="5800" \
  -v ~/neu/5800/practicum_1/local/mysql:/var/lib/mysql \
  -v ~/neu/5800/practicum_1/CS5800P1:/var/lib/mysql-files/ \
  -p 3306:3306 \
  -d mysql:latest
```

starting myphpadmin
```
docker run --name phpmyadmin-container --network cs5200-network \
--link mysql-container:db \
-p 8080:80 \
-v ~/neu/5800/practicum_1/local/phpmyadmin:/sessions \
-d phpmyadmin/phpmyadmin
```

you can attach to the container via `docker exec -it mysql-container /bin/bash`

run the following command to create database and tables
```
cd /var/lib/mysql-files/
mysql -u root -p < create_tables.sql
```

run the following command to add data from csv into database
```
mysql -u root -p < insert_csv.sql
```

## TODO
 - make sure everyone can run docker and have this setup
 - maybe fix the following data tables
    - (Raagini) table Player: use fake email addresses xxxx@example.com
    - (Weifan) table BanDecider: ?
    - (Xu Tang) table Session: add sessions
    - (Kuo) table Game: make sure each game belongs to a session, and fix results to match Results table
    - (Raagini) table Achievements: add up to 20 achievements
    - (Kuo) table PlayerGameSession: make realistic data so that Game are in a session of a player
 - download csvs
 - (Kuo) edit insert_csv.sql for each table
