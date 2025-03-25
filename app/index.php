<?php include "./includes/header.php" ?>

<h2>Applications Tabs</h2>

<div class="tabs">
    <button class="tab-button" onclick="loadTab('dashboard/dashboard.php', this)">Dashboard</button>
    <button class="tab-button" onclick="loadTab('features/get_players.php', this)">Top Players</button>
    <button class="tab-button" onclick="loadTab('features/get_player_achievement.php', this)">Get Player
        Achievement</button>
    <button class="tab-button" onclick="loadTab('features/get_players_stat.php', this)">Players Statistics</button>
    <button class="tab-button" onclick="loadTab('features/top5.php', this)">Top 5</button>
    <button class="tab-button" onclick="loadTab('features/playtime_per_week.php', this)">Play Time Per Week</button>
    <button class="tab-button" onclick="loadTab('features/stored_procedures.php', this)">Stored Procedures</button>
</div>


<div id="content">
    <p>Select a feature tab above to load PHP content.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.activeCharts = []; // global store for active Chart.js instances

    function loadTab(url, btn) {
        // Reset active button styling
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Load tab content
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const container = document.getElementById('content');
                container.innerHTML = html;

                // Destroy any previously active charts
                if (window.activeCharts.length > 0) {
                    window.activeCharts.forEach(c => {
                        if (c && typeof c.destroy === 'function') c.destroy();
                    });
                    window.activeCharts = [];
                }

                // Re-execute inline <script> tags
                const scripts = container.querySelectorAll("script");
                scripts.forEach(script => {
                    const newScript = document.createElement("script");
                    if (script.src) {
                        newScript.src = script.src;
                    } else {
                        newScript.textContent = script.textContent;
                    }
                    document.body.appendChild(newScript);
                });
            })
            .catch(err => {
                document.getElementById('content').innerHTML = `<p style="color:red;">Failed to load ${url}</p>`;
                console.error(err);
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

    function fetchProcedureResult(sourceId) {
        const procedureText = document.getElementById(sourceId).value;

        if (!procedureText.trim()) {
            document.getElementById('procedureResults').innerHTML = '<p>Please select a stored procedure from the dropdown above.</p>';
            return;
        }

        fetch('features/get_stored_procedure_result.php?procedureText=' + encodeURIComponent(procedureText))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }
                return response.text();
            })
            .then(html => {
                document.getElementById('procedureResults').innerHTML = html;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('procedureResults').innerHTML = '<p style="color:red;">Failed to load player data.</p>';
            });
    }

    function procedureExecuteFunc() {
        fetchProcedureResult('sql_query');
    }

    function procedureDropDownFunc() {
        fetchProcedureResult('procedureDropDown');
    }

    function procedureStoreFunc() {
        const procedureText = document.getElementById('sql_query').value.trim();

        fetch('features/store_procedure_text.php?procedureText=' + encodeURIComponent(procedureText))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not OK');
                }
                return response.text();
            }).then(html => {
                document.getElementById('procedureResults').innerHTML = html;
                
                // update dropdown menu
                procedureDropDownFunc();
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('procedureResults').innerHTML = '<p style="color:red;">Failed to load player data.</p>';
            });
    }

    window.onload = function () {
        document.querySelector('.tab-button').click();
    };

    let currentSort = null;
    let currentOrder = 'asc';

    // document.addEventListener("click", function (e) {
    //     if (e.target.classList.contains("sort-header")) {
    //         const clickedSort = e.target.dataset.sort;

    //         // Toggle order if clicking the same column, otherwise reset to ascending
    //         if (clickedSort === currentSort) {
    //             currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    //         } else {
    //             currentSort = clickedSort;
    //             currentOrder = 'asc';
    //         }

    //         fetch(`features/playtime_per_week.php?sort=${currentSort}&order=${currentOrder}`)
    //             .then(res => res.text())
    //             .then(html => {
    //                 document.getElementById('content').innerHTML = html;
    //             });
    //     }
    // });
    document.addEventListener("click", function (e) {
        e.preventDefault();
        if (e.target.classList.contains("sort-header")) {
            const clickedSort = e.target.dataset.sort;
            
            // Toggle order if clicking the same column, otherwise reset to ascending
            if (clickedSort === currentSort) {
                currentOrder = currentOrder === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = clickedSort;
                currentOrder = 'asc';
            }
            
            // Get the active tab by finding which tab button has the 'active' class
            const activeTab = document.querySelector('.tab-button.active');
            let tabUrl = 'features/playtime_per_week.php'; // Default as fallback
            
            if (activeTab) {
                // Extract the URL from the active tab's onclick attribute
                const onclickAttr = activeTab.getAttribute('onclick');
                if (onclickAttr) {
                    const match = onclickAttr.match(/loadTab\('([^']+)'/);
                    if (match && match[1]) {
                        tabUrl = match[1];
                    }
                }
            }
            
            fetch(`${tabUrl}?sort=${currentSort}&order=${currentOrder}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                });
        }
});
</script>

</script>
<?php include "./includes/footer.php" ?>