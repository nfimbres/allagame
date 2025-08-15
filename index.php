<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>AllAGame</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Condensed&amp;display=swap">
    <!-- <link rel="stylesheet" href="assets/css/css/header-footer.css"> -->
</head>

<body>

<?php include 'html/header.html'; ?>

<main>

<?php

if(isset($_GET['page'])){
    $page = $_GET['page'];
} else {
    $page = 'home';
}

switch($page) {
    case 'home':
        include 'pages/home.php';
        break;
    case 'stats':
        include 'pages/stats.php';
        break;
    case 'players':
        include 'pages/players.php';
        break;
    case 'player':
        include 'pages/player.php';
        break;
    case 'teams':
        include 'pages/teams.php';
        break;
    case 'aboutus':
        include 'pages/aboutus.php';
        break;
    default:
        include 'pages/404.php';
}

?>

</main>

<?php include 'html/footer.html'; ?>

</body>
</html>


<style>
:root {
--primary: rgb(8,41,114);
--secondary: rgb(234,105,12);
--background: #f6f6f6;
}

body {
  padding-top: 6rem;
  background-color: var(--background);
  min-height: 100vh;
  width: 100vw;
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
    width: 1000px;
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
    margin: 0;
}

h2 {
    font-family: 'Roboto Condensed';
    color: var(--primary);
    margin: 0;
}

</style>