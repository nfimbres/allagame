<body class="d-flex justify-content-center align-items-start" style="background: #f6f6f6;height: 800px;">
    <div class="container d-flex justify-content-center" style="--bs-primary: rgb(8, 41, 114);--bs-primary-rgb: 8,41,114;--bs-secondary: rgb(234, 105, 12);--bs-secondary-rgb: 234,105,12;margin: 10px;padding: 0px;padding-top: 100px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;height: 800px;margin-right: 0px;margin-left: 0px;">
        <div style="width: 1000px;">
            <h1 style="padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--bs-primary);">2024-25 Players</h1>
            <container>
                <?php

                include __DIR__ . '/../../private_html/db-connection.php';

                // Pagination setup
                $perPage = 50;
                $page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;

                // Get total count
                $countSql = "SELECT COUNT(*) as cnt FROM players";
                $countResult = $conn->query($countSql);
                $totalPlayers = ($countResult && $countResult->num_rows > 0) ? intval($countResult->fetch_assoc()['cnt']) : 0;
                $totalPages = $totalPlayers > 0 ? ceil($totalPlayers / $perPage) : 1;

                $offset = ($page - 1) * $perPage;

                $sql =
                    "SELECT
                playerId AS `Player ID`,
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
                ORDER BY draftPick ASC, draftYear ASC, lastName ASC, firstName ASC
                LIMIT $perPage OFFSET $offset
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
                    echo "<div class='responsive-table-wrapper'><table class='stats-table'>";
                    // Table header
                    echo "<thead><tr class='table-header'>";
                    echo "<th class='table-header-cell'></th>";
                    echo "<th class='table-header-cell'>Player</th>";
                    foreach (array_keys($data[0]) as $col) {
                        if ($col === 'Player ID' || $col === 'Player Name') continue;
                        echo "<th class='table-header-cell'>" . htmlspecialchars($col) . "</th>";
                    }
                    echo "</tr></thead><tbody>";
                    // Table rows
                    foreach ($data as $row) {
                        echo "<tr class='table-row'>";
                        // Image column
                        $playerId = $row['Player ID'];
                        $imgPath = "/../assets/img/players/{$playerId}.png";
                        $imgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
                        if (!file_exists($imgFullPath)) {
                            $imgPath = "/../assets/img/players/default.png";
                        }
                        echo "<td class='table-cell' style='width:2.25rem; min-width:2.25rem;'><a href='?page=player&player=" . urlencode($playerId) . "'><img src='" . htmlspecialchars($imgPath) . "' alt='Player' style='height:2.25rem;width:2.25rem;object-fit:cover;border-radius:10px;'></a></td>";
                        foreach ($row as $key => $cell) {
                            if ($key === 'Player ID') continue;
                            if ($key === 'Player Name') {
                                // Link player name to player page
                                echo "<td class='table-cell' style='width:40%; min-width:120px;'><a href='?page=player&player=" . urlencode($playerId) . "' class='table-link'>" . htmlspecialchars($cell) . "</a></td>";
                            } else {
                                echo "<td class='table-cell' style='width:20%; min-width:80px;'>" . htmlspecialchars($cell) . "</td>";
                            }
                        }
                        echo "</tr>";
                    }
                    echo "</tbody></table></div>";
                }

                // Build base query string without p
                $queryParams = $_GET;
                unset($queryParams['p']);
                $baseUrl = $_SERVER['PHP_SELF'];
                $baseQuery = http_build_query($queryParams);
                $base = $baseUrl . ($baseQuery ? '?' . $baseQuery . '&' : '?');

                // Pagination controls
                echo "<div style='margin-top:20px; display:flex; align-items:center; gap:10px;'>";
                // Previous button
                if ($page > 1) {
                    echo "<a href='" . $base . "p=" . ($page - 1) . "' class='btn btn-primary'>Previous</a>";
                }
                // Page dropdown
                echo "<form method='get' style='display:inline;'>";
                // Preserve all other query params as hidden fields
                foreach ($queryParams as $key => $val) {
                    echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($val) . "'>";
                }
                echo "<select name='p' onchange='this.form.submit()' style='margin:0 10px;'>";
                for ($i = 1; $i <= $totalPages; $i++) {
                    $selected = ($i == $page) ? "selected" : "";
                    echo "<option value='$i' $selected>Page $i</option>";
                }
                echo "</select>";
                echo "</form>";
                // Next button
                if ($page < $totalPages) {
                    echo "<a href='" . $base . "p=" . ($page + 1) . "' class='btn btn-primary'>Next</a>";
                }
                echo "</div>";

                ?>
            </container>
        </div>
    </div>
</body>