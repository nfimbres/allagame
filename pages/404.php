<?php
// 404.php

// Include header (adjust path as needed)
include_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>404</h1>
    <h2>Page Not Found</h2>
    <p>
        Sorry, the page you are looking for does not exist.<br>
        Please check the URL or return to the <a href="/">homepage</a>.
    </p>
</div>

<?php
// Include footer (adjust path as needed)
include_once __DIR__ . '/../includes/footer.php';
?>
<style>
.container {
    font-family: 'Roboto Condensed';
    text-align: center;
    padding: 60px 20px;
}
h1 {
    font-size: 3em;
    color: var(--secondary);
    margin-bottom: 20px;
}
h2 {
    color: #333;
    margin-bottom: 10px;
}
p {
    color: #666;
    margin-bottom: 30px;
}
a {
    color: var(--primary);
}
img {
    max-width: 300px;
    width: 100%;
    margin-bottom: 30px;
}

</style>