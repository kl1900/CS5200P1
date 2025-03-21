<?php include "./includes/header.php"?>

<h2>Applications Tabs</h2>

<div class="tabs">
    <button class="tab-button" onclick="loadTab('features/get_top_players.php', this)">Top Players</button>
    <button class="tab-button" onclick="loadTab('features/something.php', this)">some other feature</button>
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

window.onload = function () {
    document.querySelector('.tab-button').click();
};
</script>

<?php include "./includes/footer.php" ?>
