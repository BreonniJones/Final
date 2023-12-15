<?php
session_start();
include('serverconnect.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $loginuser = $_SESSION['id'];
    $equipmentID = $_POST['equipment'];
    $equser = $_POST['userid'];
    $date = date('Y-m-d H:i:s');
    $checkoutRequestsID = $_POST['checkoutRequestsID'];

    echo $equipmentID;
    echo "loginuser", $loginuser;
    echo "Eq", $equser;
    echo $checkoutRequestsID;

    if ($loginuser == $equser) {

        $checkQtyQuery = "SELECT * from eqmanage.equipment where id = '$equipmentID'";
        $checkQtyResult = mysqli_query($db, $checkQtyQuery);
        $checkQtyArray = mysqli_fetch_assoc($checkQtyResult);

        $leftQty = $checkQtyArray['leftQuantity'];
        echo $leftQty;

        $checkoutQtyQuery = "SELECT * from eqmanage.requests where id='$checkoutRequestsID'";
        $checkoutQtyResult = mysqli_query($db, $checkoutQtyQuery);
        $checkoutQtyArray = mysqli_fetch_assoc($checkoutQtyResult);

        $checkoutQty = $checkoutQtyArray['checkoutQty'];

        $newQty = $leftQty + 1;

        $statusupdate = "UPDATE eqmanage.equipment SET leftQuantity='$checkoutQty' where id='$equipmentID'";

        $loginsert = "UPDATE *eqmanage.log set returnDate = '$date' where checkoutRequests_id='$checkoutRequestsID'";

        mysqli_query($db, $statusupdate);
        mysqli_query($db, $loginsert);

        header('Location: index.php?return=1');


    } else header('Location: index.php?return=0');

}





?>
