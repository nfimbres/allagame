<h1 style="padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--primary);">2024-25 Teams</h1>
<?php
include __DIR__ . '/../../private_html/db-connection.php';

$sql = "SELECT
    teamId,
    teamAbr,
    team,
    conference
FROM teams
ORDER BY name ASC
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

if (!empty($data)) {
    echo "<div class='table-background'><table id='teamsTable'>";
    // Table header
    echo "<thead><tr>";
    // Show logo next to team name
    echo "<th></th><th>Team</th><th>Conference</th><th>Top 3 Players</th>";
    echo "</tr></thead><tbody>";
    // Table rows
    foreach ($data as $row) {
        echo "<tr>";
        $teamId = $row['teamId'];
        $teamAbr = strtolower($row['teamAbr']);
        $teamImgPath = "/../assets/img/teams/{$teamAbr}.svg";
        $teamImgFullPath = __DIR__ . "/../assets/img/teams/{$teamAbr}.svg";
        if (!file_exists($teamImgFullPath)) {
            $teamImgPath = "/../assets/img/teams/nba.png";
        }
        // Logo and team name (team name as link)
        echo "<td style='display:flex;padding-right:0;width:2.25rem; min-width:2.25rem;justify-content:center;'><img src='" . htmlspecialchars($teamImgPath) . "' style='height:2.25rem;width: auto'></td>";
        echo "<td style='width:40%; min-width:120px;'><a href='?page=team&team=" . urlencode($teamId) . "' class='table-link' style='color:var(--" . strtolower($teamAbr) ."-primary);'>" . htmlspecialchars($row['team']) . "</a></td>";
        // Conference
        echo "<td style='width:20%; min-width:80px;'>" . htmlspecialchars($row['conference']) . "</td>";

        // Top 3 players for this team
        $playersSql = "SELECT p.playerId, p.fullName FROM players p JOIN stats_rs_per75 s75 ON p.playerId = s75.playerId JOIN stats_rs_totals st ON p.playerId = st.playerId WHERE p.teamId = '" . $conn->real_escape_string($teamId) . "' ORDER BY ((s75.PTS*1.5 + s75.plusMinus + s75.REB + s75.AST + s75.STL + s75.BLK) * ((st.MIN / st.GP) / 36)) DESC LIMIT 3";
        $playersResult = $conn->query($playersSql);
        $playersImgs = [];
        if ($playersResult && $playersResult->num_rows > 0) {
            while ($pRow = $playersResult->fetch_assoc()) {
                $playerId = $pRow['playerId'];
                $playerImgPath = "/../assets/img/players/{$playerId}.png";
                $playerImgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
                if (!file_exists($playerImgFullPath)) {
                    $playerImgPath = "/../assets/img/players/default.png";
                }
                $playersImgs[] = "<a href='?page=player&player=" . urlencode($playerId) . "'><img src='" . htmlspecialchars($playerImgPath) . "' style='height:2.25rem;width:2.25rem;object-fit:cover;border-radius:2.25rem;margin-right:8px;'></a>";
            }
        }
        echo "<td style='display:flex;align-items:center;padding-top:.4rem;'>" . implode("", $playersImgs) . "</td>";

        echo "</tr>";
    }
    echo "</tbody></table></div>";
}
?>