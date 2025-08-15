<html>
<head>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Condensed&amp;display=swap">
</head>
<body><main>
<?php
include __DIR__ . '/../../private_html/db-connection.php';

// Handle form submission for all players
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playerId']) && is_array($_POST['playerId'])) {
    $updated = 0;
    foreach ($_POST['playerId'] as $playerId) {
        $position1 = isset($_POST['position1'][$playerId]) ? $_POST['position1'][$playerId] : '';
        $position2 = isset($_POST['position2'][$playerId]) ? $_POST['position2'][$playerId] : '';
        $stmt = $conn->prepare("UPDATE players SET position1 = ?, position2 = ? WHERE playerId = ?");
        $stmt->bind_param('sss', $position1, $position2, $playerId);
        $stmt->execute();
        $stmt->close();
        $updated++;
    }
    echo "<p style='color:green;'>Updated $updated player positions!</p>";
}

// Fetch all players
$sql = "SELECT playerId, fullName, height, weight, position1, position2 FROM players ORDER BY draftPick ASC, lastName ASC, firstName ASC";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<p>No players found.</p>";
    return;
}

// Table header and form
echo "<h1 style='font-family: Roboto Condensed; color: var(--primary);'>Edit Player Positions</h1>";
echo "<form method='POST' action=''><table style='width:100%; border-collapse:collapse;'>";
echo "<thead><tr class='table-header'><th>Player</th><th>Height</th><th>Weight</th><th>Position 1</th><th>Position 2</th></tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    $playerId = htmlspecialchars($row['playerId']);
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
    echo "<td>" . htmlspecialchars($row['height']) . "</td>";
    echo "<td>" . htmlspecialchars($row['weight']) . "</td>";
    echo "<input type='hidden' name='playerId[]' value='$playerId'>";
    echo "<td><input type='text' name='position1[$playerId]' value='" . htmlspecialchars($row['position1']) . "' style='width:80px;'></td>";
    echo "<td><input type='text' name='position2[$playerId]' value='" . htmlspecialchars($row['position2']) . "' style='width:80px;'></td>";
    echo "</tr>";
}

echo "</tbody></table><br><button type='submit' style='font-size:1rem;padding:0.5rem 1.5rem;'>Update All</button></form>";
?>
</body></main></html>
<style>
:root {
--primary: rgb(8,41,114);
--secondary: rgb(234,105,12);
--background: #f6f6f6;
}

body {
  padding-top: 4rem;
  background-color: var(--background);
  min-height: 100vh;
  width: 50vw;
  margin: 0;
}

main {
    min-height: 100vh;
    margin: 0;
    padding: 1rem;
    padding-top: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
}

table {
    font-family: 'Roboto Condensed';
    font-size: .875rem;
    width: fit-content;
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
    min-width: 3rem;
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

h1 {
    font-family: 'Roboto Condensed';
    color: var(--primary);
}

</style>