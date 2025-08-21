<?php

$player = isset($_GET['player']) ? htmlspecialchars($_GET['player']) : null;

include __DIR__ . '/../../private_html/db-connection.php';

if ($player !== null) {
    // New SQL query to get player info
    $player_sql = "SELECT p.fullName AS `Player`, p.playerId AS `Player ID`, p.height AS `Height`, CONCAT(p.position1, IF(p.position2 IS NOT NULL AND p.position2 != '', CONCAT('/', p.position2), '')) AS `Position`, t.teamId AS `Team ID`, t.name AS `Team`, t.teamAbr AS `Team Abr` FROM players p JOIN teams t ON p.teamId = t.teamId WHERE playerId = '" . $player . "'";
    $result = $conn->query($player_sql);
    $imgPath = "/../assets/img/players/default.png";
    $playerName = '';
    $playerPos = '';
    if ($result && $row = $result->fetch_assoc()) {
        $playerId = $row['Player ID'];
        $playerName = $row['Player'];
        $playerPos = $row['Position'];
        $playerHt = $row['Height'];
        $teamId = $row['Team ID'];
        $teamName = $row['Team'];
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
    }
} else {
    header("Location: ?page=404");
    exit();
}

?>

<div class="path">
    <h2>
        <a href="?page=players">Players</a> > <i><?php echo htmlspecialchars($playerName); ?></i>
    </h2>
</div>
<div class="player-banner">
    <img src="<?php echo htmlspecialchars($playerImgPath); ?>" class="banner-headshot">
    <div style="display: flex; flex-direction: column; justify-content: center;">
        <h1 style="white-space: nowrap"><?php echo htmlspecialchars($playerName); ?></h1>
        <div style="display:flex;flex-direction:row;align-items:center;"><img src="<?php echo htmlspecialchars($teamImgPath); ?>" class="team-logo">
            <h2 style=<?php echo "color:var(--" . strtolower($teamAbr) . "-primary);" ?>><a href=<?php echo "?page=team&team=" . $teamId; ?>><?php echo htmlspecialchars($teamName); ?></a></h2>
        </div>
        <h2><?php echo htmlspecialchars($playerHt); ?> <?php echo htmlspecialchars($playerPos); ?></h2>
    </div>
</div>

<?php

// SQL with subquery for league TS% and TS% Diff
$sql = "SELECT
    '2024-25' AS `Season`,
    p.playerId AS `Player ID`,
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
WHERE p.playerId = '" . $player . "' ORDER BY ((s75.PTS + s75.plusMinus) * ((st.MIN / st.GP) / 36)) DESC
";


$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo "<div class='table-background'><h2>Per Game</h2><table id='playerStatsTable'>";
    // Table header
    echo "<thead><tr>";
    $skipCols = ['Player ID', 'Team ID', 'Team Abr', 'Team'];
    foreach (array_keys($data[0]) as $col) {
        if (in_array($col, $skipCols)) continue;
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $key => $cell) {
            if (in_array($key, $skipCols)) continue;
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}

$sql = "SELECT
    '2024-25' AS `Season`,
    p.playerId AS `Player ID`,
    st.GP AS `GP`,
    CONCAT(st.W, '-', st.L) AS `W-L`,
    s75.MIN AS `MIN`,
    s75.PTS AS `PTS`,
    s75.AST AS `AST`,
    s75.REB AS `REB`,
    s75.STL AS `STL`,
    s75.BLK AS `BLK`,
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
    s75.3PM AS `3PA`,
    ROUND(COALESCE(st.3PM / NULLIF(st.3PA, 0), 0)*100, 1) AS `3P%`,
    s75.FTM AS `FTA`,
    ROUND(COALESCE(st.FTM / NULLIF(st.FTA, 0), 0)*100, 1) AS `FT%`,
    CONCAT(CASE WHEN s75.plusMinus > 0 THEN '+' ELSE '' END, s75.plusMinus) AS `+/-`,
    st.DD2 AS `DD2`,
    st.TD3 AS `TD3`
FROM players p
JOIN stats_rs_per75 s75 ON p.playerId = s75.playerId
JOIN stats_rs_totals st ON p.playerId = st.playerId
JOIN teams t ON p.teamId = t.teamId
WHERE p.playerId = '" . $player . "' ORDER BY ((s75.PTS + s75.plusMinus) * ((st.MIN / st.GP) / 36)) DESC
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo "<div class='table-background'><h2>Per 75 Possessions</h2><table id='playerStatsTable'>";
    // Table header
    echo "<thead><tr>";
    $skipCols = ['Player ID', 'Team ID', 'Team Abr', 'Team'];
    foreach (array_keys($data[0]) as $col) {
        if (in_array($col, $skipCols)) continue;
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $key => $cell) {
            if (in_array($key, $skipCols)) continue;
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
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

    .banner-headshot {
        height: 9rem;
        width: auto rem;
        margin: 1rem;
        margin-bottom: 0;
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