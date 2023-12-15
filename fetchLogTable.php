<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['username'] != 'administrator') {
    header('Location: index.php?adminonly=1');
    exit();
}

include('serverconnect.php');

$range = isset($_GET['filter']) ? $_GET['filter'] : 0;
$filter = isset($_GET['range']) ? $_GET['range'] : 0;
$userID = isset($_GET['user']) ? $_GET['user'] : 0;

function buildLogQuery($db, $filter, $range, $userID) {
    $query = "SELECT log.id AS logid, log.users_id, u.fullname, log.checkoutRequests_id, log.equipment_id, log.checkoutDate, log.returnDate, log.expectedReturnDate 
              FROM eqmanage.log 
              LEFT JOIN users u ON log.users_id = u.id";

    switch ($filter) {
        case 0:
            $dateField = 'checkoutDate';
            break;
        case 1:
            $dateField = 'returnDate';
            break;
        default:
            return null; // Invalid filter
    }

    $dateFilter = getDateFilter($range, $dateField);
    
    if ($userID != 0) {
        $query .= " WHERE log.users_id = ?";
    }

    $query .= $dateFilter;
    $query .= " ORDER BY log.id ASC";

    return $query;
}

function getDateFilter($range, $dateField) {
    $date = '';
    switch ($range) {
        case 0:
            // All time
            break;
        case 1:
            $date = date('Y-m-d');
            break;
        case 2:
            $date = date('Y-m-d', strtotime('yesterday'));
            break;
        case 3:
            $date = date('Y-m-d', strtotime('-7 days'));
            break;
        default:
            return ''; // Invalid range
    }

    return " AND DATE($dateField) = '$date'";
}

$query = buildLogQuery($db, $filter, $range, $userID);

if ($query !== null) {
    if ($stmt = mysqli_prepare($db, $query)) {
        if ($userID != 0) {
            mysqli_stmt_bind_param($stmt, 'i', $userID);
        }
        
        mysqli_stmt_execute($stmt);
        $results = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($results) > 0) {
            while ($row = mysqli_fetch_assoc($results)) {
                // Output table rows here
                echo '<tr>';
                echo '<td>' . $row['logid'] . '</td>';
                echo '<td>(' . $row['users_id'] . ') ' . $row['fullname'] . '</td>';
                echo '<td>' . $row['checkoutRequests_id'] . '</td>';
                echo '<td>' . $row['equipment_id'] . '</td>';
                echo '<td>' . $row['checkoutDate'] . '</td>';
                echo '<td>' . $row['expectedReturnDate'] . '</td>';
                echo '<td>' . $row['returnDate'] . '</td>';
                echo '</tr>';
            }
        } else {
            echo "No records";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "Query preparation failed";
    }
} else {
    echo "Invalid filter or range";
}

// Close the database connection
mysqli_close($db);
?>
