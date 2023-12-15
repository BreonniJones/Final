<?php
session_start();
if(!isset($_SESSION['loggedin'])){
    header('Location: login.php');
    exit();
}
if ($_SESSION['username'] != 'administrator'){
    header('Location: index.php?adminonly=1');
}
include ('serverconnect.php');

// Initialize filter and range variables
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;
$range = isset($_GET['range']) ? $_GET['range'] : null;
$userID = isset($_GET['user']) ? $_GET['user'] : null;
?>
<!DOCTYPE html>
<html>
<?php
include('header.php')
?>
<body class="form-v8 loggedin" id="fade">

<div id="loader">
    <div class="loader"><div></div><div></div><div></div><div></div></div>
</div>

<?php include('adminNavbar.php'); ?>

<div class="content">
    <div style="height: 63px; opacity: 0; padding: 0; margin: 0" ></div>


    <div class="limiter" style="padding-top: 0;">
        <h2 style="padding-bottom: 10px; margin-bottom: 20px">Log</h2>
        <div class="select-box">
            <label for="select-box1" class="label select-box1"><span class="label-desc">Filter By: </span> </label>
            <label for="checkoutDateRadio">
                <input type="radio" id="checkoutDateRadio" name="filter" value="0" <?php if ($filter == 0 || $filter === null) echo 'checked = "checked"'; ?>  onclick="changeOption();"> Checkout Date</label>
            <label for="returnDateRadio">
                <input type="radio" id="returnDateRadio" name="filter" value="1" <?php if ($filter == 1) echo 'checked = "checked"'; ?> onclick="changeOption();"> Return Date</label>
           <br>
            <label for="select-box1" class="label select-box1" id="selectLabel">Show checkout from: </label>
            <select id="select-box1" class="select" name="filtercat" onchange="changeOption()" style="width: 15%">
                <option value="0" <?php if ($range == 0 || $range === null) echo 'selected'; ?>>--All Time--</option>
                <option value="1" <?php if ($range == 1) echo 'selected'; ?>>Today</option>
                <option value="2" <?php if ($range == 2) echo 'selected'; ?>>Yesterday</option>
                <option value="3" <?php if ($range == 3) echo 'selected'; ?>>Past 7 days</option>
            </select>
            <br>
            <label for="filterUser">Filter by user ID (Enter nothing or 0 to reset): </label>
            <input id="filterUser" type="number" style="width: 50px;" value="<?php echo $userID; ?>">
            <button class="btn" style="height: 30px; font-size: 13px" onclick="filterClick()">Filter</button>

        </div>
        <div class="container-table100">
            <div class="wrap-table100">
                <div class="table100">
                    <table>
                        <thead>
                        <tr class="table100-head">
                            <th class="column1" style="border-bottom: 1px solid black">Log ID</th>
                            <th class="column5" style="border-bottom: 1px solid black">(User ID) Name</th>
                            <th class="column2" style="border-bottom: 1px solid black">Checkout ID</th>
                            <th class="column4" style="border-bottom: 1px solid black">Equipment ID</th>
                            <th class="column6" style="border-bottom: 1px solid black">Check Out Date</th>
                            <th class="column6" style="border-bottom: 1px solid black">Expd. Return Date</th>
                            <th class="column6" style="border-bottom: 1px solid black">Return Date</th>

                        </tr>
                        </thead>
                        <tbody id="table">
                       <?php include('fetchLogTable.php'); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if ($_SESSION['username'] == 'administrator'){
    include ('adminModal.php');
}
?>

<script>
    function changeOption() {
        var radioSelection = 0;
        var r = document.getElementById("selectLabel");
        
        if(document.getElementById('checkoutDateRadio').checked) {
            r.innerText = "Show checkout from: ";
        } else if(document.getElementById('returnDateRadio').checked) {
            r.innerText = "Show from return date: ";
        }

        var e = document.getElementById("select-box1");
        var selectvalue = e.options[e.selectedIndex].value;
        var radios = document.getElementsByName("filter");
        var userID = document.getElementById("filterUser").value;

        for (var i = 0, length = radios.length; i < length; i++) {
            if (radios[i].checked) {
                radioSelection = radios[i].value;
                break;
            }
        }

        var url = 'fetchLogTable.php?' + 'filter=' + selectvalue + '&' + 'range=' + radioSelection + '&' + 'user=' + userID;

        document.getElementById("table").innerHTML = "<tr><td colspan='7' style='text-align: center;'>Loading...</td></tr>";

        // You can use AJAX to fetch data and update the table here
        // Example:
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                document.getElementById("table").innerHTML = xhr.responseText;
            }
        };
        xhr.send();

        console.log("URL: " + url);
    }

    function filterClick() {
        changeOption();
    }

    window.onload = function () {
        changeOption();
    };
</script>

</body>
</html>
