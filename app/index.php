<?php include "./includes/header.php" ?>

<h2>Applications Tabs</h2>

<div class="tabs">
    <button class="tab-button" onclick="loadTab('features/get_players.php', this)">Top Players</button>
    <button class="tab-button" onclick="loadTab('features/get_player_achievement.php', this)">Get Player
        Achievement</button>
    <button class="tab-button" onclick="loadTab('features/something.php', this)">some other feature</button>
    <button class="tab-button" onclick="loadTab('features/top5.php', this)">Top 5</button>
</div>


<div id="content">
    <p>Select a feature tab above to load PHP content.</p>
</div>

<script>
    function loadTab(url, btn) {
        // Mark the active tab
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Fetch and load the PHP content
        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById('content').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('content').innerHTML = `<p style="color:red;">Failed to load ${url}</p>`;
            });
    }

    function filterByPlayer() {
        const playerId = document.getElementById('playerDropdown').value;

        if (playerId === '') {
            document.getElementById('playerResults').innerHTML = '<p>Please select a player from the dropdown above.</p>';
            return;
        }

        fetch('features/get_players_achievement_results.php?playerId=' + playerId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('playerResults').innerHTML = html;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('playerResults').innerHTML = '<p style="color:red;">Failed to load player data.</p>';
            });
    }

    window.onload = function () {
        document.querySelector('.tab-button').click();
    };
</script>

<?php include "./includes/footer.php" ?>