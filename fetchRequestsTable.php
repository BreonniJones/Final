<?php
if (session_status() === PHP_SESSION_NONE) {
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

// Initialize variables with default values
$approved = isset($_REQUEST['approved']) ? $_REQUEST['approved'] : 'false';
$rejected = isset($_REQUEST['rejected']) ? $_REQUEST['rejected'] : 'false';
$waiting = isset($_REQUEST['waiting']) ? $_REQUEST['waiting'] : 'false';

// Build the SQL query based on the selected filters
$query = "SELECT requests.id, requests.users_id, u.fullname, e.equipment, requests.equipment_id, requests.location, requests.purpose, requests.requestDate, requests.state, requests.hash, requests.checkoutQty 
          FROM eqmanage.requests 
          LEFT JOIN users u ON requests.users_id = u.id 
          LEFT JOIN equipment e ON requests.equipment_id = e.id ";

if ($approved == 'true') {
    if ($rejected == 'true') {
        if ($waiting == 'true') {
            // No additional WHERE condition needed (true, true, true)
        } else {
            $query .= "WHERE state = 'approved' OR state = 'rejected'"; // (true, true, false)
        }
    } elseif ($rejected == 'false') {
        if ($waiting == 'true') {
            $query .= "WHERE state = 'approved' OR state = 'waiting'"; // (true, false, true)
        } else {
            $query .= "WHERE state = 'approved'"; // (true, false, false)
        }
    }
} elseif ($approved == 'false') {
    if ($rejected == 'true') {
        if ($waiting == 'true') {
            $query .= "WHERE state = 'rejected' OR state = 'waiting'"; // (false, true, true)
        } else {
            $query .= "WHERE state = 'rejected'"; // (false, true, false)
        }
    }
    if ($rejected == 'false') {
        if ($waiting == 'true') {
            $query .= "WHERE state = 'waiting'"; // (false, false, true)
        } else {
            $query .= ""; // (false, false, false) - No additional WHERE condition needed
        }
    }
}

$query .= " ORDER BY eqmanage.requests.id ASC";
$results = mysqli_query($db, $query);

if ($results != null) {
    while ($row = mysqli_fetch_assoc($results)) {
        // Output table rows as you were doing before
        // ...
    }
} elseif ($rejected != 'false' && $approved != 'false' && $waiting != 'false') {
    echo "No Records";
} elseif ($results == null) {
    echo "";
}

// Close the database connection
mysqli_close($db);
?>
