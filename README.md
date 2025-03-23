# CS5200P1

## important things to notice:

- In PlayerAchievements - achievement date has been changed to make sure it will be found in session
  (make sure to update ur local table to test this)
- get_player_achievement is now designed to retrieve the achievements of the top players (based on their win rate), along with the unlock date for each achievement, linked to the earliest session in which it was unlocked.
- personal profile updation features is available in index page.

Weifan 03/232025

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

If you want to rebuild all containers, run the following command

```
docker-compose down
```

## TODO

- (Done) add instruction for setting up a php server (ideally docker server)
- (check at the end of the MS) Retrieve query results dynamically.
- The web app must include dynamic query integration, allowing users to interact with:
  - (weifan: "Top Players" ) Join Query: Display top players and their unlocked achievements by joining the Players, Achievements and Sessions tables.
    - Filter and sort data interactively.
  - (Kuo: "Player Time") Aggregation Query: Compute average playtime per player and total achievements per game.
    - what does total achievements per game mean? TBD
    - Filter and sort data interactively.
  - (Raagini: "Play Time Per Week") Nested Aggregation with Group-By: Find total playtime per week grouped by player.
    - use player + playergamesession + (session or game) to calculate playtimie
    - | player | 2/1 - 2/7 | 2/8 - 2/14 | 2/15 - 2/21 | 2/22 - 2/28 |
    - Filter and sort data interactively.
  - (Xu Tang: "Top 5" ) Filtering & Ranking Query: Display the top 5 players with the highest scores dynamically.
    - highest score highest win rate (win / (win + losses + draws))
    - Filter and sort data interactively.
  - (Weifan: "Update Player") Update Operation: Allow users to modify player profile details through a web form.
  - (Kuo: "Delete Player") Delete Operation (Cascade on Delete): Ensure deleting a player removes related sessions and achievements automatically.
    - needs to update sql schema for cascade on deletion.
    - deleting a player needs to delete relevant data in PlayerGameSession, games, GameComment, and recalculate win/loss for other players
    - Filter and sort data interactively.

Workflow:
create a branch with your name on it, and do pull requests when you finish
rebase branches when main branch is updated

(Xu Tang, Raagini) We also need to figure out a way for sorting data table

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
