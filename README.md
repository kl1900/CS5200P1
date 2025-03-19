# CS5200P1

## Setup environment
**This env uses docker-compose, please install docker before starting**

create 2 directories in this repo and grant proper permissions
```
mkdir -p local/mysql
mkdir -p local/phpadmin
sudo chmod 777 local/phpadmin
```
run the following command to start containers
```
docker-compose build
docker-compose up -d
```
There are 2 apps running to access mysql:
  - the phpadmin app is accessed via http://localhost:8081
    - user: `student`
    - password: `student`
  - the project app is accessed via http://localhost:8080

To populate data in the database, run the following command
```
docker exec mysql-db /bin/bash /var/lib/mysql-files/populate_db.sh
```

## TODO
 - (Done) add instruction for setting up a php server (ideally docker server)
 - Retrieve query results dynamically.
 - Filter and sort data interactively.
 - Update player profiles and other relevant data.
 - Delete records with cascade operations to maintain referential integrity.
 - The web app must include dynamic query integration, allowing users to interact with:
    - Join Query: Display top players and their unlocked achievements by joining the Players, Achievements and Sessions tables.
    - Aggregation Query: Compute average playtime per player and total achievements per game.
    - Nested Aggregation with Group-By: Find total playtime per week grouped by player.
    - Filtering & Ranking Query: Display the top 5 players with the highest scores dynamically.
    - Update Operation: Allow users to modify player profile details through a web form.
    - Delete Operation (Cascade on Delete): Ensure deleting a player removes related sessions and achievements automatically.


# OLD STUFF
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
