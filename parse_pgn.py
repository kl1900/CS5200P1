import csv
from pathlib import Path
import argparse
import re
import random

CSV_HEADER = ["GameID", "MoveNumber", "Move", "TimeStamp"]
RE_MOVES = re.compile(r"\d+\.\s*\S+\s*\S*")

def parse_text(text_lines):
    moves = []
    result = None
    for row in text_lines:
        if "Result" in row:
            result = row.split('"')[1]
        if "[" not in row:
            t_moves = RE_MOVES.findall(row)
            for t_move in t_moves:
                moves.extend(t_move.split(".")[1].lstrip().split(" "))
    moves.pop(-1)
    print(moves)
    return result, moves
            
            

def config():
    parser = argparse.ArgumentParser("parse pgn file")
    parser.add_argument("GameID", type=int)
    parser.add_argument("date", type=str, help="in format of YYYY-MM-DD")
    parser.add_argument("file", type=Path, help="file path")
    
    return parser.parse_args()

def main():
    args = config()
    file_p = args.file
    
    with open(file_p, "r", encoding="utf8") as f:
        data = f.readlines()
    
    winner, moves = parse_text(data)
    print(f"result is: {winner}")
    result_csv = f"{file_p.stem}.csv"
    with open(result_csv, "w", encoding="utf8") as f:
        writer = csv.DictWriter(f, fieldnames=CSV_HEADER, lineterminator="\n")
        writer.writeheader()
        counter = 1
        hour = random.randint(8, 16)
        minute = 0
        for move in moves:
            date=args.date
            second = random.randint(0,60)
            if minute >= 60:
                hour += 1
                minute -= 60
            date_time = f"{date} {hour:02d}:{minute:02d}:{second:02d}"
            minute += 1
            writer.writerow({"GameID": args.GameID, "MoveNumber": counter, "Move": move, "TimeStamp": date_time})
            counter += 1
        



if __name__ == "__main__":
    main()
