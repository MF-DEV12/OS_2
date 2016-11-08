<?php
	 	include('config/db.php');

		$db = new mysqli($server, $user, $pass, $db) or die("Unable to Connect");

		if(isset($_POST['showdisplayitem'])){
				$cnt = 0;
				$sql = "SELECT * FROM item";
				$result = mysqli_query($db, $sql);

				while($record = mysqli_fetch_array($result)){
						if($cnt<16){
								if($cnt % 4 == 0){
										echo "<div class=row>";
								}
										echo "<div class='columns three respo'>
										<figure class=itemGrids onclick=showinfo('" . $record['ItemNo'] . "')>
										<div class=itemPhoto>
										<img class=itemPhoto src=Assets/img/Items/". $record['Image'] ."></div>
										<h4>" . $record['Name'] . "</h4>
										</figure>
										</div>";

								if($cnt % 4 == 3){
										echo "</div>";
								}
								$cnt = $cnt + 1;
						}
				}
				echo "<div><a id=btnSeemore href = 'Order.php'>See More ></a></div>";

				exit();
		}

		if(isset($_POST['showcartitem'])){
				$sql = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant, orderlist WHERE item.ItemNo = orderlist.ItemNo AND item.ItemNo = itemvariant.ItemNo AND orderlist.VariantNo = itemvariant.VariantNo AND Temp = 1 AND itemvariant.Owned =1";
				$result = mysqli_query($db, $sql);

				while($record = mysqli_fetch_array($result)){
                    echo "<tr>
                        <td><a class=remove onclick=removefrom(". $record['ITNO'] .",". $record['VariantNo'] .")>-</a></td> 
                        <td>". $record['Name'] ."<br>". $record['Size'] ." ". $record['Color'] ." ". $record['Description'] ."</td>
                        <td><input type=text value='" . $record['Quantity'] . "' class=quantity onchange='quantityChange(this, " . $record['VariantNo'] . ")' /></td>
                        <td>". $record['Total'] ." </td>
                    </tr>";
				}
				exit();
		}

		if(isset($_POST['displayiteminfo'])){
            $sql = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND item.ItemNo = '". $_POST['itmID'] ."'";
            $result = mysqli_query($db, $sql);
            $record = mysqli_fetch_array($result);

            echo "<div>
                    <h4>Item: ". $record['Name'] ."</h4>
                    <h4>Variant: </h4><select onchange=priceupdate(this)>";
            
            $sql2 = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND itemvariant.Owned = 1 AND item.ItemNo = '". $_POST['itmID'] ."'";
            $result2 = mysqli_query($db, $sql2);
            while($record2 = mysqli_fetch_array($result2)){
                echo "<option value=". $record2['VariantNo'] .">". $record2['Size'] ." ". $record2['Color'] ." ". $record2['Description'] ."</option>";		
            }

            echo "</select>                 
                <div>";

            exit();
		}
        
        if(isset($_POST['displayitemprice'])){
            $sql = "SELECT * FROM itemvariant WHERE Owned = 1 AND VariantNO = '". $_POST['varID'] ."'";
            $result = mysqli_query($db, $sql);
            $record = mysqli_fetch_array($result);
            
            echo "<h4>Price: ". $record['Price'] ." </h4>";
            
            exit();
        }

        if(isset($_POST['displayitembtn'])){
            $sql = "SELECT * FROM itemvariant WHERE Owned = 1 AND VariantNo = '". $_POST['varID'] ."'";
            $result = mysqli_query($db, $sql);
            $record = mysqli_fetch_array($result);
            
            echo "<a onclick=addTo('" . $record['VariantNo'] . "') class=addtocart>Add to Cart</a>";
            
            exit();
        }

        if(isset($_POST['displayitemprice1'])){
            $sql = "SELECT * FROM itemvariant WHERE Owned = 1 AND ItemNo = '". $_POST['itmID'] ."'";
            $result = mysqli_query($db, $sql);
            $record = mysqli_fetch_array($result);
            
            echo "<h4>Price: ". $record['Price'] ." </h4>";
            
            exit();
        }

        if(isset($_POST['displayitembtn1'])){
            $sql = "SELECT * FROM itemvariant WHERE Owned = 1 AND ItemNo = '". $_POST['itmID'] ."'";
            $result = mysqli_query($db, $sql);
            $record = mysqli_fetch_array($result);
            
            echo "<a onclick=addTo('" . $record['VariantNo'] . "') class=addtocart>Add to Cart</a>";
            
            exit();
        }

		if(isset($_POST['addtocart'])){
				$sql = "SELECT ItemNo FROM orderlist WHERE VariantNo = " . $_POST["varID"] . " AND Temp = 1";
				$result = mysqli_query($db, $sql);		

				if(mysqli_num_rows($result) > 0){

				}
				else{
                    $firstsql = "SELECT *, item.ItemNo as ITNO FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND VariantNo =  '" . $_POST["varID"] . "'";
                    
                    $firstresult = mysqli_query($db, $firstsql);
                    $firstfetch = mysqli_fetch_array($firstresult);

                    $sql = "INSERT INTO orderlist(VariantNo, ItemNo, Temp) VALUES('$_POST[varID]','". $firstfetch['ITNO'] ."',1)";
                    $result = mysqli_query($db, $sql);
				}
				$postsql = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant, orderlist WHERE item.ItemNo = orderlist.ItemNo AND item.ItemNo = itemvariant.ItemNo AND orderlist.VariantNo = itemvariant.VariantNo AND Temp = 1 AND itemvariant.Owned =1";
				$postresult = mysqli_query($db, $postsql);

				while($postfetch = mysqli_fetch_array($postresult)){
                    echo "<tr>
                        <td><a class=remove onclick=removefrom(". $postfetch['ITNO'] .",". $postfetch['VariantNo'] .")>-</a></td> 
                        <td>". $postfetch['Name'] ."<br>". $postfetch['Size'] ." ". $postfetch['Color'] ." ". $postfetch['Description'] ."</td>
                        <td><input type=text value='" . $postfetch['Quantity'] . "' class=quantity onchange='quantityChange(this," . $postfetch['VariantNo'] . ")' /></td>
                        <td>". $postfetch['Total'] ." </td>
                    </tr>"; 
				}
				exit();
		}

		if(isset($_POST['quantityupdate'])){
				$presql = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND VariantNo = " . $_POST['itemID'];
				$preresult = mysqli_query($db, $presql);
				$data = mysqli_fetch_array($preresult);

				$sql = "UPDATE orderlist SET Quantity = " . $_POST['quantity'] . ", Total = '" . ($data['Price'] * $_POST['quantity']) . "' WHERE VariantNo = " . $_POST["itemID"] . " AND Temp = 1";
				$result = mysqli_query($db,$sql);
                echo $data['Price'];
                echo $_POST['quantity'];
				if($result){
				    echo "updated succesfully";
				}
				exit();
		}

		if(isset($_POST['totalamount'])){
				$postsql = "SELECT SUM(Total) AS TotalAmount FROM orderlist WHERE Temp = 1";
				$postresult = mysqli_query($db,$postsql);
				$data = mysqli_fetch_array($postresult);

				echo $data['TotalAmount'];

				exit();
		}


		if(isset($_POST['sendcinfo'])){
            $date = date('Y-m-d H:i:s');
            $datetime = date('Y-m-d H:i:s', strtotime($date));

            $presql1 = "SELECT SUM(Total) as TotalAmount FROM orderlist WHERE Temp = 1";
            echo $presql1;
            $preresult1 = mysqli_query($db,$presql1);
            $prefetch1 = mysqli_fetch_array($preresult1);

            $presql = "INSERT INTO tblorder(TotalAmount, Date, Status, Temp, Ship) VALUES('" . $prefetch1['TotalAmount'] . "', '" . $datetime . "', 'New', 0, 0)";
            echo $presql;
            $preresult = mysqli_query($db,$presql);

            $sql = "SELECT * FROM tblorder WHERE TotalAmount = '" . $prefetch1['TotalAmount'] . "' AND Date = '". $datetime ."' AND Status = 'New'";
            echo $sql;
            $result = mysqli_query($db,$sql);
            $fetch = mysqli_fetch_array($result);

            $postsql = "INSERT INTO customer(Lastname, Firstname, ContactNo, Email, Address, OrderNo) VALUES('$_POST[CLName]',  '$_POST[CFName]', '$_POST[CCNo]', '$_POST[CEmail]', '$_POST[CAdd]', ". $fetch['OrderNo'] .")";
            echo $postsql;
            $postresult = mysqli_query($db,$postsql);

            $sql = "UPDATE orderlist SET Temp = 0, OrderNo = '". $fetch['OrderNo'] ."' WHERE Temp = 1";
            echo $sql;
            $result = mysqli_query($db,$sql);

            if($result){
                    echo "updated succesfully";
            }
            exit();
		}

?>
<!DOCTYPE html>
		<head>
				<meta charset="utf-8">
				<title>Lampano Online</title>
				<meta name="description" content="">
				<meta name="author" content="">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link rel="icon" type="image/png" href="../Assets/img/Items/favicon.png">
				<link rel="stylesheet" type="text/css" href="Assets/css/Home.css"></link>

				<script src="Assets/js/Ajax1.9.1.js"></script>
				<script src="Assets/js/Home.js"></script>
				<script src="Assets/js/Jquery1.12.4.js"></script>
				<script src="Assets/js/Parallax.js"></script>
				<script src="Assets/js/Function.js"></script>
		</head>
		<body>

<!-- Sidebar -->
				<div id="SdeBar_container">
						<form id="SdeBar_frmLin" class="active">
								<ul class="SdeBar_content">
										<H1 class="SdeBar_head">Menu</H1>
										<li><a id="SdeBar_btnCancel">Cancellation</a></li>
								</ul>
						</form>
				</div>

<!-- Cancel -->
				<div id="Cancel_Container">
						<form id="Cancel_frmInput" autocomplete="off">
								<H1 class="Cancel_head">Login</H1>
								<input class="Cancel_inpt" type="text" id="Cancel_inptORNo" placeholder="Enter Order No"></input></br>
								<input class="Cancel_inpt" type="text" id="Cancel_inptCName" placeholder="Enter Your Name"></input></br>
						</form>
						<form id="Cancel_frmBtn">
								<ul class="Cancel_buttons">
										<li><a id="Cancel_btnSubmit">Submit</a></li>
										<li><a id="Cancel_btnCancel">Cancel</a></li>
								</ul>
						</form>
						<form id="Cancel_alrtNoInput">
								<ul class="Cancel_alrt">
										<H3 class="Cancel_alrt_head">Alert!!!</H3>
										<li><a>Please Fill In.</a></li>
								</ul>
						</form>
						<form id="Cancel_alrtInvalid">
								<ul class="Cancel_alrt">
										<H3 class="Cancel_alrt_head">Alert!!!</H3>
										<li><a>Your Inputs are Invalid.</a></li>
										<li><a>Your Inputs are Invalid.</a></li>
								</ul>
						</form>
				</div>

<!-- Alert Cancel Success -->
				<div id="AlrtScss_container">
						<form id="AlrtScss_frm">
								<H1 class="AlrtScss_head">Login</H1>
								<ul class="AlrtScss_content">
										<li><a>Your order has been cancelled.</a></li>
								</ul>
						</form>
				</div>

<!-- Header -->		
						<div class="block"></div>
						<header class="headBox">
								<div class="headBack">
										<div id="headNavi"></div>		
										<ul id="headNavi_content">
												<div id="headNavi_btnSearch"></div>
												<input id="headNavi_inptSearch_des" type="text" placeholder="Search"></input>
												<li><a class="headNavi_btns1" href="AboutUs.html">About Us</a></li>
												<li><a class="headNavi_btns2" href="Contact.html">Forum</a></li>
												<li><a class="headNavi_btns3" href="Order.php">Order</a></li>
										</ul>
								</div>
								<div class="headLogo"></div>
						</header>

<!-- Navigation Bar -->
						<div id="Navi_container">
								<ul id="Navi_content">
										<div id="Navi_btnSidebar">
												<span></span>
												<span></span>
												<span></span>
										</div>
										<div id="Navi_logo"></div>
										<div id="Navi_inptSearch">
												<input id="Navi_inptSearch_des" type="text" placeholder="Search"></input>
										</div>	
										<div id="Navi_btns">
												<li><a class="Navi_btns1" href="AboutUs.html">About Us</a></li>
												<li><a class="Navi_btns2" href="Contact.html">Forum</a></li>
												<li><a class="Navi_btns3" href="Order.php">Order</a></li>
										</div>
								</ul>
						</div>

		<!-- Order List View -->
				<div id="OLV_container" style="background: rgb(242,242,242)">
						<form id="OLV_form">
								<H1 class="OLV_head">Order List</H1>
										<div class="tblcontainer">
												<div class="unscrollable">
														<table class="OLV_table">
																<tr>
																		<th style = "width: 10%" ></th>
																		<th style = "width: 50%" >Item</th>
																		<th style = "width: 20%" >Qnty.</th>
																		<th style = "width: 20%" >Price</th>
																</tr>
														</table>
												</div>
												<div class="scrollable">
														<table class="OLV_table">
																<tbody id="cart">

																</tbody>
														</table>
												</div>
												<div class="unscrollable2">
														<table class="OLV_total">
																<tr>
																		<th>Total:</th>
																		<th></th>
																		<th></th>
																		<th><a id = "totalPrice"></a></th>
																</tr>
														</table>
												</div>
										</div>
										</ul>
										<ul class="OLV_btn">
												<li><a id="OLV_btnBuy">Buy</a></li>
										</ul>
								<div id="OLV_btnEdit"></div>
								<div id="OLV_btnClose"></div>
						</form>
						<div id="OLV_btnShow" class="active"><img src="Assets/img/show.png"></div>
				</div>

				<div id="IInfo_shadow"></div>
				<div id='iteminfo'>
                    <div id='iteminfoslct'></div>
                    <div id='iteminfoprice'></div>
                    <div id='iteminfobtn'></div>
				</div>
<!-- Page Content -->

<section class="pageWrap">
		<div class="itemsContent">
						<H1>QUALITY</H1>
						<span class="contentSpan"></span>
						<div id="content_heading">
								<H2>
                            Techniques don't produce quality products and services:
                                    People do, people who care,people who are treated as creatively contributing individuals
                            </H2>
						</div>
						<span class="contentSpan"></span>
				</div>

		<div class="itemDisplay">
				<div class="container respo" id="contItemDisplay">

				</div>
		</div>

		<div class="valueContent">
				<H1>VALUE</H1>
				<span class="contentSpan"></span>
				<div id="content_heading">
						<H2>
Successful companies create value by providing products or services their customers value more highly than available alternatives. they do this while consuming fewer resources, leaving more resources available to satisfy other needs in society. value creation involves making people's lives better.it is contributing to prosperity in society.					</H2>
				</div>
				<span class="contentSpan"></span>
		</div>

		<div id="catContainer" class="container">	
				<div  id="catPers" class="row">
						<div id="catCard" class="columns twelve">
								<form id="catCard1" class="active">
										<div id="catBg1"></div>
										<div id="catPaint" class="catTitle"></div>
								</form>
								<form id="catCard2">
										<div id="catBg2" class="catBack"></div>
										<div id="catWood" class="catTitle"></div>
								</form>
								<form id="catCard3">
										<div id="catBg3" class="catBack"></div>
										<div id="catPBC" class="catTitle"></div>
								</form>
								<form id="catCard4">
										<div id="catBg4"></div>
										<div id="catMetal" class="catTitle"></div>
								</form>
								<form id="catCard5">
										<div id="catBg5" class="catBack"></div>
										<div id="catElec" class="catTitle"></div>
								</form>
								<form id="catCard6">
										<div id="catBg6" class="catBack"></div>
										<div id="catVolt" class="catTitle"></div>
								</form>
								<form id="catCard7">
										<div id="catBg7" class="catBack"></div>
										<div id="catTool" class="catTitle"></div>
								</form>
								<form id="catCard8">
										<div id="catBg8" class="catBack"></div>
										<div id="catMisc" class="catTitle"></div>
								</form>
						</div>
				</div>
				<div class="row">
						<div class="columns one modify">
								<div id="catBtnLeft" class="catPageBtn"></div>
						</div>
						<div class="columns eight modify" id="catCardNo">
								<ul class="catCardNoBtn">
										<li></li>
										<li></li>
										<li></li>
										<li></li>
										<li></li>
										<li></li>
										<li></li>
										<li></li>
								</ul>
						</div>
						<div class="columns one modify">
								<div id="catBtnRight" class="catPageBtn"></div>
						</div>
				</div>
		</div>

		<div class="hardwareContent container">
				<H1>Hardware</H1>
				<div id="content_heading row">
						<div class="columns six test" id="mission">
								<span class="hardwareContentSpan"></span>
								<H2>MISSION</H2>
								<H4>
										To give a high quality type of  Realistic and special product for our Customer like you in making you feel satisfy.
								</H4>
								<span class="hardwareContentSpan"></span>
						</div>
						<div class="columns six test" id="vision">
								<span class="hardwareContentSpan"></span>
								<H2>VISION</H2>
								<H4>
										Building a High quality type of product to gain the loyalty of People's perspective. 
										And to be known Reliable worldwide.
								</H4>
								<span class="hardwareContentSpan"></span>
						</div>
				</div>
		</div>

</section>

		<!-- Customer Info -->
				<div id="CInfo_shadow"></div>
				<div id="CInfo_container">
						<form id="CInfo_frmInput" autocomplete="off">
								<H3 class="CInfo_head">Please Fill Up</H3>
								<input class="CInfo_inpt" type="text" id="CInfo_inptLName" placeholder="Lastname"></input>
								<input class="CInfo_inpt" type="text" id="CInfo_inptFName" placeholder="Firstname"></input>
								<input class="CInfo_inpt" type="text" id="CInfo_inptContact" placeholder="Contact No."></input>
								<input class="CInfo_inpt" type="text" id="CInfo_inptAddress" placeholder="Address"></input>
								<input class="CInfo_inpt" type="text" id="CInfo_inptEmail" placeholder="E-mail Address"></input>
								<H4 id="CInfo_optional">(Optional)</H4>
						</form>
						<form id="CInfo_alrtNoInput">
								<ul class="CInfo_alrt">
										<H3 class="CInfo_alrt_head">Alert!!!</H3>
										<li><a>Please Input the needed area..</a></li>
								</ul>
						</form>
						<form id="CInfo_frmBtn">
								<ul class="CInfo_buttons">
										<li><a id="CInfo_btnSubmit">Submit</a></li>
								</ul>
						</form>
						<div id="CInfo_btnClose"></div>
				</div>

		<footer class="aboutUs">
				<div class="container modify">
						<div class="row">
								<div class="columns five respo" id="links">
										<div class="br respo2">
										<strong>Follow Us On</strong>
										<ul>
												<img id="fb" src="Assets/img/fb.png">
												<li><a href="">Facebook</a></li>
												<br>
												<img id="twitter" src="Assets/img/twitter.png">
												<li><a href="">Twitter</a></li>
										</ul>
										</div>
										<div class="br respo1">
										<strong>Email Us On</strong>
										<br>
										<img id="google" src="Assets/img/google.png">
										<a href="">LampanoHardware</a>
										</div>
										<div class="br respo">
										<strong>Our Branches</strong>
										<ul class="branch">
												<li id="branch1st" class="active">Novaliches</li>
												<li id="branch2nd">Maycuayan</li>
										</ul>
										</div>
								</div>
								<form id="branch1" class="active">
										<div class="columns seven" id="map1">
												<strong>Our Location </strong>at 22 General Luis Novaliches, Quezon City, Metro Manila
												<div class="map">
												<img id="google" src="Assets/img/Maps.png">
												</div>
										</div>
								</form>
								<form id="branch2">
										<div class="columns seven modify" id="map2">
												<strong>Our Location At</strong>
										</div>
								</form>
						</div>
				</div>
		</footer>

        <script type="text/javascript">
            function showinfo(x){
                var getID = x;

                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        displayiteminfo : 1,
                        itmID : getID
                    },
                    success : function(result){
                        $('#IInfo_shadow').addClass('active');
                        $('#iteminfo').addClass('show');
                        $('#iteminfoslct').html(result);
                    }
                });
                
                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        displayitemprice1 : 1,
                        itmID : getID
                    },
                    success : function(result){ 
                        $('#iteminfoprice').html(result);
                    }
                });
                
                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        displayitembtn1 : 1,
                        itmID : getID
                    },
                    success : function(result){ 
                        $('#iteminfobtn').html(result);
                    }
                });
            }
            
            
            
            function priceupdate(itmvar){
                var varID = itmvar.value;

                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        displayitemprice : 1,
                        varID : varID
                    },
                    success : function(result){
                        $('#iteminfoprice').html(result);
                    }
                });
                
                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        displayitembtn : 1,
                        varID : varID
                    },
                    success : function(result){ 
                        $('#iteminfobtn').html(result);
                    }
                });
            }           
            
        /*iteminfobtn*/

        showitem();
        showcart();

        $('#CInfo_btnSubmit').click(function(){
            var CLName = $('#CInfo_inptLName').val();                   
            var CFName = $('#CInfo_inptFName').val();                   
            var CCNo = $('#CInfo_inptContact').val();
            var CAdd = $('#CInfo_inptAddress').val();
            var CEmail = $('#CInfo_inptEmail').val();

            if(CLName != '' && CFName != '' && CCNo != '' && CCNo > 9000000000 && CAdd != ''){
                $.ajax({
                    url : "index.php",
                    type : "POST",
                    async : false,
                    data : {
                        sendcinfo : 1,
                        CLName : CLName,
                        CFName : CFName,
                        CCNo : CCNo,
                        CAdd : CAdd,
                        CEmail : CEmail
                    },
                    success : function(result){
                        
                        alert("Your order has been send.");
                        showcart();
                    }
                });
            }
        });

        function showitem(){
			$.ajax({
                url : "index.php",
                type : "POST",
                async : false,
                data : {
                        showdisplayitem : 1
                },
                success : function(result){
                        $('#contItemDisplay').html(result);
                }
            });
		}
		function showcart(){
			$.ajax({
                url : "index.php",
                type : "POST",
                async : false,
                data : {
                    showcartitem : 1
                },
                success : function(result){
                    $('#cart').html(result);
                }
            });
            $.ajax({
				url: "index.php",
				type : "POST",
				datatype	: "JSON",
				data: {
					totalamount : 1,
				},
				success: function(boom){
					$('#totalPrice').html(boom);
				}
			});
		}
        
        function addTo(itemID){
			var varID = itemID;

			$.ajax({
				url: "index.php",
				type: "POST",
				async: false,
				data: {
					addtocart : 1,
					varID: varID
				},
				success: function(boom){
                    
					$('#cart').html(boom);
                    $('#IInfo_shadow').removeClass('active');
                    $('#iteminfo').removeClass('show');
				}
			});

		}
        
        function quantityChange(cnt, itemID){
			var itmID = itemID;
			var qty = cnt.value;

			$.ajax({
				url: "index.php",
				type : "POST",
				datatype : "JSON",
				data: {
					quantityupdate : 1,
					itemID : itmID,
					quantity : qty
				},
				success: function(boom){
                    
					showcart();
				}
			});
		}

				</script>
		</body>
</html>