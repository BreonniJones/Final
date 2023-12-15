<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}
if ($_SESSION['username'] != 'administrator') {
    header('Location: index.php?adminonly=1');
}
?>

<!DOCTYPE html>
<html>
<?php
include('header.php');
?>
<body class="form-v8 loggedin" id="fade">

<div id="loader">
    <div class="loader"><div></div><div></div><div></div><div></div></div>
</div>

<?php

if ($_SESSION['username'] == 'administrator') {
    include('adminNavbar.php');
} else {
    include('navbar.php');
}

include('serverconnect.php');
?>

<div class="content">
    <div style="height: 63px; opacity: 0; padding: 0; margin: 0"></div>
    <div id="addAlert" style="color: green;" class="name">Equipment Added</div>
    <div id="removeAlert" style="color: red;" class="name">Equipment Removed</div>
    <div id="removeCatAlert" style="color: red;" class="name">Category Removed</div>

    <div style="padding-top: 0;">
        <h2 style="padding-bottom: 10px; margin-bottom: 20px">Manage Equipment</h2>

        <!-- Trigger/Open The Modal -->
        <h1 style="font-weight: bold; margin-top: 30px; margin-bottom: 10px; text-align: center"><u>Equipment</u></h1>
        <div id="wrapper" style="box-shadow: none;"><button id="addEqBtn" class="btn">Add Equipment</button></div>

        <!-- The Modal -->
        <div id="addEqModal" class="modal">
            <!-- Modal content -->
            <div class="modal-content" style="width: fit-content">
                <div onclick="hideEqModal()">
                    <span class="close" style="margin-bottom: 10px; float: left">&times;</span>
                </div>
                <?php
                $resultset = mysqli_query($db, "select * from eqmanage.categories");
                ?>
                <div class="select-style" style="width:500px; margin: auto;" align="center">
                    <input type="text" name="name" placeholder="Equipment Name" id="name" required/>
                    Quantity: <input type="number" min="1" max="100" name="quantity" value="1" id="qty"
                                     style="margin-top: 5px;margin-bottom: 5px" required/>
                    <div id="categorySelect"></div>
                    <div class="panel-body" align="center" id="upload-div">
                        <strong>Upload equipment image</strong><input type="file" name="upload_image" id="upload_image"
                                                                    onchange="imageCrop(this)" />
                        <br />
                        <div id="uploaded_image"></div>
                    </div>
                    <input id="add" name="request" type="submit" value="Add Equipment" style="width: 100%;">
                </div>
            </div>
        </div>

        <div id="uploadimageModal" class="modal" role="dialog" style="z-index: 10000">
            <div class="modal-content" style="width:fit-content">
                <h4 class="modal-title" style="float: left"></h4>
                <div class="row">
                    <div class="col-md-7 text-center">
                        <div id="image" style="width:350px; margin-top:30px"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-info crop_image">Crop & Upload Image</button>
                </div>
            </div>
        </div>

        <?php $results = mysqli_query($db, "SELECT * FROM eqmanage.equipment inner join eqmanage.categories on equipment.category = categories.id"); ?>

        <table width="100%" id="table">
            <thead>
            <tr>
                <th scope="col">Item</th>
                <th scope="col">Category</th>
                <th scope="col">Total Qty</th>
                <th scope="col">Left Qty</th>
                <th scope="col">Availability</th>
                <th scope="col">Last used user ID</th>
                <th scope="col">Last log ID</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="table2">
            </tbody>
        </table>
        <h1 style="font-weight: bold; margin-top: 30px;margin-bottom: 10px;text-align: center"><u>Categories</u></h1>
        <table width="100%" id="table">
            <thead>
            <tr>
                <th scope="col">Category Name</th>
                <th scope="col">Number of equipment in this category</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody id="cattable">
            </tbody>
        </table>
    </div>
</div>

<?php
if ($_SESSION['username'] == 'administrator') {
    include('adminModal.php');
}
?>
<script>
    var addAlert = document.getElementById("addAlert");
    addAlert.style.display = "none";
    var removeAlert = document.getElementById("removeAlert");
    removeAlert.style.display = "none";
    var removeCatAlert = document.getElementById("removeCatAlert");
    removeCatAlert.style.display = "none";
    var addEqModal = document.getElementById("addEqModal");
    var addEqBtn = document.getElementById("addEqBtn");
    addEqBtn.onclick = function() {
        addEqModal.style.display = "block";
    };

    function hideEqModal(){
        addEqModal.style.display = "none";
        resetFields();
    }

    window.onclick = function(event) {
        if (event.target === addEqModal) {
            addEqModal.style.display = "none";
            resetFields();
        }
    };

    function selectOther(val){
        var element=document.getElementById('other');
        if(val==='Select the Category'||val==='Other')
            element.style.display='block';
        else
            element.style.display='none';
    }

    $(document).ready(function(){
        displayFromDatabase();
        $("#categorySelect").load("fetchCategorySelect.php");
        $("#add").click(function (e){
            var name = document.getElementById("name").value;
            var qty = document.getElementById("qty").value;
            var e = document.getElementById("cat");
            var cat = e.options[e.selectedIndex].value;
            var ncat = document.getElementById("other").value;
            var img = document.getElementById("eqImg").value;
            $.ajax({
                url: "editEq.php",
                type: "POST",
                async: false,
                data:{
                    "add":1,
                    "name":name,
                    "quantity":qty,
                    "category_id":cat,
                    "other":ncat,
                    "img":img
                },
                success: function(data){
                    displayFromDatabase();
                    addEqModal.style.display = "none";
                    resetFields();
                    var element=document.getElementById('other');
                    element.style.display='none';
                    var addAlert = document.getElementById("addAlert");
                    addAlert.style.display = "block";
                    var removeAlert = document.getElementById("removeAlert");
                    removeAlert.style.display = "none";
                    var removeCatAlert = document.getElementById("removeCatAlert");
                    removeCatAlert.style.display = "none";
                    console.log(data);
                }
            });
        });
    });

    function resetFields() {
        document.getElementById("name").value = "";
        document.getElementById("qty").value = "1";
        document.getElementById("cat").value = "";
        document.getElementById("other").value ="";
        $("#upload-div").load(" #upload-div");
    }

    function displayFromDatabase(){
        $.ajax({
            url: "editEq.php",
            type: "POST",
            async: false,
            data: {
                "display": 1
            },
            success:function (data) {
                $("#table2").html(data);
            }
        })
        $("#cattable").load("fetchCategoryTable.php");
        $("#categorySelect").load("fetchCategorySelect.php");
    }

    function removeEq(id) {
        $.ajax({
            url: "editEq.php",
            type: "POST",
            async: false,
            data:{
                "id":id,
                "remove":1,
                "type":1
            },
            success: function(data){
                displayFromDatabase();
                var removeAlert = document.getElementById("removeAlert");
                removeAlert.style.display = "block";
                var addAlert = document.getElementById("addAlert");
                addAlert.style.display = "none";
                var removeCatAlert = document.getElementById("removeCatAlert");
                removeCatAlert.style.display = "none";
                console.log(data);
            }
        });
    }

    function removeCat(id) {
        $.ajax({
            url: "editEq.php",
            type: "POST",
            async: false,
            data:{
                "id":id,
                "remove":1,
                "type":2
            },
            success: function(data){
                displayFromDatabase();
                var removeCatAlert = document.getElementById("removeCatAlert");
                removeCatAlert.style.display = "block";
                var addAlert = document.getElementById("addAlert");
                addAlert.style.display = "none";
                var removeAlert = document.getElementById("removeAlert");
                removeAlert.style.display = "none";
                console.log(data);
            }
        });
    }

    $image_crop = $('#image').croppie({
        enableExif: true,
        viewport: {
            width: 250,
            height: 250,
            type: 'square'
        },
        boundary:{
            width: 310,
            height: 310
        }
    });

    function imageCrop(image){
        var reader = new FileReader();
        reader.onload = function (event) {
            $image_crop.croppie('bind', {
                url: event.target.result
            }).then(function(){
                console.log('jQuery bind complete');
            });
        };
        reader.readAsDataURL(image.files[0]);
        $('#uploadimageModal').modal('show');
    }

    $('.crop_image').click(function(event){
        $image_crop.croppie('result', {
            type: 'canvas',
            size: 'original'
        }).then(function(response){
            $.ajax({
                url:"upload-image.php",
                type: "POST",
                data:{"image": response},
                success:function(data)
                {
                    $('#uploadimageModal').modal('hide');
                    $('#uploaded_image').html(data);
                }
            });
        })
    });
</script>
