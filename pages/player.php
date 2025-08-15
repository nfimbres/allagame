<?php

$player = isset($_GET['player']) ? htmlspecialchars($_GET['player']) : null;

include __DIR__ . '/../../private_html/db-connection.php';

if ($player !== null) {
    // New SQL query to get player info
    $sql = "SELECT fullName AS `Player`, playerId AS `Player ID` FROM players WHERE playerId = '" . $player . "'";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $playerId = $row['Player ID'];
        $playerName = $row['Player'];
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
