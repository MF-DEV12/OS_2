<?php
	session_start();
	if(!isset($_SESSION)){
			header('location:index.php');
	}
	if($_SESSION['LoginType']=='Admin'){
			header('location:main.php');
	}
		include('../config/db.php');
		 

		$db = new mysqli($server, $user, $pass, $db) or die("Unable to Connect");
		
		if(isset($_POST['buttonsave'])){
			$firstsql = "SELECT * FROM supplier, accounts WHERE accounts.AccountNo = supplier.AccountNo AND Username = '".$_SESSION['login_user']."'";
			$firstresult = mysqli_query($db, $firstsql);
			$firstfetch = mysqli_fetch_array($firstresult);
			
			$presql = "INSERT INTO item(Name, SizeType, Removed, Level1No, Level2No, Level3No, BoolFields, Owned, SupplierNo, SRemoved) VALUES('$_POST[name]', '$_POST[sizetype]', 0, '$_POST[fam]', '$_POST[cat]',  '$_POST[subcat]', '$_POST[fields]', 0, '" . $firstfetch['SupplierNo'] . "', 0)";
			$preresult = mysqli_query($db,$presql);
			
			$sql = "SELECT * FROM item WHERE Name = '$_POST[name]' AND Removed = 0 AND Level1No = '$_POST[fam]' AND Level2No = '$_POST[cat]' AND Level3No = '$_POST[subcat]' AND BoolFields = '$_POST[fields]' AND Owned = 0 AND SRemoved = 0";
			$result = mysqli_query($db, $sql);
			$fetch = mysqli_fetch_array($result);
			
			if($_POST['length'] != ''){
				$size = "" . $_POST['length'] . "" . $_POST['lunit'] . "";
			}
			else if($_POST['volume'] != ''){
				$size = "" . $_POST['volume'] . "" . $_POST['vunit'] . "";
			}
			else if($_POST['dimen1'] != '' && $_POST['dimen2'] != '' && $_POST['dimen3'] != ''){
				$size = "" . $_POST['dimen1'] . "" . $_POST['d1unit'] . " x " . $_POST['dimen2'] . "" . $_POST['d2unit'] . " x " . $_POST['dimen3'] . "" . $_POST['d3unit'] . "";
			}
			
			$postsql = "INSERT INTO itemvariant(ItemNo, Size, Color, Description, DPOCost, SRP, Removed, Owned, SupplierNo, SRemoved) VALUES('" . $fetch['ItemNo'] . "', '$size', '$_POST[color]', '$_POST[desc]', '$_POST[DPO]', '$_POST[SRP]', 0, 0, '" . $firstfetch['SupplierNo'] . "', 0)";
			$postresult = mysqli_query($db,$postsql);

			exit();
		}

        if(isset($_POST['buttonvarsave'])){
			$firstsql = "SELECT * FROM supplier, accounts WHERE accounts.AccountNo = supplier.AccountNo AND Username = '".$_SESSION['login_user']."'";
			$firstresult = mysqli_query($db, $firstsql);
			$firstfetch = mysqli_fetch_array($firstresult);
			
			if($_POST['length'] != ''){
				$size = "" . $_POST['length'] . "" . $_POST['lunit'] . "";
			}
			else if($_POST['volume'] != ''){
				$size = "" . $_POST['volume'] . "" . $_POST['vunit'] . "";
			}
			else if($_POST['dimen1'] != '' && $_POST['dimen2'] != '' && $_POST['dimen3'] != ''){
				$size = "" . $_POST['dimen1'] . "" . $_POST['d1unit'] . " x " . $_POST['dimen2'] . "" . $_POST['d2unit'] . " x " . $_POST['dimen3'] . "" . $_POST['d3unit'] . "";
			}
			
			$postsql = "INSERT INTO itemvariant(ItemNo, Size, Color, Description, DPOCost, SRP, Removed, Owned, SupplierNo, SRemoved) VALUES('$_POST[item]', '$size', '$_POST[color]', '$_POST[desc]', '$_POST[DPO]', '$_POST[SRP]', 0, 0, '" . $firstfetch['SupplierNo'] . "', 0)";
			$postresult = mysqli_query($db,$postsql);

			exit();
		}


		if(isset($_POST['lvl1options'])){
			$query = "SELECT * FROM level1";
			$result = mysqli_query($db, $query);

			echo "<option value='' selected hidden>Family</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['Level1No'] . "'>" . $record['Name1'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['lvl2options'])){
			$query = "SELECT * FROM level1, level2 WHERE level1.level1No = level2.level1No";
			$result = mysqli_query($db, $query);

			echo "<option value='' selected hidden>Category</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['Level2No'] . "'>" . $record['Name2'] . "</option>";
			}
			exit();
		}	

		if(isset($_POST['depLvl2'])){
			$Query = "SELECT * FROM level2 WHERE Level1No = '" . $_POST["lvl1id"] . "'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<option value='" . $row['Level2No'] . "'>" . $row['Name2'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['lvl3options'])){
			$query = "SELECT * FROM level1, level2, level3 WHERE level1.level1No = level2.level1No AND level1.level1No = level3.level1No AND level2.level2No = level3.level1No";
			$result = mysqli_query($db, $query);

			echo "<option value='' selected hidden>Subcategory</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['Level3No'] . "'>" . $record['Name3'] . "</option>";
			}
			exit();
		}	

		if(isset($_POST['depLvl3'])){
			$Query = "SELECT * FROM level3 WHERE Level2No = '" . $_POST["lvl2id"] . "'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<option value='" . $row['Level3No'] . "'>" . $row['Name3'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['depLvl3type0'])){
			$presql = "SELECT * FROM level2 WHERE Level1No = '" . $_POST["lvl1id"] . "'";
			$preresult = mysqli_query($db, $presql);
			$prefetch = mysqli_fetch_array($preresult);
			
			$Query = "SELECT * FROM level3 WHERE Level1No = '" . $_POST["lvl1id"] . "' AND Level2No = '" . $prefetch['Level2No'] . "'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<option value='" . $row['Level3No'] . "'>" . $row['Name3'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['lengthunits'])){
			echo "<option value='mm'>millimeters (mm)</option>";
			echo "<option value='cm'>centimeters (cm)</option>";
			echo "<option value='m'>meters (m)</option>";
			echo "<option value='in'>inches (in)</option>";
			echo "<option value='ft'>feet (ft)</option>";
			
			exit();
		}

		if(isset($_POST['volumeunits'])){
			echo "<option value='mL'>milliliters (mL)</option>";
			echo "<option value='L'>liters (L)</option>";
			
			exit();
		}
        
        if(isset($_POST['buttonview'])){
            $sql = "SELECT * FROM itemvariant WHERE ItemNo = '" . $_POST['vitem_no'] . "'";
            $result = mysqli_query($db, $sql);
            
            while($record = mysqli_fetch_array($result)){
				echo "<tr>";
                echo "<td>" . $record['VariantNo'] . "</td>";
                echo "<td>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
                echo "<td>" . $record['Stocks'] . "</td>";
                echo "<td>" . $record['LowStocks'] . "</td>";
                echo "<td>" . $record['Crritical'] . "</td>";
                echo "<td>" . $record['DPOCost'] . "</td>";
                echo "<td>" . $record['SRP'] . "</td>";
                echo "<td><a>Edit</a> <a>Remove</a></td>";
                echo "</tr>";
			}
            
            exit();
        }

        if(isset($_POST['addvariant'])){
            $sql = "SELECT *, item.ItemNo as ITNO FROM item, level1, level2, level3 WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND ItemNo = '" . $_POST['item_id'] . "'";
            $result = mysqli_query($db, $sql);
            $fetch = mysqli_fetch_array($result);
            
            echo "<h1>Add Variant</h1>
            <input id='addvarFillFieldTemp' readonly hidden value ='". $fetch['BoolFields'] ."'/>
            <input id='addvarItemNo' readonly hidden value ='". $fetch['ITNO'] ."'/>
            <input id='addvarSizeType' readonly hidden value ='". $fetch['SizeType'] ."'/>
            <div class='addFields'>
                <h4>Name: </h4><br>
                <h4>Family: </h4><br>
                <h4>Category: </h4><br>
                <h4>Subcategory: </h4><br>";
            
            if($fetch['BoolFields'] == 1 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 7){
                echo "<input type=checkbox disabled checked><h4>Size</h4></input><br>";
            }
            else{
                echo "<input type=checkbox disabled><h4>Size</h4></input><br>";
            }
            
            if($fetch['BoolFields'] == 2 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                echo "<input type=checkbox disabled checked><h4>Color</h4></input><br>";
            }
            else{
                echo "<input type=checkbox disabled><h4>Color</h4></input><br>";
            }
            
            if($fetch['BoolFields'] == 4 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                echo "<input type=checkbox disabled checked><h4>Description</h4></input>";
            }
            else{
                echo "<input type=checkbox disabled><h4>Description</h4></input>";
            }
            
            echo "</div>
                <div class=addInputs>
                <input type=text readonly value='". $fetch['Name'] ."' /><br>
                <select disabled>
                    <option selected>". $fetch['Name1'] ."</option>
                </select>
                <select disabled>
                    <option selected>". $fetch['Name2'] ."</option>
                </select>
                <select disabled>
                    <option selected>". $fetch['Name3'] ."</option>
                </select>
                <select disabled>
                    <option selected>". $fetch['SizeType'] ."</option>
                </select>	
                <br></br>
                <br></br>
                <br>
            </div>
            <div id='divvarFillFields'>
                <div id='addedvarFields'>";
                
                if($fetch['BoolFields'] == 1 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 7){
                echo "<h4 id='addedvarFieldSize'>Size: </h4>";
                    if($fetch['SizeType'] == 'Dimension'){
                        echo "<h4 class='addedvarFieldSize2'>.</h4>
                        <h4 class='addedvarFieldSize2'>.</h4>";
                    } 
                }         
                if($fetch['BoolFields'] == 2 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                    echo "<h4 id='addedvarFieldColor'>Color: </h4>";
                }     
                if($fetch['BoolFields'] == 4 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                    echo "<h4 id='addedvarFieldDesc'>Desc: </h4>";
                }
     
            echo "<h4 class='addedvarFieldPrice'>Price: </h4>
                <h4 class='addedvarFieldPrice'>SRP: </h4>
                </div>
                <div id='addedvarInputs'>";
            
            if($fetch['BoolFields'] == 1 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 7){
                echo "<div id='addvarSize'>";
                if($fetch['SizeType'] == 'Length'){
                    echo "<div id='addvarLength'><input type='text' id='txtvarLength' placeholder='Length'></input><select id='slctvarLength'>echo
                    <option value='mm'>millimeters (mm)</option>
                    <option value='cm'>centimeters (cm)</option>
                    <option value='m'>meters (m)</option>
                    <option value='in'>inches (in)</option>
                    <option value='ft'>feet (ft)</option>
                    </select><br></div>";
                } 
                if($fetch['SizeType'] == 'Volume'){
                    echo "<div id='addvarVolume'><input type='text' id='txtvarVolume' placeholder='Volume'></input><select id='slctvarVolume'><option value='mL'>milliliters (mL)</option>
                    <option value='L'>liters (L)</option></select><br></div>";
                } 
                if($fetch['SizeType'] == 'Dimension'){
                    echo "<div id='addvarDimension'>
                    <input type='text' id='txtvarDimension1' placeholder=''></input><select id='slctvarDimension1'>
                    <option value='mm'>millimeters (mm)</option>
                    <option value='cm'>centimeters (cm)</option>
                    <option value='m'>meters (m)</option>
                    <option value='in'>inches (in)</option>
                    <option value='ft'>feet (ft)</option></select><br>
                    <input type='text' id='txtvarDimension2' placeholder=''></input><select id='slctvarDimension2'>
                    <option value='mm'>millimeters (mm)</option>
                    <option value='cm'>centimeters (cm)</option>
                    <option value='m'>meters (m)</option>
                    <option value='in'>inches (in)</option>
                    <option value='ft'>feet (ft)</option></select><br>
                    <input type='text' id='txtvarDimension3' placeholder=''></input><select id='slctvarDimension3'>
                    <option value='mm'>millimeters (mm)</option>
                    <option value='cm'>centimeters (cm)</option>
                    <option value='m'>meters (m)</option>
                    <option value='in'>inches (in)</option>
                    <option value='ft'>feet (ft)</option></select><br>
                    </div>";
                } 
                echo "</div>";
            }      
            if($fetch['BoolFields'] == 2 || $fetch['BoolFields'] == 3 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                echo "<div id='addvarColor'>
                        <input type='text' id='txtvarColor'></input><br>
                    </div>";
            }     
            if($fetch['BoolFields'] == 4 || $fetch['BoolFields'] == 5 || $fetch['BoolFields'] == 6 || $fetch['BoolFields'] == 7){
                echo "<div id='addvarDesc'>
                        <input type='text' id='txtvarDesc'></input><br>
                    </div>";
            }
                      
            echo "<div>
            <input type='text' id=txtvarDPO></input><br>
            <input type='text' id=txtvarSRP></input><br>
            </div>
            </div>
            </div>";
            
            exit();
        }

		if(isset($_POST['changeinfo'])){
			$ID = $_POST['eItemID'];
			$name = $_POST['eName'];
			$size = $_POST['eSize'];
			$unit = $_POST['eUnit'];
			$desc = $_POST['eDesc'];
			$dpo = $_POST['eDPO'];
			$ave = $_POST['eAve'];
			$lss = $_POST['eLSs'];
			$price = $_POST['ePrice'];
			$ret = $_POST['eRet'];
			$wls = $_POST['eWls'];
			$dis = $_POST['eDis'];

			$sql = "UPDATE item SET Name = '$name', Size= '$size', Unit = '$unit', Description = '$desc', DPOcost = '$dpo', AverageCost = '$ave', LowStocks= '$lss', Price = '$price', Retail = '$ret', Wholesale = '$wls', Distribution = '$dis' WHERE ItemNo = $ID";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "Updated Successfully";
			}
			exit();
		}

		if(isset($_POST['removeitem'])){
			$ID = $_POST['dItemID'];

			$sql = "UPDATE item SET Removed = 1 WHERE ItemNo = $ID";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "Updated Successfully";
			}
			exit();
		}

		if(isset($_POST['restoreitem'])){
			$ID = $_POST['dItemID'];

			$sql = "UPDATE item SET Removed = 0 WHERE ItemNo = $ID";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "Updated Successfully";
			}
			exit();
		}

	if(isset($_POST['showitem'])){
		$presql = "SELECT * FROM supplier, accounts WHERE accounts.AccountNo = supplier.AccountNo AND Username = '".$_SESSION['login_user']."'";
		$preresult = mysqli_query($db,$presql);
		$prefetch = mysqli_fetch_array($preresult);

		$query = "SELECT * FROM item, level1, level2, level3 WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.SRemoved = 0 AND item.SupplierNo ='" . $prefetch['SupplierNo'] . "'";
		$result = mysqli_query($db, $query);	

		while($record = mysqli_fetch_array($result)){
			echo "<tr onclick=itemTemp('" . $record['ItemNo'] . "')>";
			echo "<td>" . $record['ItemNo'] . "</td>";
			echo "<td>" . $record['Name'] . "</td>";
			
			$innersql = "SELECT COUNT(VariantNo) AS NoofItems FROM itemvariant WHERE ItemNo = '" . $record['ItemNo'] . "'";
			$inneresult = mysqli_query($db, $innersql);	
			$innerecord = mysqli_fetch_array($inneresult);
			
			echo "<td>" . $innerecord['NoofItems'] . "</td>";
			echo "<td>" . $record['Name1'] . "</td>";
			echo "<td>" . $record['Name2'] . "</td>";
			echo "<td>" . $record['Name3'] . "</td>";
			echo "</tr>";
		}
		exit();
	}

	if(isset($_POST['showremove'])){
		$query = "SELECT * FROM item, family, category WHERE item.FamilyNo = family.FamilyNo AND category.categoryNo = item.CategoryNo AND Removed = 1";
		$result = mysqli_query($db, $query);	

		while($record = mysqli_fetch_array($result)){
			echo "<tr>";
			echo "<td>" . $record['ItemNo'] . "</td>";
			echo "<td>" . $record['Name'] . "</td>";
			echo "<td>" . $record['Description'] . "</td>";
			echo "<td>" . $record['Family'] . "</td>";
			echo "<td>" . $record['Category'] . "</td>";
			echo "<td>" . $record['Size'] . "</td>";
			echo "<td>" . $record['Unit'] . "</td>";
			echo "<td>" . $record['Price'] . "</td>";
			echo "</tr>";
		}
		exit();
	}

	if(isset($_POST['depSlct'])){
		$Query = "SELECT * FROM category WHERE FamilyNo = '" . $_POST["fam_ID"] . "' ORDER BY Category";
		$myResult = mysqli_query($db, $Query);

		while($row = mysqli_fetch_array($myResult)){
			echo '<option value= "' . $row['CategoryNo'] . '">' . $row['Category'] . '</option>';
		}
		exit();
	}

	if(isset($_POST['depInpt'])){
		$Query = "SELECT Name FROM item WHERE ItemNo = '" . $_POST["itm_ID"] . "'";
		$myResult = mysqli_query($db, $Query);

		while($row = mysqli_fetch_array($myResult)){
			echo '<script> $("#physName").val("' . $row["Name"] . '") </script>';
		}
		exit();
	}
	?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Lampano Hardware</title>
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="icon" type="image/png" href="../Assets/img/Items/favicon.png">
	<link rel="stylesheet" type="text/css" href="Assets/css/mainSupplier.css"></link>
	<link rel="stylesheet" type="text/css" media="all" href="Assets/css/font.css"></link>

	<script src="Assets/js/Ajax1.9.1.js"></script>
	<script src="Assets/js/Jquery1.11.3.js"></script>
	<script src="Assets/js/Jquery1.12.4.js"></script>
	<script src="Assets/js/mainSupplier.js"></script>
</head>
<body>
	<div class="navi">
		<div class="logo"></div>
		<div>
			<ul>
				<li id="naviRequests" class="naviselected">Requests</li>
				<li id="naviItems">Items</li>
			</ul>
		</div>
		<div class='admindrop'>
			<h4><?php echo $_SESSION['login_user']; ?><strong id="adminName"></strong></h4>
			<div class='arrowdown'></div>
		</div>
	</div>

	<div id='adminmenu'>
		<ul>
			<li id="Logout"><a href="logout.php">Logout</a></li>
		</ul>
	</div>

	<div class="topbar">
	<input type="text" placeholder="Search" id='searchCmd'></input>
	<div class="topbarcmd1 cmd">
			<div class='showNothing'>
			</div>
			<div>
				<a id='btnCan'>Cancel</a>
				<a id='btnPro'>Process</a>
			</div>
			<div>
				<a id='btnShp'>Ship</a>
			</div>
		</div>
		<div class="topbarcmd2 cmd">
			<div class='showNothing'>
			</div>
			<div>
				<a id='btnView'>View</a>
			</div>
		</div>
	</div>

	<div class="sidebar enable">
		<div class="topMenu">
			<div id="requestsMenu" class="visible">
				<ul>
					<li id='sdeRequests1' class='sdeselected'>Request Lists</li>
					<li id='sdeRequests2'>New</li>
					<li id='sdeRequests3'>Processing</li>
					<li id='sdeRequests4'>Incomplete</li>
					<li id='sdeRequests5'>Shipped</li>
					<li id='sdeRequests6'>Cancelled</li>
				</ul>
			</div>
			<div id="itemsMenu">
				<ul>
					<li id='sdeItems1'>Items</li>
					<li id='sdeItems2'>Add Item</li>
					<li id='sdeItems3'>Removed</li>
				</ul>
			</div>
			<div class='selector'></div>
		</div>
	</div>

	<div class="table enable">

		<div id="divAllRequests" class="whole visible">
			<h1>Request List</h1>
			<div class="tblContainer small">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 10%">No.</th>
								<th style="width: 14%">Date</th>
								<th style="width: 24%">Company</th>
								<th style="width: 10%">Number of Items</th>
								<th style="width: 13%">Total</th>
								<th style="width: 12%">Status</th>
								<th style="width: 8%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>

			<div id="sideStats">
				<div id="specStats">
					<table>
						<tr><th>All</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
				<div id="specStats">
					<table>
						<tr><th>New</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
				<div id="specStats">
					<table>
						<tr><th>Process</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
				<div id="specStats">
					<table>
						<tr><th>Incomplete</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
				<div id="specStats">
					<table>
						<tr><th>Shipped</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
				<div id="specStats">
					<table>
						<tr><th>Cancel</th></tr>
						<tr><td>0</td></tr>
					</table>
				</div>
			</div>
		</div>

		<div id="divNewRequests" class="whole">
			<h1>New Request</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 12%">No.</th>
								<th style="width: 16%">Date</th>
								<th style="width: 26%">Company</th>
								<th style="width: 12%">Number of Items</th>
								<th style="width: 15%">Total</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divProRequests" class="whole">
			<h1>Processing Request</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 12%">No.</th>
								<th style="width: 16%">Date</th>
								<th style="width: 26%">Company</th>
								<th style="width: 12%">Number of Items</th>
								<th style="width: 15%">Total</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divShipRequests" class="whole">
			<h1>Shipped Request</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 12%">No.</th>
								<th style="width: 16%">Date</th>
								<th style="width: 26%">Company</th>
								<th style="width: 12%">Number of Items</th>
								<th style="width: 15%">Total</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divIncRequests" class="whole">
			<h1>Incomplete Request</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 12%">No.</th>
								<th style="width: 16%">Date</th>
								<th style="width: 26%">Company</th>
								<th style="width: 12%">Number of Items</th>
								<th style="width: 15%">Total</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divCancelRequests" class="whole">
			<h1>Cancelled Request</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 12%">No.</th>
								<th style="width: 16%">Date</th>
								<th style="width: 26%">Company</th>
								<th style="width: 12%">Number of Items</th>
								<th style="width: 15%">Total</th>
								<th style="width: 10%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody>
							<?php
								$query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.status = 'New'";
								$result = mysqli_query($db, $query);	

								while($record = mysqli_fetch_array($result)){
									echo "<tr>";
									echo "<td style='width: 10%'>" . $record['OrderNo'] . "</td>";
									echo "<td style='width: 14%'>" . $record['Customer'] . "</td>";
									echo "<td style='width: 24%'>" . $record['Address'] . "</td>";
									echo "<td style='width: 10%'>" . $record['Date'] . "</td>";
									echo "<td style='width: 13%'>" . $record['TotalAmount'] . "</td>";
									echo "<td style='width: 12%'>" . $record['Status'] . "</td>";
									echo "<td style='width: 8%'><a>View</a><a>Print</a></td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divItems1" class="whole">
			<h1>Items</h1><input id='viewitemtemp' readonly hidden/>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 10%">ItemNo</th>
								<th style="width: 26%">Name</th>
								<th style="width: 10%">No of<br>Variant</th>
								<th style="width: 18%">Family</th>
								<th style="width: 18%">Category</th>
								<th style="width: 18%">Subcategory</th>
							</tr>
						</thead>	
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody id = 'tblItem'>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divItemView" class="whole">
			<div>
				<h2 class='itemname'>Item:</h2><input id="itmName" readonly class='change1' />
				<span></span>
				<h5>Item No.:</h5><input id='itmNo' readonly class='item0'/>
			</div>
			<div class='orderDisplayed viewFunc'>
				<a class='viewBtn' id='viewEdit'>Edit</a>
				<a class='viewBtn' id='viewRemove'>Remove</a>
			</div>		
            <br>  
            <h2>Variants</h2>
            <a class='viewBtn' id='viewAdd'>Add Variant</a>
            <div id="supFormBody">
                <div class="tblContaineritemdisplay">
                    <div class="unscrollableitemdisplay">
                        <table>
                            <thead>
                                <tr>
                                    <th>Variant<br>No</th>
                                    <th>Description</th>
                                    <th>Stock</th>
                                    <th>Low Stock<br>Level</th>
                                    <th>Critical<br>Level</th>
                                    <th>Default<br>PO Cost</th>
                                    <th>SRP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="scrollableitemdisplay">
                        <table id ='suppliertable'>
                            <tbody id='tblSupplierItems'>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

	<div id="divItems2" class="whole">
		<input id='addFillFieldTemp' readonly hidden/>
		<h1>Add Item</h1>
        <div class="addFields">
            <h4>Name: </h4><br>
            <h4>Family: </h4><br>
            <h4>Category: </h4><br>
            <h4>Subcategory: </h4><br>
            <input type='checkbox' id='chkSize'><h4>Size</h4></input><br>
            <input type='checkbox' id='chkColor'><h4>Color</h4></input><br>
            <input type='checkbox' id='chkDesc'><h4>Description</h4></input>
        </div>
        <div class="addInputs">
            <input type='text' id = "additmname" autocomplete="off"/><br>
            <select name='lvl1' id='addSlctLvl1'>

            </select>
            <select name='lvl2' id='addSlctLvl2'>

            </select>
            <select name='lvl3' id='addSlctLvl3'>

            </select>
            <select id='addSlctSize' disabled='true'>
                <option value='Length' selected>Length</option>
                <option value='Volume'>Volume</option>
                <option value='Dimension'>Dimension</option>
            </select>	
            <br></br>
            <br></br>
            <br>
        </div>
        <div  id='divFillFields'>
            <div id='addedFields'>
                <h4 id='addedFieldSize'>Size: </h4>
                <h4 class='addedFieldSize2'>.</h4>
                <h4 class='addedFieldSize2'>.</h4>
                <h4 id='addedFieldColor'>Color: </h4>
                <h4 id='addedFieldDesc'>Desc: </h4>
                <h4 class='addedFieldPrice'>Price: </h4>
                <h4 class='addedFieldPrice'>SRP: </h4>
            </div>
            <div id='addedInputs'>
                <div id='addSize'>
                    <div id='addLength'><input type='text' id='txtLength' placeholder='Length'></input><select id='slctLength'></select><br></div>
                    <div id='addVolume'><input type='text' id='txtVolume' placeholder='Volume'></input><select id='slctVolume'></select><br></div>
                    <div id='addDimension'>
                        <input type='text' id='txtDimension1' placeholder=''><select id='slctDimension1'></select></input><br>
                        <input type='text' id='txtDimension2' placeholder=''><select id='slctDimension2'></select></input><br>
                        <input type='text' id='txtDimension3' placeholder=''><select id='slctDimension3'></select></input><br>
                    </div>
                </div>
                <div id='addColor'>
                    <input type='text' id='txtColor'></input><br>
                </div>
                <div id='addDesc'>
                    <input type='text' id='txtDesc'></input><br>
                </div>
                <div id='addPrice'>
                    <input type='text' id='txtDPO'></input><br>
                    <input type='text' id='txtSRP'></input><br>
                </div>
            </div>
        </div>
        <div class="addButtons">
            <a id="additmConfirm">Confirm</a>
            <a id="additmReset">Reset</a>
        </div>
	</div>

    <div id="divAddVariant" class="whole">
        <div id='addvarFields'>
        
        </div>
		<div class="addButtons">
            <a id="addvarConfirm">Confirm</a>
            <a id="addvarReset">Reset</a>
        </div>
	</div>

	<div id="divItems3" class="whole">
			<h1>Removed Items</h1><input id='viewtemp' readonly hidden />

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 10%">ItemNo</th>
								<th style="width: 15%">Name</th>
								<th style="width: 23%">Description</th>
								<th style="width: 12%">Family</th>
								<th style="width: 12%">Category</th>
								<th style="width: 8%">Size</th>
								<th style="width: 6%">Unit</th>
								<th style="width: 7%">Default<br>Price</th>
							</tr>
						</thead>	
					</table>
				</div>
				<div class="scrollable" >
					<table>
						<tbody id = 'tblItem'>

						</tbody>
					</table>
				</div>
			</div>
		</div>	

		<script type="text/javascript">
			$(function(){
                var itemvar ='';
				showdata();

				$('#btnView').click(function(){
					var viewItemNo = $('#viewitemtemp').val();
                    itemvar = $('#viewitemtemp').val();
					if(viewItemNo != ''){
						$.ajax({
							url: "mainSupplier.php",
							type: "POST",
							async: false,
							data: {
								buttonview : 1,
								vitem_no : viewItemNo
							},
							success: function(boom){
                                $('#tblSupplierItems').html(boom);
                                $('#divItemView').toggleClass('visible');
							}
						});
					}
					else{
						alert('Please choose an item to view.');
					}
				});

				$('#btnEdit').click(function(){
					var catID = $('#sctdCat').val();

					$.ajax({
						url: "mainSupplier.php",
						type: "POST",
						async: false,
						data: {
							editfamilyoptions : 1,
							cat_ID : catID
						},
						success: function(boom){
							$('#adtCatSlct').html(boom);
						}
					});
				});

				$('#sdeItems2').click(function(){
					$.ajax({
						url: "mainSupplier.php",
						type: "POST",
						async: false,
						data: {
							lvl1options : 1,
						},
						success: function(boom){
							$('#addSlctLvl1').html(boom);
						}
					});
					$.ajax({
						url: "mainSupplier.php",
						type: "POST",
						async: false,
						data: {
							lvl2options : 1,
						},
						success: function(boom){
							$('#addSlctLvl2').html(boom);
						}
					});
					$.ajax({
						url: "mainSupplier.php",
						type: "POST",
						async: false,
						data: {
							lvl3options : 1,
						},
						success: function(boom){
							$('#addSlctLvl3').html(boom);
						}
					});
				});

				$('#btnViewRev').click(function(){
					var viewItemNo = $('#viewtemprev').val();

					if(viewItemNo != ''){
						$.ajax({
							url: "mainSupplier.php",
							type: "POST",
							async: false,
							data: {
								buttonview : 1,
								vitem_no : viewItemNo
							},
							success: function(boom){
									$('#viewItemInfo').html(boom);
									$('#divItemView').toggleClass('visible');
							}
						});
					}
					else{
						alert('Please choose an item to view.');
					}
				});

				$('#viewRemove').click(function(){
					var viewItemNo = $('#viewtemp').val();

					if(viewItemNo != ''){
						$.ajax({
							url: "mainSupplier.php",
							type: "POST",
							async: false,
							data: {
								buttonview : 1,
								vitem_no : viewItemNo
							},
							success: function(boom){
									$('#viewItemInfo').html(boom);
							}
						});
					}
					else{
						alert('Please choose an item to view.');
					}
				});

				$('#physCountConfirm').click(function(){
					var cntItmNo = $('#physSlct').val();
					var cntItmPhys = $('#physStock').val();

					if(cntItmNo != ''){
						$.ajax({
							url: "mainSupplier.php",
							type : "POST",
							datatype	: "JSON",
							data: {
								countconfirm : 1,
								cnt_itmNo : cntItmNo,
								cnt_itmStock : cntItmPhys
							},
							success: function(boom){
								alert("Updated Successfully");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 );
							}
						});
					}
				});

				$('#addFamConfirm').click(function(){
					var name = $('#addFamName').val();
					var desc = $('#addFamDesc').val();

					if( name != '' ){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							async	: false,
							data : {
								buttonaddfam : 1,
								aname : name,
								adesc : desc,
							},
							success : function(result){
								alert("Successfully Added");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 );
							}
						});
					}	
				});

				$('#addCatConfirm').click(function(){
					var name = $('#addCatName').val();
					var desc = $('#addCatDesc').val();
					var fam = $('#addCatSlct').val();
					if( name != '' ){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							async	: false,
							data : {
								buttonaddcat : 1,
								aname : name,
								adesc : desc,
								afam : fam
							},
							success : function(result){
								alert("Successfully Added");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 );
							}
						});
					}	
				});

				$('#additmConfirm').click(function(){
					var name = $('#additmname').val();
					var fam = $('#addSlctLvl1').val();
					var cat = $('#addSlctLvl2').val();
					var subcat = $('#addSlctLvl3').val();
					var fields = $('#addFillFieldTemp').val();
					var sizetype = $('#addSlctSize').val();
					var length = $('#txtLength').val();
					var lunit = $('#slctLength').val();
					var volume = $('#txtVolume').val();
					var vunit = $('#slctVolume').val();
					var dimen1 = $('#txtDimension1').val();
					var d1unit = $('#slctDimension1').val();
					var dimen2 = $('#txtDimension2').val();
					var d2unit = $('#slctDimension2').val();
					var dimen3 = $('#txtDimension3').val();
					var d3unit = $('#slctDimension3').val();
					var color = $('#txtColor').val();
					var desc = $('#txtDesc').val();
					var DPO = parseInt($('#txtDPO').val());
					var SRP = parseInt($('#txtSRP').val());
					
					var ctr = 0;
							
					if(name != '' && fam !='' && cat !='' && subcat !=''){
						if(fields != '' && fields != 0){
							if(fields == 1 || fields == 3 || fields == 5 || fields == 7){
								if(sizetype == 'Length'){
									if(length != ''){
										ctr = ctr + 1;
										$('#txtLength').css("border","1px solid #ccc");
									}
									else{
										if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
											ctr = ctr - 1;
										}
										$('#txtLength').css("border","1px solid red");
									}
								}
								else if(sizetype == 'Volume'){
									if(volume != ''){
										ctr = ctr + 1;
										$('#txtVolume').css("border","1px solid #ccc");
									}
									else{
										if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
											ctr = ctr - 1;
										}
										$('#txtVolume').css("border","1px solid red");
									}
								}
								else if(sizetype == 'Dimension'){
									if(dimen1 != '' && dimen2 != '' && dimen3 != ''){
										ctr = ctr + 1;
										$('#txtDimension1').css("border","1px solid #ccc");
										$('#txtDimension2').css("border","1px solid #ccc");
										$('#txtDimension3').css("border","1px solid #ccc");
									}
									else{
										if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
											ctr = ctr - 1;
										}
										$('#txtDimension1').css("border","1px solid red");
										$('#txtDimension2').css("border","1px solid red");
										$('#txtDimension3').css("border","1px solid red");
									}
								}
							}
							if(fields == 2 || fields == 3 || fields == 6 || fields == 7){
								if(color != ''){
									ctr = ctr + 2;
									$('#txtColor').css("border","1px solid #ccc");
								}
								else{
									if(ctr == 2 || ctr == 3 || ctr == 6 || ctr == 7){
										ctr = ctr - 2;
									}
									$('#txtColor').css("border","1px solid red");
								}
							}
							if(fields == 4 || fields == 5 || fields == 6 || fields == 7){
								if(desc != ''){
									ctr = ctr + 4;
									$('#txtDesc').css("border","1px solid #ccc");
								}
								else{
									if(ctr == 4 || ctr == 5 || ctr == 6 || ctr == 7){
										ctr = ctr - 4;
									}
									$('#txtDesc').css("border","1px solid red");
								}
							}
							
							if(ctr == fields){
								if(DPO != '' && SRP !=''){
									if(SRP > DPO){
										$.ajax({
											url : "mainSupplier.php",
											type : "POST",
											async	: false,
											data : {
												buttonsave : 1,
												name : name,
												fam : fam,
												cat : cat,
												subcat : subcat,
												fields : fields,
                                                sizetype : sizetype,
												length : length,
												lunit : lunit,
												volume : volume,
												vunit : vunit,
												dimen1 : dimen1,
												d1unit : d1unit,
												dimen2 : dimen2,
												d2unit : d2unit,
												dimen3 : dimen3,
												d3unit : d3unit,
												color : color,
												desc : desc,
												DPO : DPO,
												SRP : SRP,
											},
											success : function(result){
												alert("Successfully added");
												showdata();
												$("input").val("");
												$("input").css("border", "1px solid #ccc");
												$('.visible').removeClass('visible');
												$('#divItems1').addClass('visible');
												$('#itemsMenu').addClass('visible');
												sdeSelect = 12.5;
												$('.selector').css('top',sdeSelect+'px');
												$('.topbarcmd2').css('top','-6vh');
												$('li').removeClass('sdeselected');
												$('#sdeItems1').addClass('sdeselected');
											}
										});
									}
									else{
										alert("SRP cannot be equal/lower than the Price.");
										$('#txtDPO').css("border","1px solid red");
										$('#txtSRP').css("border","1px solid red");
									}
								}
								else{
									alert("Please put a price.");
									$('#txtDPO').css("border","1px solid red");
									$('#txtSRP').css("border","1px solid red");
								}
							}
							else{
								alert("Please fill the fields.");
							}
						}
						else{
							alert("Please pick a field/s.");
						}
						$('#additmname').css("border","1px solid #ccc");
						$('#addSlctLvl1').css("border","1px solid #ccc");
						$('#addSlctLvl2').css("border","1px solid #ccc");
						$('#addSlctLvl3').css("border","1px solid #ccc");
					}
					else{
						if( name == '' ){
							$('#additmname').css("border","1px solid red");
						}
						else{
							$('#additmname').css("border","1px solid #ccc");
						}
						if( fam == '' ){
							$('#addSlctLvl1').css("border","1px solid red");
						}
						else{
							$('#addSlctLvl1').css("border","1px solid #ccc");
						}
						if( cat == '' ){
							$('#addSlctLvl2').css("border","1px solid red");
						}
						else{
							$('#addSlctLvl2').css("border","1px solid #ccc");
						}
						if( subcat == '' ){
							$('#addSlctLvl3').css("border","1px solid red");
						}
						else{
							$('#addSlctLvl3').css("border","1px solid #ccc");
						}						
						alert("Please Fill All.");
					}
				});
                
                $('#addvarConfirm').click(function(){
                    var fields = $('#addvarFillFieldTemp').val();
                    var sizetype = $('#addvarSizeType').val();
                    var item = $('#addvarItemNo').val();
					var length = $('#txtvarLength').val();
					var lunit = $('#slctvarLength').val();
					var volume = $('#txtvarVolume').val();
					var vunit = $('#slctvarVolume').val();
					var dimen1 = $('#txtvarDimension1').val();
					var d1unit = $('#slctvarDimension1').val();
					var dimen2 = $('#txtvarDimension2').val();
					var d2unit = $('#slctvarDimension2').val();
					var dimen3 = $('#txtvarDimension3').val();
					var d3unit = $('#slctvarDimension3').val();
					var color = $('#txtvarColor').val();
					var desc = $('#txtvarDesc').val();
					var DPO = parseInt($('#txtvarDPO').val());
					var SRP = parseInt($('#txtvarSRP').val());
					
					var ctr = 0;
                    if(fields == 1 || fields == 3 || fields == 5 || fields == 7){
                        if(sizetype == 'Length'){
                            if(length != ''){
                                ctr = ctr + 1;
                                $('#txtLength').css("border","1px solid #ccc");
                            }
                            else{
                                if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
                                    ctr = ctr - 1;
                                }
                                $('#txtLength').css("border","1px solid red");
                            }
                        }
                        else if(sizetype == 'Volume'){
                            if(volume != ''){
                                ctr = ctr + 1;
                                $('#txtVolume').css("border","1px solid #ccc");
                            }
                            else{
                                if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
                                    ctr = ctr - 1;
                                }
                                $('#txtVolume').css("border","1px solid red");
                            }
                        }
                        else if(sizetype == 'Dimension'){
                            if(dimen1 != '' && dimen2 != '' && dimen3 != ''){
                                ctr = ctr + 1;
                                $('#txtDimension1').css("border","1px solid #ccc");
                                $('#txtDimension2').css("border","1px solid #ccc");
                                $('#txtDimension3').css("border","1px solid #ccc");
                            }
                            else{
                                if(ctr == 1 || ctr == 3 || ctr == 5 || ctr == 7){
                                    ctr = ctr - 1;
                                }
                                $('#txtDimension1').css("border","1px solid red");
                                $('#txtDimension2').css("border","1px solid red");
                                $('#txtDimension3').css("border","1px solid red");
                            }
                        }
                    }
                    if(fields == 2 || fields == 3 || fields == 6 || fields == 7){
                        if(color != ''){
                            ctr = ctr + 2;
                            $('#txtColor').css("border","1px solid #ccc");
                        }
                        else{
                            if(ctr == 2 || ctr == 3 || ctr == 6 || ctr == 7){
                                ctr = ctr - 2;
                            }
                            $('#txtColor').css("border","1px solid red");
                        }
                    }
                    if(fields == 4 || fields == 5 || fields == 6 || fields == 7){
                        if(desc != ''){
                            ctr = ctr + 4;
                            $('#txtDesc').css("border","1px solid #ccc");
                        }
                        else{
                            if(ctr == 4 || ctr == 5 || ctr == 6 || ctr == 7){
                                ctr = ctr - 4;
                            }
                            $('#txtDesc').css("border","1px solid red");
                        }
                    }

                    if(ctr == fields){
                        if(DPO != '' && SRP !=''){
                            if(SRP > DPO){
                                $.ajax({
                                    url : "mainSupplier.php",
                                    type : "POST",
                                    async	: false,
                                    data : {
                                        buttonvarsave : 1,
                                        fields : fields,
                                        item : item,
                                        length : length,
                                        lunit : lunit,
                                        volume : volume,
                                        vunit : vunit,
                                        dimen1 : dimen1,
                                        d1unit : d1unit,
                                        dimen2 : dimen2,
                                        d2unit : d2unit,
                                        dimen3 : dimen3,
                                        d3unit : d3unit,
                                        color : color,
                                        desc : desc,
                                        DPO : DPO,
                                        SRP : SRP,
                                    },
                                    success : function(result){
                                        alert("Successfully added");
                                        showdata();
                                        $("input").val("");
                                        $("input").css("border", "1px solid #ccc");
                                        $('.visible').removeClass('visible');
                                        $('#divItems1').addClass('visible');
                                        $('#itemsMenu').addClass('visible');
                                        sdeSelect = 12.5;
                                        $('.selector').css('top',sdeSelect+'px');
                                        $('.topbarcmd2').css('top','-6vh');
                                        $('li').removeClass('sdeselected');
                                        $('#sdeItems1').addClass('sdeselected');
                                    }
                                });
                            }
                            else{
                                alert("SRP cannot be equal/lower than the Price.");
                                $('#txtDPO').css("border","1px solid red");
                                $('#txtSRP').css("border","1px solid red");
                            }
                        }
                        else{
                            alert("Please put a price.");
                            $('#txtDPO').css("border","1px solid red");
                            $('#txtSRP').css("border","1px solid red");
                        }
                    }
                    else{
                        alert("Please fill the fields.");
                    }
				});

				$('#viewEdit').click(function(){
					var item_id = $('#viewtemp').val();

					if(item_id != ''){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							async : false,
							data : {
								edit_fam : 1,
								eItemID : item_id,
							},
							success : function(result){
								showdata();
								$('#slctEdtFam').html(result);
							}
						});

						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							async : false,
							data : {
								edit_cat : 1,
								eItemID : item_id,
							},
							success : function(result){
								showdata();
								$('#slctEdtCat').html(result);
							}
						});
					}
				});
               
				$('#edtItmConfirm').click(function(){
					var item_id = $('#viewtemp').val();
					var name = $('#edtName').val();
					var size = $('#edtSize').val();
					var unit = $('#edtUnit').val();
					var desc = $('#edtDescription').val();
					var DPO = $('#edtDpo').val();
					var Ave = $('#edtUnit_cost').val();
					var LSs = $('#edtlow_stock').val();
					var price = $('#edtUnit_price').val();
					var ret = $('#edtRetail').val();
					var wls = $('#edtWholesale').val();
					var dis = $('#edtDistribution').val();

					if(item_id != ''){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							datatype : "JSON",
							data : {
								changeinfo : 1,
								eItemID : item_id,
								eName : name,
								eSize : size,
								eUnit : unit,
								eDesc : desc,
								eDPO : DPO,
								eAve : Ave,
								eLSs : LSs,
								ePrice : price,
								eRet : ret,
								eWls : wls,
								eDis : dis
							},
							success : function(result){
								alert("Information Updated");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 )
							}
						});
					}
				});

				$('#rmvItmConfirm').click(function(){
					var item_id = $('#viewtemp').val();

					if(item_id != ''){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							datatype : "JSON",
							data : {
								removeitem : 1,
								dItemID : item_id
							},
							success : function(result){
								alert("Item removed");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
									$('#divItemView').removeClass('visible');
								}, 300 )
							}
						});
					}
				});

				$('#rstItmConfirm').click(function(){
					var item_id = $('#viewtemprev').val();

					if(item_id != ''){
						$.ajax({
							url : "mainSupplier.php",
							type : "POST",
							datatype : "JSON",
							data : {
								restoreitem : 1,
								dItemID : item_id
							},
							success : function(result){
								alert("Item restored");
								showdata();
								$('.addmodal').removeClass('show');

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
									$('#divItemView').removeClass('visible');
								}, 300 )
							}
						});
					}
				});
                
                $('#viewAdd').click(function(){
                    $.ajax({
                        url : "mainSupplier.php",
                        type : "POST",
                        async : false,
                        data : {
                            addvariant : 1,
                            item_id : itemvar
                        },
                        success : function(result){ 
                            $('#addvarFields').html(result);
                        }
                    });
				});

			});
			
			function showslct(){
				$.ajax({
					url : "mainSupplier.php",
					type : "POST",
					async : false,
					data : {
						lengthunits : 1,
						},
						success : function(result){
							$('#slctLength').html(result);
							$('#slctDimension1').html(result);
							$('#slctDimension2').html(result);
							$('#slctDimension3').html(result);
						}
				});
				
				$.ajax({
					url : "mainSupplier.php",
					type : "POST",
					async : false,
					data : {
						volumeunits : 1,
						},
						success : function(result){
							$('#slctVolume').html(result);
						}
				});
			}

			function showdata(){
				$.ajax({
					url : "mainSupplier.php",
					type : "POST",
					async : false,
					data : {
						showitem : 1,
						},
						success : function(result){
							$('#tblItem').html(result);
						}
				});

				$.ajax({
					url : "mainSupplier.php",
					type : "POST",
					async : false,
					data : {
						showremove : 1,
					},
					success : function(result){
						$('#tblItemRemoved').html(result);
					}
				});

				var viewItemNo = $('#viewtemp').val();
				if(viewItemNo != ''){
					$.ajax({
						url: "mainSupplier.php",
						type: "POST",
						async: false,
						data: {
							btnviewupdated : 1,
							vitem_no: viewItemNo
						},
						success : function(result){
							$('#viewItemInfo').html(result);
						}
					});
				}
			}
            
            function itemTemp(id){
                var itemid = id;

                $('#viewitemtemp').val(itemid);
            }
		</script>

		<script>
			$(document).ready(function(){
				$('#addSlctLvl1').change(function(){
					var lvl1id = $(this).val();
					$.ajax({
						url: 'mainSupplier.php',
						method: 'POST',
						data: {
							depLvl2: 1,
							lvl1id : lvl1id
						},
						datatype: 'text',
						success:function(data){
							$('#addSlctLvl2').html(data);
						}
					});
					
					var lvl2id = $('#addSlctLvl2').val();
					$.ajax({
						url: 'mainSupplier.php',
						method: 'POST',
						data: {
							depLvl3type0: 1,
							lvl1id : lvl1id,
							lvl2id : lvl2id
						},
						datatype: 'text',
						success:function(data){
							$('#addSlctLvl3').html(data);
						}
					});
				});
				
				$('#addSlctLvl2').change(function(){
					var lvl2id = $(this).val();
					$.ajax({
						url: 'mainSupplier.php',
						method: 'POST',
						data: {
							depLvl3: 1,
							lvl2id : lvl2id
						},
						datatype: 'text',
						success:function(data){
							$('#addSlctLvl3').html(data);
						}
					});
				});

			});
		</script>
	</div>
</body>
</html>