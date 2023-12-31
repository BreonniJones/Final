<?php
include('serverconnect.php');
session_start();
if(!isset($_SESSION['loggedin'])){
    header('Location: login.php');
    exit();
}
if ($_SESSION['username'] != 'administrator'){
    header('Location: index.php?adminonly=1');
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Dashboard</title>
    <?php include('adminHeader.php') ?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <!-- Material Kit CSS -->
    <link href="assets/css/dashboardstyle.css" rel="stylesheet" />
    <script src="assets/js/select2.min.js"></script>
    <link rel="stylesheet" href="assets/css/select2.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="bootstrap-iso.css">
    <script src="assets/js/select2.min.js"></script>
</head>

<body>

<div class="wrapper" id="main">

    <div class="main-panel">
        <!-- Navbar -->
        <?php include('adminNavbar.php')?>

        <!-- End Navbar -->

        <h2 class="text-center">Search</h2>

        <div class="container-fluid bootstrap-iso" id="container">
            <!-- your content here -->
            <div align="center" style="margin-top: 10px">
                <label for="userradio">User</label>
                <input type="radio" id="userradio" style="margin-right: 15px" onclick="displayRadioValue()" name="type" value="1" <?php if(isset($_GET['type']) && $_GET['type'] == 1){echo 'checked="checked"';};  ?>>
                <label for="userradio">Equipment</label>
                <input type="radio" id="userradio" style="margin-right: 15px"  onclick="displayRadioValue()" name="type" value="2" <?php if(isset($_GET['type']) && $_GET['type'] == 2){echo 'checked="checked"';};  ?>>
                <label for="userradio">Log</label>
                <input type="radio" id="userradio" style="margin-right: 15px"  onclick="displayRadioValue()" name="type" value="3" <?php if(isset($_GET['type']) && $_GET['type'] == 3){echo 'checked="checked"';};  ?>>
                <label for="userradio">Request</label>
                <input type="radio" id="userradio" style="margin-right: 15px"  onclick="displayRadioValue()" name="type" value="4" <?php if(isset($_GET['type']) && $_GET['type'] == 4){echo 'checked="checked"';};  ?>>
                <label for="userradio">Category</label>
                <input type="radio" id="userradio" style="margin-right: 15px"  onclick="displayRadioValue()" name="type" value="5" <?php if(isset($_GET['type']) && $_GET['type'] == 5){echo 'checked="checked"';};  ?>>
            </div>

            <?php
            $selected = isset($_GET['type']) ? $_GET['type'] : null;
            if ($selected == 1 || $selected === null){include('searchUser.php');};
            if ($selected == 2){include('searchEq.php');};
            if ($selected == 3){include('searchLog.php');};
            if ($selected == 4){include('searchRequests.php');};
            if ($selected == 5){include('searchCategory.php');};
            ?>
        </div>
    </div>

</div>

</body>

</html>

<script>
    $("#select").select2( {
        placeholder: "Enter ID",
        allowClear: true,
    } );

    $(document).ready(function() {
        $("#userSelect").change(function () {
            var id = $(this).val();
            console.log("Working");
            var url = 'fetchSearchUser.php?' + 'id=' + id;
            console.log(url);
            $("#user").load(url);
            console.log("Done");
        })
    });

    function displayRadioValue() {
        var ele = document.getElementsByName('type');
        var id;
        for(i = 0; i < ele.length; i++) {
            if(ele[i].checked)
                id = ele[i].value;
                console.log(id);
                var url = 'tempSearch.php?' + 'type=' + id;
                console.log(url);
        }
        console.log(url);
        $("#main").load(url);
        console.log('done');
    };
</script>
