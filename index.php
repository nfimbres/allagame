<?php include 'header.html'; ?>

<div style="height:4rem"></div>

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