<body class="d-flex justify-content-center align-items-start" style="background: #f6f6f6;height: 800px;">
    <div class="container d-flex justify-content-center" style="--bs-primary: rgb(8, 41, 114);--bs-primary-rgb: 8,41,114;--bs-secondary: rgb(234, 105, 12);--bs-secondary-rgb: 234,105,12;margin: 10px;padding: 0px;padding-top: 100px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;height: 800px;margin-right: 0px;margin-left: 0px;">
        <div style="width: 1000px;">
            <h1 style="padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--bs-primary);">2024-25 Players</h1>
            <container>
                <?php

                require_once 'assets/php/db-connection.php';

                $sql =
                    "SELECT
                fullName AS `Player Name`,
                height AS `Ht`,
                weight AS `Wt`,
                (CASE
                    WHEN draftPick is NULL OR draftPick = '' THEN 'Undrafted'
                    WHEN draftYear IS NULL OR draftYear = '' THEN 'Undrafted'
                    WHEN draftRound IS NULL OR draftRound = '' THEN 'Undrafted'
                    WHEN draftRound = 'Undrafted' THEN 'Undrafted'
                    ELSE CONCAT(draftYear, ': ', draftPick)
                END) AS `Draft`
                FROM players
                WHERE 1
                ORDER BY draftPick ASC, draftYear ASC
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

                // Display table
                if (!empty($data)) {
                    echo "<table class='nba-table'>";
                    // Table header
                    echo "<thead><tr class='nba-header'>";
                    foreach (array_keys($data[0]) as $col) {
                        echo "<th class='nba-header-cell'>" . htmlspecialchars($col) . "</th>";
                    }
                    echo "</tr></thead><tbody>";
                    // Table rows
                    foreach ($data as $row) {
                        echo "<tr class='nba-row'>";
                        foreach ($row as $cell) {
                            echo "<td class='nba-cell'>" . htmlspecialchars($cell) . "</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                }

                ?>
            </container>
        </div>
    </div>
</body>