USE Practicum1;

CREATE TABLE Player (
    PlayerID INT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL,
    registration_date DATE NOT NULL,
    email_address VARCHAR(100) UNIQUE NOT NULL,
    Wins INT NOT NULL DEFAULT 0,
    Losses INT NOT NULL DEFAULT 0,
    Draws INT NOT NULL DEFAULT 0,
    Withdraw INT NOT NULL DEFAULT 0
);

CREATE TABLE PlayerRank (
    RankID INT PRIMARY KEY,
    RankName VARCHAR(50) NOT NULL
);

CREATE TABLE RankDecider (
    Wins INT NOT NULL,
    Losses INT NOT NULL,
    RankID INT NOT NULL,
    PRIMARY KEY (Wins, Losses),
    FOREIGN KEY (RankID) REFERENCES PlayerRank(RankID)
);

CREATE TABLE BanStatus (
    Ban_status_ID INT PRIMARY KEY,
    description VARCHAR(100) NOT NULL
);

CREATE TABLE BanDecider (
    Wins INT NOT NULL,
    Losses INT NOT NULL,
    Draws INT NOT NULL,
    Withdraws INT NOT NULL,
    Ban_status_ID INT NOT NULL,
    PRIMARY KEY (Wins, Losses, Draws),
    FOREIGN KEY (Ban_status_ID) REFERENCES BanStatus(Ban_status_ID)
);

CREATE TABLE PaidPlayer (
    PlayerID INT PRIMARY KEY,
    Show_statistics BOOLEAN NOT NULL,
    Show_features BOOLEAN NOT NULL,
    HasPaidPlayerBadge BOOLEAN NOT NULL,
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE
);

CREATE TABLE Session (
    SessionID INT PRIMARY KEY,
    Session_start DATETIME NOT NULL,
    Session_end DATETIME
);

CREATE TABLE Result (
    ResultID INT PRIMARY KEY,
    result_description VARCHAR(50) NOT NULL
);

CREATE TABLE Game (
    GameID INT PRIMARY KEY,
    Game_start DATETIME NOT NULL,
    Game_end DATETIME,
    White INT NOT NULL,
    Black INT NOT NULL,
    ResultID INT NOT NULL,
    FOREIGN KEY (White) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (Black) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (ResultID) REFERENCES Result(ResultID)
);

CREATE TABLE PlayerGameSession (
    PlayerID INT NOT NULL,
    GameID INT NOT NULL,
    SessionID INT NOT NULL,
    PRIMARY KEY (PlayerID, GameID, SessionID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE,
    FOREIGN KEY (SessionID) REFERENCES Session(SessionID) ON DELETE CASCADE
);

CREATE TABLE GameComment (
    CommentID INT PRIMARY KEY,
    GameID INT NOT NULL,
    PlayerID INT NOT NULL,
    Comment TEXT,
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE,
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE
);

CREATE TABLE Action (
    GameID INT NOT NULL,
    MoveNumber INT NOT NULL,
    Move VARCHAR(50) NOT NULL,
    time_stamp DATETIME NOT NULL,
    PRIMARY KEY (GameID, MoveNumber),
    FOREIGN KEY (GameID) REFERENCES Game(GameID) ON DELETE CASCADE
);

CREATE TABLE Achievements (
    AchievementID INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    requirement VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE PlayerAchievement (
    PlayerID INT NOT NULL,
    AchievementID INT NOT NULL,
    achievement_datetime DATETIME NOT NULL,
    PRIMARY KEY (PlayerID, AchievementID),
    FOREIGN KEY (PlayerID) REFERENCES Player(PlayerID) ON DELETE CASCADE,
    FOREIGN KEY (AchievementID) REFERENCES Achievements(AchievementID) ON DELETE CASCADE
);
