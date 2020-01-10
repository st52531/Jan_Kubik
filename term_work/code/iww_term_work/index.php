<?php
ob_start();
session_start();
include "./config.php"; //load configurations
require_once "./code/functions.php";
require_once "./class/BookRepository.php";
$cartEmpty = true;
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/layout.css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Bookworm</title>
</head>
<body>

<header>
    <div id="header-web-title"><a href="<?= BASE_URL ?>"><img id="header-logo" src="./images/logo_orange.png"/>Bookworm</a>
    </div>

    <nav id="nav">

        <?php if (!empty($_SESSION["user_id"])) { ?> <!-- Check if the user is logged in -->
            <?php if ($_SESSION["role"] == "administrator") { ?> <!--Check if it's an admin -->
                <a href="<?= BASE_URL . "?page=book_management" ?>"><i class="fa fa-book"></i> Správa knih</a>
                <a href="<?= BASE_URL . "?page=user_management" ?>"><i class="fa fa-user"></i> Správa uživatelů</a>
            <?php } ?>
            <a href="<?= BASE_URL . "?page=account" ?>"><i class="fa fa-user"></i> Přihlášen
                jako <b><?php echo($_SESSION["first_name"] . " " . $_SESSION["last_name"]) ?></b></a>
            <a href="<?= BASE_URL . "?page=logout" ?>"><i class="fa fa-times"></i> Odhlásit</a>
        <?php } else { ?>
            <a href="<?= BASE_URL . "?page=login" ?>"><i class="fa fa-user"></i> Přihlášení</a>
            <a href="<?= BASE_URL . "?page=registration" ?>"><i class="fa fa-handshake-o"></i> Registrace</a>
        <?php } ?>
        <a href="<?= BASE_URL . "?page=contact" ?>"><i class="fa fa-map-marker"></i> Kontakt</a>
    </nav>
</header>

<section id="hero">
    <span id="category-btn" onclick="location.href='<?= BASE_URL . "?page=book_selection"?>'" onmouseover="openNav()" onmouseleave="closeNav()">&#9776; Nabídka knih</span>
    <div id="search-container">
        <form action="/action_page.php">
            <input type="search" placeholder="Zadejte název knihy, autora..." name="search">
            <button type="submit"><i class="fa fa-search"></i></button>
        </form>
    </div>
    <a href="<?= BASE_URL . "?page=cart" ?>"><i class="fa fa-shopping-cart"></i> Košík</a>
</section>

<div id="category-menu" onmouseover="openNav()" onmouseleave="closeNav()">
    <?php
    $genres = CustomFunctions::getAllBookGenres();
    $index = 0;
    $count = count($genres);
    foreach ($genres AS $genre_row) {
        echo("<a href=" . BASE_URL . "?page=book_selection&genre=". $genre_row["id"] . ">"
            . $genre_row["name"] . "<i class=\"fa fa-chevron-right\"></i></a>");

        if($index<$count-1) //add thematic break after each entry apart from the last one
            echo "<hr>";
        $index++;
    }
    ?>
</div>

<main>
    <?php
    if (isset($_GET["message"]))
        echo $_GET["message"];

    if (isset($_GET['page'])) {
        $file = "./page/" . $_GET["page"] . ".php";
        if (file_exists($file)) {
            include $file;
            //header ('Location: ' . $file);
        }
    } else {
        include "./page/default.php";
    }

    if (isset($_GET["action"])) {
        //user management
        if ($_GET["action"] == "user_insert")
            include "./users/user_insert.php";
        else if ($_GET["action"] == "user_delete")
            include "./users/user_delete.php";
        else if ($_GET["action"] == "user_update")
            include "./users/user_update.php";
        else if ($_GET["action"] == "user_delete_all")
            include "./users/user_delete_all.php";
        //book management
        else if ($_GET["action"] == "book_import")
            include "./books/book_import.php";
        else if ($_GET["action"] == "book_export")
            include "./books/book_export.php";
        else if ($_GET["action"] == "book_insert")
            include "./books/book_insert.php";
        else if ($_GET["action"] == "book_update")
            include "./books/book_update.php";
        else if ($_GET["action"] == "book_delete")
            include "./books/book_delete.php";
        else if ($_GET["action"] == "book_delete_all")
            include "./books/book_delete_all.php";

    }

    ?>
</main>
<footer>
    <div>
        <p>Copyright
            ©<?php echo date("Y"); ?>
            Bookworm
        </p>
    </div>
    <div>
        <p><a id="export-btn" onclick="return confirm('Informace o knihách budou staženy ve formátu JSON. Přejete si pokračovat?')"
              href="<?= BASE_URL . "?action=book_export&message=<p><b class='color-green'>Soubor byl stažen.</b></p>"?>"
              >Export knih</a></p>
    </div>
</footer>
</body>
</html>


