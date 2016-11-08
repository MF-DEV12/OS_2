<?php
		 	include('config/db.php');
					 

			$db = new mysqli($server, $user, $pass, $db) or die("Unable to Connect");

			if(isset($_POST['showdisplayitem'])){
					$cnt = 0;
					$sql = "SELECT * FROM item ORDER BY ItemNo";
					$result = mysqli_query($db, $sql);

					while($record = mysqli_fetch_array($result)){
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

					exit();
			}

			if(isset($_POST['showcartitem'])){
					$sql = "SELECT * FROM item, orderlist WHERE item.ItemNo = orderlist.ItemNo AND Temp = 1";
					$result = mysqli_query($db, $sql);

					while($record = mysqli_fetch_array($result)){
							echo "<tr>
									<td>". $record['Name'] ."</td>
									<td>". $record['Description'] ." </td>
									<td><input type=text value='" . $record['Quantity'] . "' class=quantity onchange='quantityChange(this, " . $record['OrderListNo'] . ")' /></td>
									<td>". $record['Total'] ." </td>
							</tr>";
					}
					exit();
			}

			if(isset($_POST['displayiteminfo'])){
					$sql = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND item.ItemNo = ". $_POST['itmID'];
					$result = mysqli_query($db, $sql);
					$record = mysqli_fetch_array($result);

					echo "<div>
							<h4>Item: ". $record['Name'] ."</h4>
							<h4>Description: ". $record['Description'] ." </h4>
							<h4>Size: ". $record['Size'] ." </h4>
							<h4>Price: ". $record['Price'] ." </h4><br>
							<a onclick=addTo('" . $record['ItemNo'] . "') class=addtocart>Add to Cart</a>
					</div>";

					exit();
			}

			if(isset($_POST['addtocart'])){
					$sql = "SELECT ItemNo FROM orderlist WHERE ItemNo = " . $_POST["itemID"] . " AND Temp = 1";
					$result = mysqli_query($db, $sql);		

					if(mysqli_num_rows($result) > 0){

					}
					else{
							$sql = "INSERT INTO orderlist(ItemNo, Temp) VALUES('$_POST[itemID]',1)";
							$result = mysqli_query($db, $sql);
					}
					$postsql = "SELECT * FROM item, orderlist WHERE item.ItemNo = orderlist.ItemNo AND Temp = 1";
					$postresult = mysqli_query($db, $postsql);

					while($postfetch = mysqli_fetch_array($postresult)){
							echo "<tr>
									<td>". $postfetch['Name'] ."</td>
									<td>". $postfetch['Description'] ." </td>
									<td><input type=text value='" . $postfetch['Quantity'] . "' class=quantity onchange='quantityChange(this, " . $postfetch['OrderListNo'] . ")' /></td>
									<td>". $postfetch['Total'] ." </td>
							</tr>";   
					}
					exit();
			}

			if(isset($_POST['quantityupdate'])){
					$presql = "SELECT * FROM item WHERE ItemNo = " . $_POST['itemID'];
					$preresult = mysqli_query($db, $presql);
					$data = mysqli_fetch_array($preresult);

					$sql = "UPDATE orderlist SET Quantity = " . $_POST['quantity'] . ", Total = " . $data['Price'] * $_POST['quantity'] . " WHERE OrderListNo = " . $_POST["itemID"] . " AND Temp = 1";
					$result = mysqli_query($db,$sql);

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
					$preresult1 = mysqli_query($db,$presql1);
					$prefetch1 = mysqli_fetch_array($preresult1);

					$presql = "INSERT INTO tblorder(TotalAmount, Date, Status, Temp, Ship) VALUES('" . $prefetch1['TotalAmount'] . "', '" . $datetime . "', 'New', 0, 0)";
					$preresult = mysqli_query($db,$presql);

					$sql = "SELECT * FROM tblorder WHERE TotalAmount = '" . $prefetch1['TotalAmount'] . "' AND Date = '". $datetime ."' AND Status = 'New'";
					$result = mysqli_query($db,$sql);
					$fetch = mysqli_fetch_array($result);

					$postsql = "INSERT INTO customer(Lastname, Firstname, ContactNo, Email, Address, OrderNo) VALUES('$_POST[CLName]',  '$_POST[CFName]', '$_POST[CCNo]', '$_POST[CEmail]', '$_POST[CAdd]', ". $fetch['OrderNo'] .")";
					$postresult = mysqli_query($db,$postsql);

					$sql = "UPDATE orderlist SET Temp = 0, OrderNo = '". $fetch['OrderNo'] ."' WHERE Temp = 1";
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
	<link rel="stylesheet" type="text/css" href="Assets/css/Order.css"></link>

	<script src="Assets/js/Parallax.js"></script>
	<script src="Assets/js/Home.js"></script>
	<script src="Assets/js/Jquery1.12.4.js"></script>
	<script src="Assets/js/Order.js"></script>
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
									<H1 class="Cancel_head">Cancellation</H1>
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
									</ul>
							</form>
					</div>

					<div id="AlrtScss_container">
							<form id="AlrtScss_frm">
									<H1 class="AlrtScss_head">Login</H1>
									<ul class="AlrtScss_content">
											<li><a>Your order has been cancelled.</a></li>
									</ul>
							</form>
					</div>

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
													<li><a class="Navi_btns2" href="Contact.html">Contact</a></li>
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
																			<tr>
																			<th style:width = "30%">Item</th>
																			<th style:width = "30%" >Desc</th>
																			<th style:width = "30%" >Qnty.</th>
																			<th style:width = "40%" >Price</th>
																	</tr>
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

					</div>
<!-- Page Content -->

<section class="pageWrap">

<div class="itemDisplay">
					<div class="container respo" id="contItemDisplay">

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

			<script type="text/javascript">

									showitem();
									showcart();

							$('#CInfo_btnSubmit').click(function(){
									var CLName = $('#CInfo_inptLName').val();                   
									var CFName = $('#CInfo_inptFName').val();                   
									var CCNo = $('#CInfo_inptContact').val();
									var CAdd = $('#CInfo_inptAddress').val();
									var CEmail = $('#CInfo_inptEmail').val();

									if(CLName != '' && CFName != '' && CCNo != '' && CAdd != ''){
											$.ajax({
													url : "Order.php",
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

							function showinfo(x){
									var getID = x;

									$.ajax({
											url : "Order.php",
											type : "POST",
											async : false,
											data : {
													displayiteminfo : 1,
													itmID : getID
											},
											success : function(result){
													$('#IInfo_shadow').addClass('active');
													$('#iteminfo').addClass('show');
													$('#iteminfo').html(result);
											}
									});
							}

							function showitem(){
				$.ajax({
											url : "Order.php",
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
											url : "Order.php",
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
					url: "Order.php",
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
				var itmID = itemID;

				$.ajax({
					url: "Order.php",
					type: "POST",
					async: false,
					data: {
						addtocart : 1,
						itemID: itmID
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
					url: "Order.php",
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