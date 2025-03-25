// Destroy all existing charts if needed
if (!window.activeCharts) window.activeCharts = [];
window.activeCharts.forEach(chart => chart.destroy());
window.activeCharts = [];

// Chart 1: Top 5 Players
fetch("dashboard/top5_data.php")
  .then(res => res.json())
  .then(data => {
    const ctx = document.getElementById("top5Chart").getContext("2d");

    const chart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.map(d => d.playerName),
        datasets: [{
          label: "Win Ratio (%)",
          data: data.map(d => d.winRatio),
          backgroundColor: "rgba(0, 250, 250, 0.7)"
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Top 5 Players by Win Ratio"
          }
        },
        scales: {
          x: {
            beginAtZero: true,

          }
        }
      }
    });

    if (!window.activeCharts) window.activeCharts = [];
    window.activeCharts.push(chart);
  })
  .catch(err => console.error("Failed to load chart:", err));


// Chart 2: Weekly Playtime
fetch("dashboard/total_play_time_data.php")
  .then(res => res.json())
  .then(data => {
    const ctx = document.getElementById("playtimeChart").getContext("2d");
    const chart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.map(d => d.Username),
        datasets: [{
          label: "Total Playtime (min)",
          data: data.map(d => d.Minutes),
          borderColor: "blue",
          backgroundColor: "rgba(250, 0, 208, 0.7)",
          fill: false
        }]
      }
    });
    window.activeCharts.push(chart);
  });

// Chart 3: Win / Loss / Draw
fetch("dashboard/get_player_data.php")
  .then(res => res.json())
  .then(data => {
    const labels = data.map(d => d.Username);
    const wins = data.map(d => d.Wins);
    const losses = data.map(d => d.Losses);
    const draws = data.map(d => d.Draws);

    const ctx = document.getElementById("wldChart").getContext("2d");
    const chart = new Chart(ctx, {
      type: "bar",
      data: {
        labels,
        datasets: [
          { label: "Wins", data: wins, backgroundColor: "green" },
          { label: "Losses", data: losses, backgroundColor: "red" },
          { label: "Draws", data: draws, backgroundColor: "orange" }
        ]
      },
      options: {
        scales: {
          x: { stacked: true },
          y: { stacked: true, beginAtZero: true }
        }
      }
    });

    window.activeCharts.push(chart);
  })
  .catch(err => console.error("Failed to load WLD chart:", err));


// Chart 4: Achievement Summary 
fetch("dashboard/player_stat_data.php")
  .then(res => res.json())
  .then(data => {
    console.log("Achievement summary data:", data);
    const ctx = document.getElementById("achievementChart").getContext("2d");
    const chart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: data.map(d => d.Username),
        datasets: [{
          label: "Achievements Unlocked",
          data: data.map(d => d.Achievements),
          backgroundColor: "rgba(154, 0, 250, 0.7)"
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Top 5 Players by Achievements"
          }
        },
        scales: {
          x: {
            beginAtZero: true,
          }
        }
      }
    });

    window.activeCharts.push(chart);
  })
  .catch(err => console.error("Failed to load chart:", err));


// Chart 5: Paid vs Unpaid Players
fetch("dashboard/get_paid_status_summary.php")
  .then(res => res.json())
  .then(data => {
    const ctx = document.getElementById("paidStatusChart").getContext("2d");

    const chart = new Chart(ctx, {
      type: "doughnut",
      data: {
        labels: data.map(d => d.status),
        datasets: [{
          data: data.map(d => d.count),
          backgroundColor: ["green", "gray"]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Paid vs Unpaid Players"
          }
        }
      }
    });

    window.activeCharts.push(chart);
  })
  .catch(err => console.error("Failed to load paid status chart:", err));

  // Chart 6: Player Registration Trend
fetch("dashboard/player_registration_trend.php")
  .then(res => res.json())
  .then(data => {
    const ctx = document.getElementById("registrationChart").getContext("2d");
    const chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: data.map(d => d.month),
        datasets: [{
          label: "New Registrations",
          data: data.map(d => d.count),
          borderColor: "purple",
          fill: false
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Player Registration Trend"
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    window.activeCharts.push(chart);
  });





