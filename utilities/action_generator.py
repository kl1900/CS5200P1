import random
from datetime import datetime, timedelta
import csv

start_dt = datetime(2025, 2, 1, 0, 0)
end_dt = datetime(2025, 2, 28, 23, 59)

actions = ['d4', 'Nf6', 'c4', 'e6', 'Nc3', 'Bb4', 'Qc2', 'O-O', 'e4', 'd5', 'e5', 'Ne4', 'a3', 'Bxc3+', 'bxc3', 'c5', 'Bd3', 'cxd4', 'cxd4', 'Nc6', 'Ne2', 'Qa5+', 'Kf1', 'f6', 'Bxe4', 'dxe4', 'exf6', 'Rxf6', 'Be3', 'Bd7', 'Kg1', 'Raf8', 'h4', 'Ne7', 'Qxe4', 'Nf5', 'Bg5', 'Nd6', 'Qd3', 'Rxf2', 'Be7', 'Bc6', 'Rh2', 'Qf5', 'Qxf5', 'R8xf5', 'd5', 'exd5', 'Ng3', 'Nxc4', 'Bc5', 'R2f4', 'Nxf5', 'Rxf5', 'Bxa7', 'b6', 'Rh3', 'Rf7', 'Bb8', 'd4', 'Rc1', 'b5', 'a4', 'Rd7', 'axb5', 'Bxb5', 'Rb3', 'Rd5', 'Bf4', 'Ne5', 'Rc8+', 'Kf7', 'Rc7+', 'Kf6', 'Rb7', 'Bd7', 'Rg3', 'Ng4', 'Bg5+', 'Kf5', 'Rf3+', 'Kg6', 'Rb6+', 'Kh5', 'Rf7', 'Ra5', 'Rd6', 'Bf5', 'Bc1', 'd3', 'Rxg7', 'Kxh4', 'Rf7', 'Be4', 'Rd4', 'Bf5', 'Rf4', 'd2', 'Bxd2', 'Ra1+', 'Rf1', 'Rxf1+', 'Kxf1', 'Bd3+', 'Kg1', 'Be4', 'Ra7', 'Bc2', 'Ra5', 'e4', 'e5', 'Nf3', 'Nc6', 'Nc3', 'Nf6', 'Bb5', 'Bb4', 'O-O', 'O-O', 'd3', 'd6', 'Bg5', 'Bxc3', 'bxc3', 'Qe7', 'Re1', 'Nd8', 'd4', 'Ne6', 'Bc1', 'c6', 'Bf1', 'Qc7', 'Nh4', 'Rd8', 'Nf5', 'g6', 'Nh6+', 'Kg7', 'g3', 'd5', 'exd5', 'Nxd5', 'dxe5', 'Qe7', 'Qf3', 'f5', 'exf6+', 'Qxf6', 'Qxf6+', 'c4', 'Re8', 'Bb2', 'Nc7', 'h3', 'g5', 'Ng4', 'Bxg4', 'hxg4', 'Bd3', 'Ne6', 'Kg2', 'Nc5', 'Bf5', 'Na4', 'Bxf6', 'Kxf6', 'Rh1', 'Rxh7', 'Nc5', 'c3', 'Rh8', 'Re1+', 'Kf6', 'Rhe7', 'Rad8', 'R7e5', 'b6', 'Bb1', 'Rh6', 'Rf5+', 'Kg7', 'Rxg5+', 'Kh8', 'Rh5', 'gxh5', 'Kg7', 'g4', 'Kf6', 'f4', 'Rd6', 'g5+', 'Kf7', 'h6']

def random_datetime(start_time, end_time, n):
    """Generate a random datetime between two datetime objects."""
    # Ensure start_time and end_time are datetime objects
    if isinstance(start_time, str):
        start_time = datetime.fromisoformat(start_time)
    if isinstance(end_time, str):
        end_time = datetime.fromisoformat(end_time)

    # Get total seconds between start and end times
    delta_seconds = (end_time - start_time).total_seconds()

    # Generate a random number of seconds within the range
    random_seconds = []
    for _ in range(n):
        random_seconds.append(random.uniform(0, delta_seconds))
    
    random_seconds.sort()


    return [start_time + timedelta(seconds=i) for i in random_seconds]



with open("action.csv", "w") as f:
    writer = csv.DictWriter(f, fieldnames=["GameID", "MoveNumber", "Move", "TimeStamp"])
    writer.writeheader()
    for i in range(300):
        game_id = i
        num_of_moves = random.randint(10,20)
        times_stamp = random_datetime(start_dt, end_dt, num_of_moves)
        for m in range(num_of_moves):
            move = random.choice(actions)
            writer.writerow({
                "GameID": game_id + 1,
                "MoveNumber": m + 1,
                "Move": move,
                "TimeStamp": f"""{times_stamp[m].strftime('%Y-%m-%d %H:%M:%S')}"""
            })
