<?php

$team = isset($_GET['team']) ? htmlspecialchars($_GET['team']) : null;

include __DIR__ . '/../../private_html/db-connection.php';

if ($team !== null) {
    // New SQL query to get player info
    $team_sql = "SELECT t.team AS `Team`, t.teamAbr AS `Team Abr`, t.teamId AS `Team ID`, t.name AS `Name`, t.conference AS `Conf`  FROM teams t WHERE t.teamId = '" . $team . "'";
    $result = $conn->query($team_sql);
    $imgPath = "/../assets/img/players/default.png";
    $teamName = '';
    $teamConf = '';
    if ($result && $row = $result->fetch_assoc()) {
        $team = $row['Team ID'];
        $teamName = $row['Team'];
        $teamConf = $row['Conf'];
        $teamAbr = strtolower($row['Team Abr']);
        $teamImgPath = "/../assets/img/teams/{$teamAbr}.svg";
        $teamImgFullPath = __DIR__ . "/../assets/img/teams/{$teamAbr}.svg";
        if (!file_exists($teamImgFullPath)) {
            $teamImgPath = "/../assets/img/teams/nba.png";
        }
    }
} else {
    header("Location: ?page=404");
    exit();
}

?>

<div class="path">
    <h2>
        <a href="?page=teams">Teams</a> > <i><?php echo htmlspecialchars($teamName); ?></i>
    </h2>
</div>
<div class="player-banner">
    <img src="<?php echo htmlspecialchars($teamImgPath); ?>" class="banner-team-logo">
    <div style="display: flex; flex-direction: column; justify-content: center;">
        <h1 style="white-space: nowrap"><?php echo htmlspecialchars($teamName); ?></h1>
    </div>
</div>

<?php 

$sql = "SELECT
    p.playerId AS `Player ID`,
    p.fullName AS `Player`,
    p.teamId AS `Team ID`,
    t.teamAbr AS 'Team Abr',
    t.name AS `Team`,
    p.height AS `Ht`,
    CONCAT(position1, IF(position2 IS NOT NULL AND position2 != '', CONCAT('/', position2), '')) AS `Position`,
    st.GP AS `GP`,
    CONCAT(st.W, '-', st.L) AS `W-L`,
    ROUND(st.MIN / NULLIF(st.GP, 0), 1) AS `MIN`,
    ROUND(s75.PTS * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `PTS`,
    ROUND(s75.AST * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `AST`,
    ROUND(s75.REB * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `REB`,
    ROUND(s75.STL * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `STL`,
    ROUND(s75.BLK * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `BLK`,
    ROUND(COALESCE((st.PTS * 0.5) / NULLIF((st.FGA + 0.44 * st.FTA), 0), 0)*100, 1) AS `TS%`,
    CONCAT(CASE WHEN ROUND(100*(
        ROUND(COALESCE((st.PTS * 0.5) / NULLIF((st.FGA + 0.44 * st.FTA), 0), 0), 3) -
        (SELECT ROUND(COALESCE((SUM(st2.PTS) * 0.5) / NULLIF((SUM(st2.FGA) + 0.44 * SUM(st2.FTA)), 0), 0), 3) FROM stats_rs_totals st2)
    ),1) > 0 THEN '+' ELSE '' END,
    ROUND(100*(
        ROUND(COALESCE((st.PTS * 0.5) / NULLIF((st.FGA + 0.44 * st.FTA), 0), 0), 3) -
        (SELECT ROUND(COALESCE((SUM(st2.PTS) * 0.5) / NULLIF((SUM(st2.FGA) + 0.44 * SUM(st2.FTA)), 0), 0), 3) FROM stats_rs_totals st2)
    ),1)
    ) AS `rTS%`,
    ROUND(s75.3PM * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `3PA`,
    ROUND(COALESCE(st.3PM / NULLIF(st.3PA, 0), 0)*100, 1) AS `3P%`,
    ROUND(s75.FTM * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) AS `FTA`,
    ROUND(COALESCE(st.FTM / NULLIF(st.FTA, 0), 0)*100, 1) AS `FT%`,
    CONCAT(
        CASE WHEN ROUND(s75.plusMinus * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1) > 0 THEN '+' ELSE '' END,
        ROUND(s75.plusMinus * (ROUND(st.MIN / NULLIF(st.GP, 0), 1) / 36), 1)
    ) AS `+/-`,
    st.DD2 AS `DD2`,
    st.TD3 AS `TD3`
FROM players p
JOIN stats_rs_per75 s75 ON p.playerId = s75.playerId
JOIN stats_rs_totals st ON p.playerId = st.playerId
JOIN teams t ON p.teamId = t.teamId
WHERE t.teamId = '" . $team . "' AND st.GP >= 15 AND round(st.MIN/st.gp) >= 6
ORDER BY ((s75.PTS*1.5 + s75.plusMinus + s75.REB + s75.AST + s75.STL + s75.BLK) * ((st.MIN / st.GP) / 36)) DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo "<div class='table-background'><h2>Team Stats (2024-25)</h2><table id='playersTable'>";
    // Table header
    echo "<thead><tr>";
    // Rk column: not sortable
    echo "<th>Rk</th><th></th><th>Player</th><th></th";
    $skipCols = ['Player ID', 'Player', 'Team ID', 'Team Abr', 'Team', 'heightIn'];
    foreach (array_keys($data[0]) as $col) {
        if (in_array($col, $skipCols)) continue;
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    $rk = 1;
    foreach ($data as $row) {
        $heightIn = 0;
        if (isset($row['Ht'])) {
            if (preg_match("/(\d+)'(\d+)/", $row['Ht'], $matches)) {
                $heightIn = ((int)$matches[1]) * 12 + (int)$matches[2];
            }
        }
        echo "<tr data-heightin='" . htmlspecialchars($heightIn) . "'>";
        // Rk column
        echo "<td>" . $rk++ . "</td>";
        // Image columns
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
        // Headshot
        echo "<td><a href='?page=player&player=" . urlencode($playerId) . "'><img src='" . htmlspecialchars($playerImgPath) . "' style='height:2.25rem;width:2.25rem;object-fit:cover;border-radius:2.25rem;'></a></td>";
        // Player name
        echo "<td><a href='?page=player&player=" . urlencode($playerId) . "' class='table-link'>" . htmlspecialchars($row['Player']) . "</a></td>";
        // Team logo and name columns removed
        // Stats columns
        foreach ($row as $key => $cell) {
            if (in_array($key, $skipCols)) continue;
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "<p>No stats found for this team.</p>";
}


?>


<style>
    .player-banner {
        height: 10rem;
        width: 100%;
        background-color: white;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        font-family: 'Roboto Condensed';
        color: black;
    }

    .banner-team-logo {
        height: 9rem;
        width: auto rem;
        margin: 0.5rem;
    }

    .team-logo {
        height: 2rem;
        width: auto;
        margin: 0;
        padding: 0;
        margin-right: 0.2rem;
    }

    .path {
        width: 100%;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
    }

    .player-banner h1 {
        color: black;
        font-size: 3rem;
        margin: 0;
    }

    .player-banner h2 {
        color: black;
        font-size: 1.5rem;
        margin: 0;
    }

    .table-name {
        font-family: 'Roboto Condensed';
        font-weight: bold;
        color: black;
    }
</style>