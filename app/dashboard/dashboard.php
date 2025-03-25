<h2 style="text-align: center;">Game Analytics Dashboard </h2>

<!-- Layout container -->
<div class="dashboard-grid">

    <!-- Card 1: Top 5 Players -->
    <div class="dashboard-card">
        <h3>Top 5 Players By Win Ratio</h3>
        <canvas id="top5Chart"></canvas>
    </div>

    <!-- Card 2: Weekly Playtime -->
    <div class="dashboard-card">
        <h3>Top 5 Players by Longest Total PlayTime</h3>
        <canvas id="playtimeChart"></canvas>
    </div>

    <!-- Card 3: Win/Loss/Draw -->
    <div class="dashboard-card">
        <h3>Top 5 Players By Win Rate – Win / Loss / Draw Stats</h3>
        <canvas id="wldChart"></canvas>
    </div>

    <!-- Card 4: Achievement Stats -->
    <div class="dashboard-card">
        <h3>Top 5 Players by Most Unlocked Achievements</h3>
        <canvas id="achievementChart"></canvas>
    </div>

    <!-- Card 5: Paid Players -->
    <div class="dashboard-card">
        <h3>Paid vs Unpaid Players</h3>
        <canvas id="paidStatusChart"></canvas>
    </div>

    <!-- Card 6: Player Registration Trend -->
    <div class="dashboard-card">
        <h3>Player Registration Trend</h3>
        <canvas id="registrationChart"></canvas>
    </div>


</div>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
        gap: 28px;
        padding: 40px 60px;
        max-width: 1700px;
        margin: 0 auto 60px;
    }

    .dashboard-card {
        background: white;
        border-radius: 20px;
        padding: 30px 24px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.08);
    }

    .dashboard-card h3 {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: #2c3e50;
        font-weight: 600;
        border-left: 5px solid #3498db;
        padding-left: 12px;
        width: 100%;
        text-align: left;
    }

    canvas {
        width: 100%;
        height: auto;
    }

    @media (max-width: 768px) {
        .dashboard-card {
            padding: 20px;
        }

        .dashboard-card h3 {
            font-size: 1rem;
        }
    }
</style>


<script src="dashboard/dashboard-chart.js"></script>