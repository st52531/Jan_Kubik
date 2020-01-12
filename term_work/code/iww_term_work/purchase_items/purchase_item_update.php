<?php
$errorFeedbackArray = array();
$successFeedback = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
// <editor-fold default-state="collapsed" desc="input-validation">
    if (empty($_POST["isbn"])) {
        $feedbackMessage = "Je vyžadován ISBN kód knihy!";
        array_push($errorFeedbackArray, $feedbackMessage);
    }
    if (empty($_POST["quantity"])) {
        $feedbackMessage = "Je vyžadováno množství položek!";
        array_push($errorFeedbackArray, $feedbackMessage);
    }
// </editor-fold>


    if (empty($errorFeedbackArray)) {
        // save data to database
        try {
            $conn = Connection::getPdoInstance();

            $bookRepo = new BookRepository($conn);
            $book = $bookRepo->getByISBN($_POST["isbn"]);

            if (empty($book))
                throw new UnexpectedValueException();

            $price = $book["price"] * $_POST["quantity"];

            $stmt = $conn->prepare("UPDATE purchase_book SET price = :price, quantity = :quantity, book_isbn = :book_isbn
                                               WHERE purchase_book.id = :id");
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':quantity', $_POST["quantity"]);
            $stmt->bindParam(':book_isbn', $book["isbn"]);
            $stmt->bindParam(':id', $_GET["item_id"]);
            $stmt->execute();
            $successFeedback = "Položka objednávky byla upravena.";

        } catch (UnexpectedValueException $e) {
            $feedbackMessage = "<p><b class='color-red'>Zadaný kód ISBN se nenachází v databázi.</b></p>";
            array_push($errorFeedbackArray, $feedbackMessage);
        } catch (Exception $e) {
            $feedbackMessage = "<p><b class='color-red'>Při přidávání nastaly potíže, zkuste to prosím později.</b></p>";
            array_push($errorFeedbackArray, $feedbackMessage);

        }
    }

}

?>

<?php
if (empty($errorFeedbackArray)) { //load origin data from database
    $conn = Connection::getPdoInstance();

    $purchaseItemRepo = new PurchaseBookRepository($conn);
    $purchaseItem = $purchaseItemRepo->getPurchasedItemById($_GET["item_id"]);

    $isbn = $purchaseItem["book_isbn"];
    $quantity = $purchaseItem["quantity"];

} else { //in case of any error, load data
    $isbn = $_POST["isbn"];
    $quantity = $_POST["quantity"];
}
?>

<form id="custom-form" method="post" enctype="multipart/form-data">
    <h2>Upravit knihu</h2>
    <?php
    if (!empty($errorFeedbackArray)) {
        echo "<b class='color-orange'>Při zadávání se vyskytly tyto chyby:</b><br>";
        foreach ($errorFeedbackArray as $errorFeedback) {
            echo "<b class='color-red'>" . $errorFeedback . "</b><br>";
        }
        echo "<br>";
    } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($successFeedback)) {
        header("Location:" . BASE_URL . "?page=purchase_item_management" . "&action=purchase_item_update" . "&message=" . "<br><b class='color-green'>$successFeedback</b><br>" . "&purchase_id=" . $_GET["purchase_id"] . "&item_id=" . $_GET["item_id"]);
    }
    ?>
    <input id="admin-input" type="text" name="isbn" placeholder="ISBN" pattern="([0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{1})" value="<?= $isbn; ?>">
    <input id="admin-input" type="number" name="quantity" placeholder="Množství" min="1" max="50" value="<?= $quantity; ?>">
    <br>
    <input id="custom-submit" type="submit" name="insert_book" value="Upravit">
</form>


