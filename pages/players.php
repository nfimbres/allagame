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
                -- show all players, no pagination
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
echo "<input type='text' id='playerSearch' placeholder='Search players...' style='margin-bottom:1rem; width:60%; font-size:1rem; padding:0.5rem; font-family: Roboto Condensed;'>";

if (!empty($data)) {
    echo "<div><table id='playersTable'>";
    // Table header
    echo "<thead><tr class='table-header'>";
    echo "<th></th>";
    echo "<th>Player</th>";
    foreach (array_keys($data[0]) as $col) {
        if ($col === 'Player ID' || $col === 'Player Name') continue;
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Table rows
    foreach ($data as $row) {
        echo "<tr>";
        // Image column
        $playerId = $row['Player ID'];
        $imgPath = "/../assets/img/players/{$playerId}.png";
        $imgFullPath = __DIR__ . "/../assets/img/players/{$playerId}.png";
        if (!file_exists($imgFullPath)) {
            $imgPath = "/../assets/img/players/default.png";
        }
        echo "<td style='width:2.25rem; min-width:2.25rem;'><a href='?page=player&player=" . urlencode($playerId) . "'><img src='" . htmlspecialchars($imgPath) . "' alt='Player' style='height:2.25rem;width:2.25rem;object-fit:cover;border-radius:2.25rem;'></a></td>";
        foreach ($row as $key => $cell) {
            if ($key === 'Player ID') continue;
            if ($key === 'Player Name') {
                // Link player name to player page
                echo "<td style='width:40%; min-width:120px;'><a href='?page=player&player=" . urlencode($playerId) . "' class='table-link'>" . htmlspecialchars($cell) . "</a></td>";
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

// ...existing code...


// ...existing code...

?>
<style>
    table {
        font-family: 'Roboto Condensed';
        width: 1000px;
        font-size: .875rem;
        width: 60%;
        border-collapse: collapse;
    }

    tr.table-header {
        text-align: left;
        background-color: var(--primary);
        color: var(--secondary);
    }

    tr {
        background-color: white;
        border-bottom: 2px solid var(--gray-200);
    }

    td {
        color: var(--primary);
        background-color: white;
        padding-left: .2rem;
        padding-right: .4rem;
        padding-top: .4rem;
        padding-bottom: .2rem;
        border-left: 0px;
        border-right: 0px;
        text-align: left;
        white-space: nowrap;
        width: fit-content;
    }

    th {
        padding: .4rem;
    }

    a.table-link:link,
    a.table-link:visited {
        color: var(--primary);
        text-decoration: none;
    }

    a.table-link:hover {
        color: var(--secondary);
        text-decoration: none;
    }

    a.header-link:link,
    a.header-link:visited {
        color: var(--secondary);
        text-decoration: none;
    }

    a.header-link:hover {
        color: white;
        text-decoration: none;
    }

    a.pagination-button {
        color: var(--secondary);
        background-color: var(--primary);
        font-family: 'Roboto Condensed';
        padding: 0.5rem 1rem;
        text-decoration: none;
        border-radius: 0.25rem;
    }
</style>