<?php include "./includes/header.php" ?>

<h2>Check Game Dashboard</h2>

<div class="tabs">
    <button class="tab-button" onclick="loadTab('dashboard/dashboard.php', this)">Dashboard</button>
    <button class="tab-button" onclick="loadTab('features/all_players.php', this)">All Players</button>
    <button class="tab-button" onclick="loadTab('features/get_player_achievement.php', this)">Player Achievements</button>
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
    // Global variables to track current state
    let currentSort = null;
    let currentOrder = 'asc';
    let currentFilters = {};

    function refreshDropdown() {
        fetch("features/get_stored_procedures_options.php")
            .then(response => response.text())
            .then(optionHTML => {
                const dropdown = document.getElementById("procedureDropDown");
                dropdown.innerHTML = '<option value="">-- Select a procedure --</option>' + optionHTML;
            })
            .catch(error => {
                console.error("Failed to refresh dropdown:", error);
            });
    }

    function loadTab(url, btn) {
        // Reset active button styling
        document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        // Reset filters when switching tabs
        currentFilters = {};

        // Fetch and load the PHP content
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

// New function to handle filters
window.applyFilters = function(filterData) {
    // Log what we're receiving 
    console.log('Parent applyFilters called with:', filterData);
    
    // Store the filter values
    currentFilters = filterData;
    
    // Get the active tab URL
    const activeTab = document.querySelector('.tab-button.active');
    let tabUrl = '';
    
    if (activeTab) {
        console.log('Active tab found:', activeTab);
        const onclickAttr = activeTab.getAttribute('onclick');
        console.log('onclick attribute:', onclickAttr);
        
        if (onclickAttr) {
            const match = onclickAttr.match(/loadTab\('([^']+)'/);
            console.log('match result:', match);
            
            if (match && match[1]) {
                tabUrl = match[1].split('?')[0]; // Get base URL without parameters
                console.log('Extracted URL:', tabUrl);
            }
        }
    } else {
        console.error('No active tab found');
    }
    
    if (!tabUrl) {
        console.error('Could not determine active tab URL');
        return;
    }
    
    // Build the complete URL with filter and sort parameters
    let url = tabUrl + '?';
    let params = [];
    
    // Add filter parameters
    for (const key in filterData) {
        if (filterData[key]) {
            params.push(encodeURIComponent(key) + '=' + encodeURIComponent(filterData[key]));
        }
    }
    
    // Add sort parameters if they exist
    if (currentSort) {
        params.push('sort=' + encodeURIComponent(currentSort));
        params.push('order=' + encodeURIComponent(currentOrder));
    }
    
    url += params.join('&');
    console.log('Final URL:', url);
    
    // Fetch and load the filtered content
    fetch(url)
        .then(response => {
            console.log('Response status:', response.status);
            return response.text();
        })
        .then(html => {
            console.log('Received HTML length:', html.length);
            document.getElementById('content').innerHTML = html;
            console.log('Content updated');
        })
        .catch(err => {
            console.error('Error loading filtered content:', err);
            document.getElementById('content').innerHTML = `<p style="color:red;">Failed to load filtered content: ${err.message}</p>`;
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
            
            // Build the complete URL with both sort and filter parameters
            let url = `${tabUrl}?sort=${currentSort}&order=${currentOrder}`;
            
            // Add any existing filter parameters
            for (const key in currentFilters) {
                if (currentFilters[key]) {
                    url += `&${encodeURIComponent(key)}=${encodeURIComponent(currentFilters[key])}`;
                }
            }
            
            fetch(url)
                
        }
});
</script>

</script>
<?php include "./includes/footer.php" ?>