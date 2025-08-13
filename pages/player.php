<body class="d-flex justify-content-center align-items-start" style="background: #f6f6f6;height: 800px;">
    <div class="container d-flex justify-content-center" style="--bs-primary: rgb(8, 41, 114);--bs-primary-rgb: 8,41,114;--bs-secondary: rgb(234, 105, 12);--bs-secondary-rgb: 234,105,12;margin: 10px;padding: 0px;padding-top: 100px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;height: 800px;margin-right: 0px;margin-left: 0px;">
        <div style="width: 1000px;">
            <?php

            $player = isset($_GET['player']) ? htmlspecialchars($_GET['player']) : null;

            // Ensure $conn is defined and is a valid MySQLi connection
            if (!isset($conn) || !$conn instanceof mysqli) {
                // Replace with your actual connection parameters
                $conn = new mysqli('localhost', 'username', 'password', 'database_name');
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
            }

            if ($player !== null) {
                // New SQL query to get player info
                $sql = "SELECT playerId AS `Player ID`, fullName AS `Player Name`, height AS `Ht`, weight AS `Wt`, (CASE WHEN draftPick is NULL OR draftPick = '' THEN 'Undrafted' WHEN draftYear IS NULL OR draftYear = '' THEN 'Undrafted' WHEN draftRound IS NULL OR draftRound = '' THEN 'Undrafted' WHEN draftRound = 'Undrafted' THEN 'Undrafted' ELSE CONCAT(draftYear, ': ', draftPick) END) AS `Draft` FROM players WHERE playerId = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param('s', $player);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                if ($row) {
                    echo "<h1 style=\"padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--bs-primary);\">Player ID: " . $row['Player ID'] . "</h1>";
                    echo "<h2 style=\"font-family: 'Roboto Condensed';color: var(--bs-secondary);\">" . htmlspecialchars($row['Player Name']) . "</h2>";
                    $playerId = $row['Player ID'];
                    $imgPath = "/../assets/img/players/{$playerId}.png";
                    $imgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
                    if (!file_exists($imgFullPath)) {
                        $imgPath = "/../assets/img/players/default.png";
                    }
                    echo "<img src='{$imgPath}' alt='Player Image' style='max-width:200px;max-height:200px;margin-bottom:20px;'>";
                }
            }
            
            ?>
            <container>
            </container>
        </div>
    </div>
</body>