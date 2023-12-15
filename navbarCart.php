<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}
include('serverconnect.php');

// Check if eqID and qty are set in the POST data
if (isset($_POST['eqID']) && isset($_POST['qty'])) {
    $addEqID = $_POST['eqID'];
    $addQty = $_POST['qty'];

    // Initialize variables
    $index = 0;
    $oldQty = 0;
    $exists = 0;
    $leftQty = 0;

    // Fetch left quantity of the selected equipment
    $prequery = "SELECT * FROM eqmanage.equipment WHERE id = '$addEqID'";
    $queryEq = mysqli_query($db, $prequery);
    while ($row = mysqli_fetch_array($queryEq)) {
        $leftQty = $row['leftQuantity'];
    }

    // Check if cart exists
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $addEqID) {
                $exists = 1;
                $oldQty = $item['qty'];
                $addQty += $oldQty;
                if ($addQty > $leftQty) {
                    $addQty = $leftQty;
                }
                $item['qty'] = $addQty;
                break;
            }
        }

        if ($exists == 0) {
            $_SESSION['cart'][] = array("id" => $addEqID, "qty" => $addQty);
        }
    } else {
        $_SESSION['cart'] = array(array("id" => $addEqID, "qty" => $addQty));
    }
} elseif (isset($_POST['destroy_cart']) && $_POST['destroy_cart'] == 1) {
    // Clear cart selected
    unset($_SESSION['cart']);
} elseif (isset($_POST['delete']) && isset($_POST['eqID']) && $_POST['delete'] == 1) {
    // Delete action called
    $tempArray = $_SESSION['cart'];
    $eqID = $_POST['eqID'];
    $i = 0;
    foreach ($_SESSION['cart'] as $cart) {
        if ($cart['id'] == $eqID) {
            unset($tempArray[$i]);
        }
        $i++;
    }
    $newArray = array_values($tempArray);
    $_SESSION['cart'] = $newArray;
} elseif (isset($_POST['update']) && isset($_POST['qty']) && isset($_POST['eqID']) && $_POST['update'] == "1") {
    // Update action called
    $targetEqID = $_POST['eqID'];
    $replaceQty = $_POST['qty'];
    $leftQty = 0;
    $index = 0;
    $prequery = "SELECT * FROM eqmanage.equipment WHERE id = '$targetEqID'";
    $queryEq = mysqli_query($db, $prequery);
    while ($row = mysqli_fetch_array($queryEq)) {
        $leftQty = $row['leftQuantity'];
    }
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $targetEqID) {
            if ($replaceQty > $leftQty) {
                $replaceQty = $leftQty;
            }
            $item['qty'] = $replaceQty;
        }
        $index++;
    }
}

// Check if $_SESSION['cart'] exists and is an array before using sizeof()
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $max = sizeof($_SESSION['cart']);
} else {
    $max = 0; // Set a default value if $_SESSION['cart'] is not set or not an array
}

echo "<div class=\"row total-header-section\">
    <h2 style='font-weight: bold; font-size: 25px; margin-left: 5%'>Cart</h2>
    <p style='font-weight: lighter;color: grey; margin-left:auto; margin-right: 5%'>$max Equipment Added</p>
</div>";

foreach ($_SESSION['cart'] as $item) {
    $eqID = $item['id'];
    $qty = $item['qty'];

    $eqName = "";
    $prequery = "SELECT * FROM eqmanage.equipment e LEFT JOIN categories c ON e.category = c.id WHERE e.id = $eqID";
    $queryEq = mysqli_query($db, $prequery);

    while ($row = mysqli_fetch_array($queryEq)) {
        echo "<div class='row cart-detail'>";
        echo "<div class='col-lg-4 col-sm-4 col-4 cart-detail-img' id='imgContainer'>";
        echo "<img src=\"assets/images/" . $row['imgID'] . ".png\">";
        echo "</div>";
        echo "<div class='col-lg-8 col-sm-8 col-8 cart-detail-product'>";
        echo "<span class='price text-info'>" . $row["categoryName"] . "</span> <h3 class='name' style='font-weight: bold; font-size: 20px; margin-bottom: 0;'>" . $row['equipment'] . "</h3>";
        echo "<span class='count'> Quantity: </span><input id='cartInput' type='number' min='1' max=" . $row['leftQuantity'] . " name='quantity' id=" . $row['id'] . "_qty" . " value='$qty' style='margin-bottom: 10px' onchange='updateQty($eqID,this.value)'/>";
        echo "<button class='btn btn-danger btn-block' style='display: inline-block; padding-left: 5px; font-size: 12px' onclick='deleteItem($eqID)'>Delete</button>
            </div>
        </div><hr>";
    }
}

function findEqIndex($eqID) {
    $max = sizeof($_SESSION['cart']);
    $index = -1;
    for ($i = 0; $i < $max; $i++) {
        if ($_SESSION['cart'][$i]['eqID'] == $eqID) {
            $index = $i;
        }
    }
    return $index;
}
?>
