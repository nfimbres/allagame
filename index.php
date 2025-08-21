<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="All A Game - Your source for basketball stats, news, and home of the It's All A Game Podcast.">
    <title>AllAGame</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Condensed&amp;display=swap">
    <link rel="icon" href="assets/img/aag-logo.png" type="image/x-icon">
    <link rel="shortcut icon" href="assets/img/aag-logo.png" type="image/x-icon">
</head>

<body>

    <?php include 'html/header.html'; ?>

    <main>

        <?php

        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        } else {
            $page = 'home';
        }

        switch ($page) {
            case 'home':
                include 'pages/home.php';
                break;
            case 'episodes':
                include 'pages/episodes.php';
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
            case 'team':
                include 'pages/team.php';
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
        --primary: rgba(8, 41, 114, 1);
        --secondary: rgb(234, 105, 12);
        --background: #f0f0f0ff;

        --phi-primary: rgba(0, 64, 168, 1);
        --phi-secondary: rgba(224, 0, 48, 1);
        --phi-tertiary: white;

        --mil-primary: rgba(40, 80, 48, 1);
        --mil-secondary: rgba(216, 208, 160, 1);
        --mil-tertiary: rgba(0, 96, 184, 1);

        --chi-primary: rgba(184, 16, 48, 1);
        --chi-secondary: black;
        --chi-tertiary: white;

        --cle-primary: rgba(112, 40, 64, 1);
        --cle-secondary: rgba(200, 152, 104, 1);
        --cle-tertiary: black;

        --bos-primary: rgba(0, 120, 48, 1);
        --bos-secondary: black;
        --bos-tertiary: white;

        --lac-primary: rgba(16, 32, 64, 1);
        --lac-secondary: rgba(216, 0, 48, 1);
        --lac-tertiary: white;

        --mem-primary: rgba(8, 32, 64, 1);
        --mem-secondary: rgba(128, 160, 192, 1);
        --mem-tertiary: rgba(240, 208, 88, 1);

        --atl-primary: rgba(201, 16, 46, 1);
        --atl-secondary: rgba(255, 200, 47, 1);
        --atl-tertiary: white;

        --mia-primary: rgba(134, 38, 50, 1);
        --mia-secondary: black;
        --mia-tertiary: white;

        --cha-primary: rgba(0, 120, 139, 1);
        --cha-secondary: rgba(32, 22, 69, 1);
        --cha-tertiary: white;

        --uta-primary: rgba(48, 3, 112, 1);
        --uta-secondary: black;
        --uta-tertiary: white;

        --sac-primary: rgba(89, 43, 131, 1);
        --sac-secondary: black;
        --sac-tertiary: white;

        --nyk-primary: rgba(0, 62, 164, 1);
        --nyk-secondary: rgba(255, 102, 31, 1);
        --nyk-tertiary: white;

        --lal-primary: #612786;
        --lal-secondary: #ffc62b;
        --lal-tertiary: white;

        --orl-primary: #0150b5;
        --orl-secondary: black;
        --orl-tertiary: white;

        --dal-primary: #004fb4;
        --dal-secondary: #0b233f;
        --dal-tertiary: white;

        --bkn-primary: black;
        --bkn-secondary: white;
        --bkn-tertiary: #717371;

        --den-primary: #0b233f;
        --den-secondary: #852532;
        --den-tertiary: #ffc72b;

        --ind-primary: #031e41;
        --ind-secondary: #ffc52c;
        --ind-tertiary: white;

        --nop-primary: #0b233f;
        --nop-secondary: #c9102d;
        --nop-tertiary: #c79a69;

        --det-primary: #003ea5;
        --det-secondary: #d30231;
        --det-tertiary: white;

        --tor-primary: #bb0d2f;
        --tor-secondary: black;
        --tor-tertiary: white;

        --hou-primary: #bc0c30;
        --hou-secondary: black;
        --hou-tertiary: white;

        --sas-primary: black;
        --sas-secondary: #ced7db;
        --sas-tertiary: white;

        --phx-primary: #221745;
        --phx-secondary: #ca5e16;
        --phx-tertiary: white;

        --okc-primary: #0072cd;
        --okc-secondary: #f8423c;
        --okc-tertiary: #ffb81d;

        --min-primary: #0c243f;
        --min-secondary: #246192;
        --min-tertiary: #78be21;

        --por-primary: #ca0c30;
        --por-secondary: black;
        --por-tertiary: white;

        --gsw-primary: #1b459a;
        --gsw-secondary: #fec92c;
        --gsw-tertiary: white;

        --was-primary: #0b233e;
        --was-secondary: #c8102d;
        --was-tertiary: white;
    }

    body {
        background-color: var(--background);
        min-height: 100vh;
        width: 100vw;
        margin: 0;
    }

    main {
        min-height: 100vh;
        margin: 0;
        padding: 1rem;
        padding-top: 6rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        overflow-x: auto;
    }

    table {
        font-family: 'Roboto Condensed';
        max-width: 100%;
        font-size: .875rem;
        border-collapse: collapse;
    }

    tr {
        background-color: white;
        border-bottom: 2px solid #f6f6f6;
    }

    tr:hover {
        background-color: var(--background);
    }

    thead tr:hover {
        background-color: white;
    }

    td {
        color: black;
        padding-left: .25rem;
        padding-right: .75rem;
        padding-top: .25rem;
        padding-bottom: .25rem;
        border-left: 0;
        border-right: 0;
        text-align: left;
        white-space: nowrap;
        min-width: 1.5rem;
    }

    th {
        text-align: left;
        padding: .4rem;
        padding-left: .25rem;
        padding-right: .75rem;
    }

    a.table-link:link,
    a.table-link:visited,
    a.table-link:hover {
        color: black;
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

    a,
    a:visited {
        color: inherit;
        text-decoration: none;
    }

    .table-background {
        overflow-x: auto;
        background-color: white;
        padding: 2rem;
        padding-top: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: 1rem;
    }

    .table-background h2 {
        color: black;
        padding-bottom: 1.5rem;
    }
</style>