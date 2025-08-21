<h1 style="padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--primary);">2024-25 Players</h1>
<?php

include __DIR__ . '/../../private_html/db-connection.php';

// ...existing code...

// Get total count
$countSql = "SELECT COUNT(*) as cnt FROM players";
$countResult = $conn->query($countSql);
$totalPlayers = ($countResult && $countResult->num_rows > 0) ? intval($countResult->fetch_assoc()['cnt']) : 0;
// ...existing code...

$sql =
    "SELECT
                p.playerId AS `Player ID`,
                p.fullName AS `Player`,
                p.teamId AS `Team ID`,
                t.teamAbr AS 'Team Abr',
                t.name AS `Team`,
                CONCAT(position1, IF(position2 IS NOT NULL AND position2 != '', CONCAT('/', position2), '')) AS `Position`,
                height AS `Ht`,
                weight AS `Wt`,
                (CASE
                    WHEN draftPick is NULL OR draftPick = '' THEN 'Undrafted'
                    WHEN draftYear IS NULL OR draftYear = '' THEN 'Undrafted'
                    WHEN draftRound IS NULL OR draftRound = '' THEN 'Undrafted'
                    WHEN draftRound = 'Undrafted' THEN 'Undrafted'
                    ELSE CONCAT(draftYear, ': ', draftPick)
                END) AS `Draft`
                FROM players p
                JOIN stats_rs_per75 s75 ON p.playerId = s75.playerId
                JOIN stats_rs_totals st ON p.playerId = st.playerId
                JOIN teams t ON p.teamId = t.teamId
                WHERE 1
                ORDER BY ((s75.PTS*1.5 + s75.plusMinus + s75.REB + s75.AST + s75.STL + s75.BLK) * ((st.MIN/st.gp)/36)) DESC
                ";

$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

if (empty($result) || !$result->num_rows) {
    echo "<p>No data available.</p>";
    return;
}

// Fuzzy search input
echo "<input type='text' id='playerSearch' placeholder='Search players...' style='margin-bottom:1rem; width:60%; font-size:1rem; padding:0.5rem; font-family: Roboto Condensed; border: 0;'>";

if (!empty($data)) {
    echo "<div class='table-background'><table id='playersTable'>";
    // Table header
    echo "<thead><tr>";
    foreach (array_keys($data[0]) as $col) {
        if ($col === 'Player ID' || $col === 'Team ID' || $col === 'Team Abr') continue;
        if ($col === 'Player' || $col === 'Team') {
            echo "<th></th>";
        }
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Table rows
    foreach ($data as $row) {
        echo "<tr>";
        // Image column
        $playerId = $row['Player ID'];
        $teamId = $row['Team ID'];
        $teamAbr = strtolower($row['Team Abr']);
        $playerImgPath = "/../assets/img/players/{$playerId}.png";
        $playerImgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
        if (!file_exists($playerImgFullPath)) {
            $playerImgPath = "/../assets/img/players/default.png";
        }
        $teamImgPath = "/../assets/img/teams/{$teamAbr}.svg";
        $teamImgFullPath = __DIR__ . "/../assets/img/teams/{$teamAbr}.svg";
        if (!file_exists($teamImgFullPath)) {
            $teamImgPath = "/../assets/img/teams/nba.png";
        }
        foreach ($row as $key => $cell) {
            if ($key === 'Player ID' || $key === 'Team ID' || $key === 'Team Abr') continue;
            if ($key === 'Player') {
                // Link player name to player page
                echo "<td style='display:flex;padding-top:.4rem;width:2.25rem;min-width:2.25rem;justify-content:center;'><a href='?page=player&player=" . urlencode($playerId) . "'><img src='" . htmlspecialchars($playerImgPath) . "' style='height:2.25rem;width:2.25rem;object-fit:cover;border-radius:2.25rem;'></a></td><td style='width:40%; min-width:120px;'><a href='?page=player&player=" . urlencode($playerId) . "' class='table-link'>" . htmlspecialchars($cell) . "</a></td>";
            } else if ($key === 'Team') {
                // Link player name to player page
                echo "<td style='display:flex;padding-right:0;width:2.25rem; min-width:2.25rem;justify-content:center;'><a href='?page=team&team=" . urlencode($teamId) . "'><img src='" . htmlspecialchars($teamImgPath) . "' style='height:2.25rem;width: auto'></a></td><td style='width:40%; min-width:120px;'><a href='?page=team&team=" . urlencode($teamId) . "' class='table-link' style='color:var(--" . strtolower($teamAbr) ."-primary);'>" . htmlspecialchars($cell) . "</a></td>";
            } else {
                echo "<td style='width:20%; min-width:80px;'>" . htmlspecialchars($cell) . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}

// Fuzzy search script (client-side, inclusive)
echo "<script>
document.getElementById('playerSearch').addEventListener('input', function() {
    var search = this.value.toLowerCase();
    var rows = document.querySelectorAll('#playersTable tbody tr');
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        // Fuzzy match: allow any substring, ignore spaces, very inclusive
        var normalizedText = text.replace(/\s+/g, '');
        var normalizedSearch = search.replace(/\s+/g, '');
        var match = normalizedText.includes(normalizedSearch);
        if (!match && normalizedSearch.length > 1) {
            // Fuzzy: check if all search chars appear in order (not necessarily consecutive)
            var i = 0, j = 0;
            while (i < normalizedText.length && j < normalizedSearch.length) {
                if (normalizedText[i] === normalizedSearch[j]) j++;
                i++;
            }
            match = (j === normalizedSearch.length);
        }
        row.style.display = match || search === '' ? '' : 'none';
    });
});
</script>";

?>