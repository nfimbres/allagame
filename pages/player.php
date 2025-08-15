<?php

$player = isset($_GET['player']) ? htmlspecialchars($_GET['player']) : null;

include __DIR__ . '/../../private_html/db-connection.php';

if ($player !== null) {
    // New SQL query to get player info
    $sql = "SELECT fullName AS `Player`, playerId AS `Player ID`, COALESCE(CONCAT(position1,COALESCE(CONCAT('/',position2)))) AS `Position` FROM players WHERE playerId = '" . $player . "'";
    $result = $conn->query($sql);
    $imgPath = "/../assets/img/players/default.png";
    $playerName = '';
    $playerPos = '';
    if ($result && $row = $result->fetch_assoc()) {
        $playerId = $row['Player ID'];
        $playerName = $row['Player'];
        $playerPos = $row['Position'];
        $imgPath = "/../assets/img/players/{$playerId}.png";
        $imgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
        if (!file_exists($imgFullPath)) {
            $imgPath = "/../assets/img/players/default.png";
        }
    }
} else {
    header("Location: ?page=404");
    exit();
}

?>


<div class="player-banner">
    <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Player Image" class="banner-headshot">
    <div style="display: flex; flex-direction: column; justify-content: center;">
        <h1><?php echo htmlspecialchars($playerName); ?></h1>
        <h2><?php echo htmlspecialchars($playerPos); ?></h1>
    </div>
</div>

<br>
<h1> Career Stats</h1>
<table>
    <thead>
        <tr class="table-header">
            <th>Season</th>
            <th>Team</th>
            <th>PTS</th>
            <th>REB</th>
            <th>AST</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>2025</td>
            <td>MIN</td>
            <td>25.2</td>
            <td>5.4</td>
            <td>5.6</td>
        </tr>
</table>


<style>
    .player-banner {
        height: 10rem;
        width: 100%;
        background-color: white;
        margin-top: 1rem;
        display: flex;
        align-items: center;
        font-family: 'Roboto Condensed';
        color: var(--primary);
    }

    .banner-headshot {
        height: 9rem;
        width: auto rem;
        margin: 1rem;
        margin-bottom: 0;
        /* background-image: url('/../assets/img/players/default.png'); */
    }
</style>