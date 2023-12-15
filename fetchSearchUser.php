<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['username'] != 'administrator') {
    header('Location: index.php?adminonly=1');
}

include('serverconnect.php');

$userID = isset($_GET['id']) ? $_GET['id'] : null;

// Initialize $overdue to 0 by default
$overdue = 0;

if ($userID !== null) {
    $query = "SELECT users.fullname, l.returnDate, l.checkoutRequests_id, l.expectedReturnDate 
              FROM eqmanage.users 
              LEFT JOIN log l ON l.users_id = users.id 
              LEFT JOIN equipment e ON e.id = l.equipment_id
              WHERE users.id = $userID";

    $borrowing = 0;
    $borrowed = 0;

    $result = mysqli_query($db, $query);

    if (!$result) {
        die("Error in SQL query: " . mysqli_error($db));
    }

    while ($row = mysqli_fetch_array($result)) {
        if ($row['returnDate'] === null && $row['checkoutRequests_id'] !== null) {
            $borrowing++;
            $borrowed++;
        } elseif ($row['returnDate'] !== null && $row['checkoutRequests_id'] !== null) {
            $borrowed++;
        }

        $today = date("Y-m-d H:i:s");
        $returnDate = $row['expectedReturnDate'];

        if (strtotime($returnDate) < strtotime($today) && $row['checkoutRequests_id'] !== null && $row['returnDate'] === null) {
            $overdue++;
        }
    }
}

echo "<h1>Searching ID: $userID</h1>
<div class=\"row\">
    <div class=\"col-lg-3 col-md-6 col-sm-6\">
        <div class=\"card card-stats\">
            <div class=\"card-header card-header-warning card-header-icon\">
                <h3 class=\"card-category\">Full Name</h3>
                <div id=\"overdue\"><h4 class=\"card-title\">" . ($userID ? $row['fullname'] : '-') . "</h4></div>
            </div>
            <div class=\"card-footer\">
                <div class=\"stats\">
                    <!-- <i class=\"material-icons text-danger\">warning</i> -->
                   <!--  <a href=\"overdue.php\">View overdue >></a> -->
                </div>
            </div>
        </div>
    </div>

    <div class=\"col-lg-3 col-md-6 col-sm-6\">
        <div class=\"card card-stats\">
            <div class=\"card-header card-header-warning card-header-icon\">
                <h3 class=\"card-category\">Overdue</h3>
                <div id=\"overdue\"><h4 class=\"card-title\">" . ($userID ? $overdue : '-') . "</h4></div>
            </div>
            <div class=\"card-footer\">
                <div class=\"stats\">
                    <!-- <i class=\"material-icons text-danger\">warning</i> -->
                    <a href=\"overdue.php\">View all overdues >></a>
                </div>
            </div>
        </div>
    </div>

    <div class=\"col-lg-3 col-md-6 col-sm-6\">
        <div class=\"card card-stats\">
            <div class=\"card-header card-header-warning card-header-icon\">
                <h3 class=\"card-category\">Currently Borrowing</h3>
                <div id=\"overdue\"><h4 class=\"card-title\">" . ($userID ? $borrowing : '-') . "</h4></div>
            </div>
            <div class=\"card-footer\">
                <div class=\"stats\">
                    <!-- <i class=\"material-icons text-danger\">warning</i> -->
                    <a href=\"log.php\">View all log >></a>
                </div>
            </div>
        </div>
    </div>

    <div class=\"col-lg-3 col-md-6 col-sm-6\">
        <div class=\"card card-stats\">
            <div class=\"card-header card-header-warning card-header-icon\">
                <h3 class=\"card-category\">Borrowed Total</h3>
                <div id=\"overdue\"><h4 class=\"card-title\">" . ($userID ? $borrowed : '-') . "</h4></div>
            </div>
            <div class=\"card-footer\">
                <div class=\"stats\">
                    <!-- <i class=\"material-icons text-danger\">warning</i> -->
                    <a href=\"log.php\">View all log >></a>
                </div>
            </div>
        </div>
    </div>
</div>";
?>
