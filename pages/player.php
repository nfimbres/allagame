<body class="d-flex justify-content-center align-items-start" style="background: #f6f6f6;height: 800px;">
    <div class="container d-flex justify-content-center" style="--bs-primary: rgb(8, 41, 114);--bs-primary-rgb: 8,41,114;--bs-secondary: rgb(234, 105, 12);--bs-secondary-rgb: 234,105,12;margin: 10px;padding: 0px;padding-top: 100px;padding-right: 20px;padding-bottom: 20px;padding-left: 20px;height: 800px;margin-right: 0px;margin-left: 0px;">
        <div style="width: 1000px;">
            <?php

            $player = isset($_GET['player']) ? htmlspecialchars($_GET['player']) : null;

            if ($player !== null) {
                echo "<h1 style=\"padding-bottom: 10px;font-family: 'Roboto Condensed';color: var(--bs-primary);\">Player ID: " . $player . "</h1>";
            }
            
            ?>
            <container>
            </container>
        </div>
    </div>
</body>