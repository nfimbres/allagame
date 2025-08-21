<h1 style="padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--primary);">2024-25 Leaders</h1>
<?php
include __DIR__ . '/../../private_html/db-connection.php';

$sql = "SELECT
    p.playerId AS `Player ID`,
    p.fullName AS `Player`,
    p.teamId AS `Team ID`,
    t.teamAbr AS 'Team Abr',
    t.name AS `Team`,
    t.conference AS `Conference`,
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
WHERE st.GP >= 15 AND ROUND(st.MIN / st.GP) >= 12
ORDER BY ((s75.PTS*1.5 + s75.plusMinus + s75.REB + s75.AST + s75.STL + s75.BLK) * ((st.MIN / st.GP) / 36)) DESC
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
// Add filter dropdowns for Team and Position (custom groups)
$teams = array_unique(array_map(function($row) { return $row['Team']; }, $data));
sort($teams);

// Custom position groups
$positionGroups = [
    'Point Guard' => ['PG', 'PG/SG', 'PG/ANYTHING'],
    'Shooting Guard' => ['SG', 'SG/PG', 'SG/SF', 'SG/ANYTHING'],
    'Small Forward' => ['SF', 'SF/SG', 'SF/PF', 'SF/ANYTHING'],
    'Power Forward' => ['PF', 'PF/SF', 'PF/C', 'PF/ANYTHING'],
    'Center' => ['C', 'C/PF', 'C/ANYTHING'],
    'Guard' => ['PG', 'PG/SG', 'SG/PG', 'SG', 'PG/ANYTHING'],
    'Wing' => ['SG', 'SG/SF', 'SF/SG', 'SF', 'SF/ANYTHING', 'SG/ANYTHING'],
    'Forward' => ['SF', 'SF/PF', 'PF', 'PF/SF', 'SF/ANYTHING', 'PF/ANYTHING'],
    'Big' => ['PF/C', 'C', 'C/PF', 'C/ANYTHING']
];

// Normalize positions in data for matching
function normalize_position($pos) {
    return strtoupper(str_replace([' ', '-'], '', $pos));
}

// Fuzzy search input
// Add filter dropdowns for Team and Position
$teams = array_unique(array_map(function($row) { return $row['Team']; }, $data));
$positions = array_unique(array_map(function($row) { return $row['Position']; }, $data));

sort($teams);
sort($positions);

// Add conference filter
$conferences = array_unique(array_map(function($row) { return isset($row['Conference']) ? $row['Conference'] : ''; }, $data));
sort($conferences);
echo "<div style='margin-bottom:1rem;display:flex;gap:1rem;align-items:center;'>";
echo "<input type='text' id='playerSearch' placeholder='Search players...' style='font-size:1rem; padding:0.5rem; font-family: Roboto Condensed; border: 0; width:30%;'>";
// Build team-conference map for JS
$teamConfMap = [];
foreach ($data as $row) {
    $teamConfMap[$row['Team']] = $row['Conference'];
}
echo "<select id='teamFilter' style='font-size:1rem; padding:0.5rem; font-family: Roboto Condensed; border: 0px;'><option value=''>All Teams</option>";
foreach ($teams as $team) {
    echo "<option value='" . htmlspecialchars($team) . "' data-conf='" . htmlspecialchars($teamConfMap[$team]) . "'>" . htmlspecialchars($team) . "</option>";
}
echo "</select>";
echo "<select id='conferenceFilter' style='font-size:1rem; padding:0.5rem; font-family: Roboto Condensed; border: 0px;'><option value=''>All Conferences</option>";
foreach ($conferences as $conf) {
    if ($conf) echo "<option value='" . htmlspecialchars($conf) . "'>" . htmlspecialchars($conf) . "</option>";
}
echo "</select>";
echo "<select id='positionFilter' style='font-size:1rem; padding:0.5rem; font-family: Roboto Condensed; border: 0px;'><option value=''>All Positions</option>";
foreach ($positionGroups as $label => $codes) {
    echo "<option value='" . htmlspecialchars($label) . "'>" . htmlspecialchars($label) . "</option>";
}
echo "</select>";
echo "</div>";

if (!empty($data)) {
    echo "<div class='table-background'><h2>League Stats</h2><table id='playersTable'>";
    // Table header
    echo "<thead><tr>";
    // Rk column: not sortable
    echo "<th>Rk</th><th></th><th>Player</th><th></th><th>Team</th>";
    $skipCols = ['Player ID', 'Player', 'Team ID', 'Team Abr', 'Team', 'heightIn', 'Conference'];
    foreach (array_keys($data[0]) as $col) {
        if (in_array($col, $skipCols)) continue;
        echo "<th>" . htmlspecialchars($col) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Table rows
    $rk = 1;
    foreach ($data as $row) {
        // Add data-heightin attribute for sorting by height
        $heightIn = 0;
        if (isset($row['Ht'])) {
            // Convert height string (e.g., 6'7") to inches
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
        // Team logo
        echo "<td><a href='?page=team&team=" . urlencode($teamId) . "'><img src='" . htmlspecialchars($teamImgPath) . "' style='height:2.25rem;width: auto'></a></td>";
        // Team name
        echo "<td><a href='?page=team&team=" . urlencode($teamId) . "' class='table-link' style='color:var(--" . strtolower($teamAbr) . "-primary);'>" . htmlspecialchars($row['Team']) . "</a></td>";
        // Stats columns
        foreach ($row as $key => $cell) {
            if (in_array($key, $skipCols)) continue;
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        // Hidden conference cell for filtering
        echo "<td style='display:none;'>" . htmlspecialchars($row['Conference']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}

// Fuzzy search and sortable table script
// Add filter logic for team and position group
?>
<script>
const positionGroups = {
    'Point Guard': ['PG', 'PG/SG', 'PG/PF'],
    'Shooting Guard': ['SG', 'SG/PG', 'SG/SF'],
    'Small Forward': ['SF', 'SF/SG', 'SF/PF'],
    'Power Forward': ['PF', 'PF/SF', 'PF/C'],
    'Center': ['C', 'C/PF'],
    'Guard': ['PG', 'PG/SG', 'SG/PG', 'SG'],
    'Wing': ['SG', 'SG/SF', 'SF/SG'],
    'Forward': ['SF', 'SF/PF', 'PF', 'PF/SF'],
    'Big': ['PF/C', 'C', 'C/PF']
};

document.getElementById('playerSearch').addEventListener('input', filterTable);
document.getElementById('conferenceFilter').addEventListener('change', function() {
    filterTeamDropdown();
    filterTable();
});
document.getElementById('teamFilter').addEventListener('change', function() {
    filterConferenceDropdown();
    filterTable();
});
document.getElementById('positionFilter').addEventListener('change', filterTable);

function normalizePosition(pos) {
    return pos.toUpperCase().replace(/\s+|-/g, '');
}

function filterTeamDropdown() {
    var conf = document.getElementById('conferenceFilter').value;
    var teamSelect = document.getElementById('teamFilter');
    for (var i = 0; i < teamSelect.options.length; i++) {
        var opt = teamSelect.options[i];
        if (!conf || !opt.value) {
            opt.style.display = '';
        } else {
            opt.style.display = (opt.getAttribute('data-conf') === conf) ? '' : 'none';
        }
    }
    // If selected team doesn't match, reset
    if (teamSelect.value && teamSelect.options[teamSelect.selectedIndex].style.display === 'none') {
        teamSelect.value = '';
    }
}

function filterConferenceDropdown() {
    var team = document.getElementById('teamFilter').value;
    var confSelect = document.getElementById('conferenceFilter');
    if (!team) {
        for (var i = 0; i < confSelect.options.length; i++) {
            confSelect.options[i].style.display = '';
        }
        return;
    }
    var conf = '';
    var teamOpt = document.querySelector('#teamFilter option[value="' + team.replace(/"/g, '\"') + '"]');
    if (teamOpt) conf = teamOpt.getAttribute('data-conf');
    for (var i = 0; i < confSelect.options.length; i++) {
        var opt = confSelect.options[i];
        if (!opt.value) {
            opt.style.display = '';
        } else {
            opt.style.display = (opt.value === conf) ? '' : 'none';
        }
    }
    // If selected conf doesn't match, reset
    if (confSelect.value && confSelect.options[confSelect.selectedIndex].style.display === 'none') {
        confSelect.value = '';
    }
}

function filterTable() {
    var search = document.getElementById('playerSearch').value.toLowerCase();
    var conference = document.getElementById('conferenceFilter').value;
    var team = document.getElementById('teamFilter').value;
    var positionGroup = document.getElementById('positionFilter').value;
    var rows = document.querySelectorAll('#playersTable tbody tr');
    // Find column indexes for Team and Position dynamically
    var headerCells = document.querySelectorAll('#playersTable thead th');
    var teamIdx = -1, posIdx = -1;
    headerCells.forEach(function(h, i) {
        if (h.textContent.trim() === 'Team') teamIdx = i;
        if (h.textContent.trim() === 'Position') posIdx = i;
    });
    rows.forEach(function(row) {
        var text = row.textContent.toLowerCase();
        var normalizedText = text.replace(/\s+/g, '');
        var normalizedSearch = search.replace(/\s+/g, '');
        var match = normalizedText.includes(normalizedSearch);
        if (!match && normalizedSearch.length > 1) {
            var i = 0, j = 0;
            while (i < normalizedText.length && j < normalizedSearch.length) {
                if (normalizedText[i] === normalizedSearch[j]) j++;
                i++;
            }
            match = (j === normalizedSearch.length);
        }
        // Conference filter (always last cell)
        var confMatch = true;
        if (conference) {
            var tds = row.querySelectorAll('td');
            var confCell = tds[tds.length - 1];
            confMatch = confCell && confCell.textContent.trim() === conference;
        }
        // Team filter
        var teamMatch = true;
        if (team && teamIdx !== -1) {
            var teamCell = row.querySelectorAll('td')[teamIdx];
            teamMatch = teamCell && teamCell.textContent === team;
        }
        // Position group filter
        var posMatch = true;
        if (positionGroup && posIdx !== -1) {
            var posCell = row.querySelectorAll('td')[posIdx];
            var posVal = posCell ? normalizePosition(posCell.textContent) : '';
            var codes = positionGroups[positionGroup].map(normalizePosition);
            posMatch = codes.some(function(code) {
                return posVal === code;
            });
        }
        row.style.display = (match && confMatch && teamMatch && posMatch) || search === '' ? '' : 'none';
        if (conference && !confMatch) row.style.display = 'none';
        if (team && !teamMatch) row.style.display = 'none';
        if (positionGroup && !posMatch) row.style.display = 'none';
    });
}
</script>