"A quick script to generate files"
import random
import csv
from copy import deepcopy, copy
from datetime import timedelta, datetime
from dataclasses import dataclass, field
from typing import List


GAME_TBL_HEADER = ["GameID", "Game_start", "Game_end", "White", "Black", "ResultID"]
RESULTS = ["1", "2", "3"]
RESULTS_WEIGHT = [0.45, 0.45, 0.10]
real_sessions: List["Session"] = []
def session_id_generator():
    counter = 0
    while True:
        counter += 1
        yield counter

sess_id_gen = session_id_generator()
@dataclass
class Player:
    player_id: str
    sessions: set = field(default_factory=set)
    wins: int = 0
    losses: int = 0
    draws: int = 0
    withdraws: int = 0
    
    def add_session(self, session: "Session"):
        is_new_sess = True
        for i in self.sessions:
            if i == session:
                is_new_sess = False
                return i
        
        if is_new_sess:
            session.session_id = next(sess_id_gen)
            self.sessions.add(session)
            real_sessions.append(session)
            return session
    
    def win(self):
        self.wins += 1
        
    def lose(self):
        self.losses += 1
    
    def draw(self):
        self.draws += 1
 
players = [Player(i) for i in range(1,21)]

   
@dataclass
class Session:
    start: datetime
    end: datetime
    session_id: str = None
    player: Player = None
    
    def __post_init__(self):
        self._blocks = []
        # Compute the start of the chosen block
        _start = deepcopy(self.start)
        _end = deepcopy(self.end)
        while _start + timedelta(minutes=30) <= _end:
            block_end = _start + timedelta(minutes=30)
            self._blocks.append((_start, block_end))
            _start = block_end
    
    def __hash__(self):
        return hash(str(self.start) + str(self.end))
    
    def __eq__(self, value: "Session"):
        return self.start == value.start and self.end == value.end


    def give_a_block(self):
        b = random.choice(self._blocks)
        self._blocks.remove(b)
        return b



@dataclass
class Game:
    game_id: str
    session: Session
    white: Player = None
    black: Player = None
    start: datetime = None
    end: datetime = None
    result: str = None
    
    def __post_init__(self):
        self.start, self.end = self.session.give_a_block()
        
        
        # select 2 players
        _players = copy(players)
        self.white = random.choice(_players)
        _players.remove(self.white)
        self.black = random.choice(_players)
        
        # add session to the players
        new_sess = deepcopy(self.session)
        new_sess.player = self.white
        self.white_session = self.white.add_session(new_sess)

        
        new_sess = deepcopy(self.session)
        new_sess.player = self.black
        self.black_session = self.black.add_session(new_sess)
        
        # select result
        self.result = random.choices(population=RESULTS, weights=RESULTS_WEIGHT)[0]

        if self.result == "1":
            self.white.win()
            self.black.lose()
        
        elif self.result == "2":
            self.white.lose()
            self.black.win()
        
        else:
            self.white.draw()
            self.black.draw()


def random_16hour_blocks(start_datetime, end_datetime, num_blocks=1):
    # Ensure start_datetime and end_datetime are datetime objects
    if isinstance(start_datetime, str):
        start_datetime = datetime.fromisoformat(start_datetime)
    if isinstance(end_datetime, str):
        end_datetime = datetime.fromisoformat(end_datetime)

    # Generate a random date between start and end
    total_days = (end_datetime.date() - start_datetime.date()).days
    random_day_offset = random.randint(0, total_days)
    random_date = start_datetime + timedelta(days=random_day_offset)

    # Set possible time range within the selected date
    day_start = datetime(random_date.year, random_date.month, random_date.day, 0, 0)
    day_end = datetime(random_date.year, random_date.month, random_date.day, 23, 0)

    blocks = []
    for _ in range(num_blocks):
        # Ensure there is enough room for a 16-hour block
        if (day_end - day_start).total_seconds() < 16 * 3600:
            raise ValueError("Not enough time in the day for a 16-hour block.")

        # Pick a random start hour
        max_start_hour = 8  # Latest possible start time to fit a 16-hour block
        random_start_hour = random.randint(0, max_start_hour)
        block_start = datetime(random_date.year, random_date.month, random_date.day, random_start_hour, 0)
        block_end = block_start + timedelta(hours=16)

        blocks.append((block_start, block_end))

    return blocks

# Example usage
start_dt = datetime(2025, 2, 1, 0, 0)
end_dt = datetime(2025, 2, 28, 23, 59)

random_blocks = random_16hour_blocks(start_dt, end_dt, num_blocks=20)

sessions_templates = []
for start, end in random_blocks:
    sessions_templates.append(Session(start, end))

games:List[Game] = []
for i in range(1, 301):
    sess = random.choice(sessions_templates)
    games.append(Game(str(i), sess))




# write files =========================================================
with open("players.csv", "w", encoding="utf8") as f:
    writer = csv.DictWriter(f, fieldnames=["ID", "wins", "losses", "draws"], lineterminator="\n")
    writer.writeheader()
    players.sort(key=lambda x: int(x.player_id))
    for p in players:
        writer.writerow({"ID": p.player_id, "wins": p.wins, "losses": p.losses, "draws": p.draws})

with open("playergamesession.csv", "w", encoding="utf8") as f:
    writer = csv.DictWriter(f, fieldnames=["PlayerID", "GameID", "SessionID"], lineterminator="\n")
    writer.writeheader()
    games.sort(key=lambda x: int(x.game_id))
    for g in games:
        writer.writerow({"PlayerID": g.white.player_id, "GameID": g.game_id, "SessionID": g.white_session.session_id})
        writer.writerow({"PlayerID": g.black.player_id, "GameID": g.game_id, "SessionID": g.black_session.session_id})

with open("game.csv", "w", encoding="utf8") as f:
    writer = csv.DictWriter(f, fieldnames=["GameID", "Game_start", "Game_end", "White", "Black", "ResultID"], lineterminator="\n")
    writer.writeheader()
    games.sort(key=lambda x: int(x.game_id))
    for g in games:
        writer.writerow({"GameID": g.game_id, 
                         "Game_start": g.start, 
                         "Game_end": g.end,
                         "White": g.white.player_id,
                         "Black": g.black.player_id,
                         "ResultID": g.result
                         })

with open("session.csv", "w", encoding="utf8") as f:
    writer = csv.DictWriter(f, fieldnames=["SessionID", "Session_start", "Session_end"], lineterminator="\n")
    writer.writeheader()
    real_sessions.sort(key=lambda x: int(x.session_id))
    for s in real_sessions:
        writer.writerow({"SessionID": s.session_id, 
                         "Session_start": s.start, 
                         "Session_end": s.end,
                         })
