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
  padding-top: 4rem;
  background-color: var(--background);
  min-height: 100vh;
  width: 100vw;
  margin: 0;
}

main {
    width: 100vw;
    min-height: 100vh;
    margin: 0;
    padding: 1rem;
    padding-top: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
}

</style>