# CS5200P1

CS5200 Practicum Done by Group 5, including:
  - Kuo Lu
  - Weifan Li
  - Raagini Tyagi
  - Xu Tang

PDF Attachment is [here](./Analytical%20queries%20and%20explainations.pdf)

## Starting the application

**This env uses docker-compose, please install docker before starting following [this guide](https://www.docker.com/get-started/)**


**The following commands assumes you use a POSIX shell**

1. Using git to pull this project
```
git clone git@github.com:kl1900/CS5200P1.git
```
2. create 2 directories in this repo and grant proper permissions

```
mkdir -p local/mysql
mkdir -p local/phpadmin
sudo chmod 777 local/phpadmin
```
3. run the following command to start containers

```
docker-compose build
docker-compose up -d
```

  - There are 2 apps running to access mysql:
    - to examining database with convenience, the phpadmin app can be accessed via http://localhost:8081
      - user: `student`
      - password: `student`
    - the project app is accessed via http://localhost:8080

4. run the following command to populate data in the database

```
docker exec mysql-db /bin/bash /var/lib/mysql-files/populate_db.sh
```

5. If you want to stop and remove all containers, run the following command

```
docker-compose down
```

## Other utilities
To generate random game data, run this
```
python3 utilities/game_generator.py
python3 utilities/action_generator.py
```