<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

include('serverconnect.php');

$filterCategory = $_POST['filterCat'];
$sortE = $_POST['sortE'];
$sortC = $_POST['sortC'];

$baseQuery = "SELECT C.categoryName, E.id, E.equipment, E.leftQuantity, E.availability, E.imgID
      FROM eqmanage.equipment E
      LEFT JOIN eqmanage.categories C ON C.id = E.category ";

$orderByQuery = "ORDER BY ";

if ($filterCategory == null or $filterCategory == 0) {
    $orderByQuery .= ($sortC == 1) ? "C.categoryName ASC, " : "C.categoryName DESC, ";
    $orderByQuery .= ($sortE == 1) ? "E.equipment ASC" : "E.equipment DESC";
} elseif ($filterCategory != null) {
    $baseQuery .= "WHERE C.id = " . $filterCategory . " ";
    $orderByQuery .= ($sortE == 1) ? "E.equipment ASC" : "E.equipment DESC";
} else {
    // If no sort order is selected, set a default order
    $orderByQuery .= "C.categoryName ASC, E.equipment ASC";
}

$executeQuery = $baseQuery . $orderByQuery;
$executeResult = mysqli_query($db, $executeQuery);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Add your HTML head content here (e.g., meta tags, stylesheets, etc.) -->
</head>

<body>
    <div class="container">
        <!-- Add your HTML body content here -->
        <div class="row">
            <?php
            while ($row = mysqli_fetch_array($executeResult)) {
                echo "<div class=\"col-sm-6 col-md-5 col-lg-4 item\">";
                $availability = isset($row['availability']) ? $row['availability'] : null; // Check if 'availability' key exists

                echo ($availability == 1) ? "<div class=\"box\" id='box2'><img src=\"assets/images/" . $row['imgID'] . ".png\" style='width: 100px;height:100px'><br>" :
                    "<div class=\"box\" id='box2'>";

                echo ($availability == 1) ? "<a style='font-style: italic; text-decoration: underline'>" . $row['categoryName'] . "<a/><h3 class=\"name\">" . $row['equipment'] . "</h3>" :
                    "<a style='font-style: italic; text-decoration: underline;color: red'>" . $row['categoryName'] . "<a/><h3 class=\"name\" style='color: red'>" . $row['equipment'] . "</h3>";

                echo ($availability == 1) ? "<p class=\"description\">" . $row['leftQuantity'] . " Available" :
                    "<p class=\"description\" style='color: red'>Not Available";

                echo "</p>";

                if ($availability == 1) {
                    echo "<a href=\"checkout.php?select=" . $row['id'] . "\" class=\"learn-more\">Borrow This Equipment »</a><br>";
                } elseif ($availability == 0) {
                    $query = "SELECT expectedReturnDate FROM EqManage.log WHERE log.equipment_id =" . $row['id'] . " AND returnDate IS NULL ORDER BY expectedReturnDate ASC";
                    if ($results = mysqli_query($db, $query)) {
                        mysqli_data_seek($results, 0);
                        $row = mysqli_fetch_row($results);
                        $date = date('M-d H:i', strtotime($row[0]));
                        if ($row[0] != null) {
                            echo "<p class=\"description\" style='color: red'>Expected to be available at: " . $date;
                        } else {
                            echo "<p class=\"description\" style='color: red'>Availability date not determined yet";
                        }
                    }
                }

                if ($availability == 1) {
                    echo "Quantity: <input type=\"number\" min=\"1\" max=" . $row['leftQuantity'] . " name=\"quantity\" id=" . $row['id'] . "_qty" . " style=\"margin-bottom: 15px;\" value=\"1\" />
                        <button id=\"add-cart\" class='btn trigger_button' style='font-size: 12px; margin: 5px' value=" . $row['id'] . " onclick='addCart(" . $row['id'] . ")'>Add To Cart</button>";
                }

                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
    </div>
</body>

</html>
