<?php
		include('../config/db.php');
		
		session_start();
		if(!isset($_SESSION)){
			header('location:index.php');
		}
		if($_SESSION['LoginType']=='Supplier'){
			header('location:mainSupplier.php');
		}
	 
		$db = new mysqli($server, $user, $pass, $db) or die("Unable to Connect");

/*------------------------------------------------  ------------------------------------------------*/

		if(isset($_POST['buttonsave'])){
			$username = $_POST['ausername'];
			$password = $_POST['apassword'];

			$sql = "INSERT INTO accounts(Username, Password, LoginType) VALUES('$username','$password','Supplier')";
			$result = mysqli_query($db,$sql);

			if($result){
				echo "Account Successfully Added";
			}		

			$name = $_POST['aname'];
			$address = $_POST['aaddress'];
			$contact = $_POST['acontact'];
			$email = $_POST['aemail'];

			$accID = mysqli_query($db, "SELECT AccountNo FROM accounts WHERE Username = '$username' AND Password ='$password' ");
			$result2 = mysqli_fetch_array($accID);

			$sql3 = "INSERT INTO supplier(SupplierName, ContactNo, Address, Email, AccountNo) VALUES('$name','$contact','$address','$email','$result2[AccountNo]')";
			$result3 = mysqli_query($db,$sql3);

			if($result3){
				echo "Supplier Successfully Added";
			}
			exit();
		}

		if(isset($_POST['buttonsave'])){
			$query = "SELECT * FROM supplyrequest, requestlist, supplier";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['SupplyRequestNo'] . "</td>";
				echo "<td>" . $record['Name'] . "</td>";
				echo "<td>" . $record['SupplierName'] . "</td>";
				echo "<td>" . $record['Date'] . "</td>";
				echo "<td><a>View</a></td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['createrequest'])){				
			$query = "SELECT * FROM supplier";
			$result = mysqli_query($db, $query);

			echo "<option selected value=''>Select Supplier</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['SupplierNo'] . "'>" . $record['SupplierName'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['lowstockscheck'])){
			$Query = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND Stocks <= LowStock AND itemvariant.Owned = 1 AND itemvariant.SupplierNo = '$_POST[supplierID]'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
				echo "<td>" . $row['ItemNo'] . "-" . $row['VariantNo'] . "</td>";
				echo "<td>" . $row['Name'] . "<br>" . $row['Size'] . " " . $row['Color'] . " " . $row['Description'] . "</td>";
				echo "<td>" . $row['Stocks'] . "</td>";
				echo "<td>" . $row['LowStock'] . "</td>";
				echo "<td>" . $row['Critical'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['supplierselect'])){
			$sql= "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND item.SupplierNo = '" . $_POST["supplierID"] . "'";
			$result = mysqli_query($db, $sql);

			while($row = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td><a class='addto' onclick=addToPO('" . $row['ItemNo'] . "','" . $row['VariantNo'] . "')>+</a></td>";
				echo "<td>" . $row['ItemNo'] . "-" . $row['VariantNo'] . "</td>";
				echo "<td>" . $row['Name'] . "<br>" . $row['Size'] . " " . $row['Color'] . " " . $row['Description'] . "</td>";
				echo "<td>" . $row['DPOCost'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['addtopurchase'])){
			$sql = "SELECT ItemNo, VariantNo FROM requestlist WHERE ItemNo = " . $_POST["itemID"] . " AND VariantNo = " . $_POST["varID"] . " AND Temp = 1";
			$result = mysqli_query($db, $sql);

			if(mysqli_num_rows($result) > 0){
				exit();
			}
			else{
				$sql = "INSERT INTO requestlist(Temp, ItemNo, VariantNo) VALUES(1, " . $_POST["itemID"] . ", " . $_POST["varID"] . ")";
				$result = mysqli_query($db,$sql);

				if($result){
					echo "List Successfully Added";
				}
				exit();
			}
		}

		if(isset($_POST['showpurchase'])){
			$sql = "SELECT * FROM item, itemvariant, requestlist WHERE item.ItemNo = itemvariant.ItemNo AND item.ItemNo = requestlist.ItemNo AND itemvariant.VariantNo = requestlist.VariantNo AND item.SupplierNo = '" . $_POST["supplierID"] . "' AND Temp = 1";
			$result = mysqli_query($db, $sql);

			while($row = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td><a class='removefrom' onclick=removeFromPO('" . $row['ItemNo'] . "','" . $row['VariantNo'] . "')>-</a></td>";
				echo "<td><input type=text value='" . $row['Quantity'] . "' class=quantity onchange=quantityChange(this,'" . $row['ItemNo'] . "','" . $row['VariantNo'] . "') /></td>";
				echo "<td>" . $row['ItemNo'] . "-" . $row['VariantNo'] . "</td>";
				echo "<td>" . $row['Name'] . "<br>" . $row['Size'] . " " . $row['Color'] . " " . $row['Description'] . "</td>";
				echo "<td>" . $row['DPOCost'] . "</td>";
				echo "<td>" . $row['Total'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['removefrompo'])){
			$Query = "DELETE FROM requestlist WHERE itemNo = " . $_POST['itemID'] . " AND VariantNo = " . $_POST["varID"] . " AND Temp = 1";
			$myResult = mysqli_query($db, $Query);

			if($result){
				echo "Deleted Successfully";
			}
			exit();
		}

		if(isset($_POST['quantityupdate'])){
			$presql = "SELECT * FROM requestlist, item, itemvariant WHERE requestlist.itemNo = item.itemNo AND item.itemNo = itemvariant.itemNo AND Temp = 1 AND itemvariant.VariantNo = requestlist.VariantNo AND requestlist.ItemNo ='" . $_POST["itemID"] . "' AND requestlist.VariantNo ='" . $_POST["varID"] . "'";
			$preresult = mysqli_query($db,$presql);
			$data = mysqli_fetch_array($preresult);

			$sql = "UPDATE requestlist SET Quantity = " . $_POST['quantity'] . ", Total = " . $data['DPOCost'] * $_POST['quantity'] . " WHERE ItemNo = " . $_POST["itemID"] . " AND VariantNo = " . $_POST["varID"] . " AND Temp = 1";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "updated succesfully";
			}
			exit();
		}

		if(isset($_POST['totalamount'])){
			$postsql = "SELECT SUM(Total) AS TotalPOAmount FROM requestlist WHERE Temp = 1";
			$postresult = mysqli_query($db,$postsql);
			$data = mysqli_fetch_array($postresult);

			echo $data['TotalPOAmount'];

			exit();
		}

		if(isset($_POST['submitpo'])){
			date_default_timezone_set("Asia/Manila");
			$date = date('Y-m-d H:i:s');
			$datetime = date('Y-m-d H:i:s', strtotime($date));
            
            $firstsql = "SELECT * FROM requestlist WHERE Temp = 1";
            $firstresult = mysqli_query($db,$firstsql);
            $firstfetch = $data = mysqli_fetch_array($firstresult);
            
            if($firstfetch['Quantity'] != ''){
                $endsql = "DELETE FROM requestlist WHERE Quantity IS NULL AND Temp = 1";
                $endresult = mysqli_query($db,$endsql); 
                
                $sql = "INSERT INTO supplyrequest(Date, SupplierNo, isReceived) VALUES('$datetime','" . $_POST['suppID'] . "', 0)";
                $result = mysqli_query($db,$sql);

                $presql = "SELECT * FROM supplyrequest WHERE Date = '$datetime' AND SupplierNo = '" . $_POST['suppID'] . "'";
                $preresult = mysqli_query($db,$presql);
                $prefetch = $data = mysqli_fetch_array($preresult);

                $postsql = "UPDATE requestlist SET SupplyRequestNo ='" . $prefetch['SupplyRequestNo'] . "', Temp = 0 WHERE Temp = 1";
                $postresult = mysqli_query($db,$postsql);
                
                echo "Transaction Successful.";
            }
            else{
                $endsql = "DELETE FROM requestlist WHERE Temp = 1";
                $endresult = mysqli_query($db,$endsql); 
                
                echo "Transaction Failed.";
            }	

			exit();
		}

		if(isset($_POST['showpo'])){
			$query = "SELECT supplyrequest.SupplyRequestNo AS supno, SupplierName, Date FROM supplyrequest, supplier WHERE supplier.SupplierNo = supplyrequest.SupplierNo ORDER BY Date Desc";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				$prequery = "SELECT Count(*) AS NoofItems FROM requestlist WHERE SupplyRequestNo = '" . $record['supno'] . "' AND Quantity IS NOT NULL";
				$preresult = mysqli_query($db, $prequery);
				$prefetch = mysqli_fetch_array($preresult);
                
                if($prefetch['NoofItems'] > 0){
                    echo "<tr>";
                    echo "<td>" . $record['supno'] . "</td>";
                    echo "<td>" . $prefetch['NoofItems'] . "</td>";
                    echo "<td>" . $record['SupplierName'] . "</td>";
                    echo "<td>" . $record['Date'] . "</td>";
                    echo "<td><a>View</a><a>Print</a></td>";
                    echo "</tr>";
                }
			}
			exit();
		}

		if(isset($_POST['showreceivings'])){
			$query = "SELECT *
			FROM requestlist, supplyrequest, supplier, supply, item, itemvariant 
			WHERE supplyrequest.SupplierNo = supplier.SupplierNo
            AND item.ItemNo = itemvariant.ItemNo
            AND requestlist.VariantNo = itemvariant.VariantNo
			AND supplyrequest.SupplyRequestNo = requestlist.SupplyRequestNo
			AND supply.RequestListNo = requestlist.RequestListNo
			AND supplyrequest.SupplyRequestNo = supply.SupplyRequestNo
			AND requestlist.ItemNo = item.ItemNo
			AND isReceived = 1
            ORDER BY DateReceive Desc";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['SupplyNo'] . "</td>";
				echo "<td>" . date('Y-m-d', strtotime($record['DateReceive'])) . "</td>";
				echo "<td>" . $record['SupplierName'] . "</td>";
				echo "<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
				echo "<td>" . $record['QuantityReceived'] . "</td>";
				echo "<td>" . $record['PendingQuantity'] . "</td>";
				echo "<td>" . $record['Quantity'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['showreceive'])){
			$query = "SELECT supplyrequest.SupplyRequestNo AS supno, SupplierName, Date FROM supplyrequest, supplier WHERE supplier.SupplierNo = supplyrequest.SupplierNo AND isReceived = 0 ORDER BY Date Desc";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				$prequery = "SELECT COUNT(RequestListNo) AS NoofItems FROM requestlist WHERE SupplyRequestNo = ". $record['supno'] ." AND Quantity IS NOT NULL";
				$preresult = mysqli_query($db, $prequery);
				$prefetch = mysqli_fetch_array($preresult);
                
                if($prefetch['NoofItems'] > 0){
                    echo "<tr>";
				    echo "<td>" . $record['supno'] . "</td>";
                    echo "<td>" . $prefetch['NoofItems'] . "</td>";
                    echo "<td>" . $record['SupplierName'] . "</td>";
                    echo "<td>" . date('Y-m-d', strtotime($record['Date'])) . "</td>";
                    echo "</tr>";  
                }
			}
			exit();
		}

		if(isset($_POST['showrecevingform'])){
			$query = "SELECT *, requestlist.RequestListNo as reqno FROM item, itemvariant, supplyrequest, requestlist WHERE item.ItemNo = requestlist.itemNo AND item.ItemNo = itemvariant.itemNo AND requestlist.VariantNo = itemvariant.VariantNo AND requestlist.SupplyRequestNo = supplyrequest.SupplyRequestNo AND supplyrequest.SupplyRequestNo = '" . $_POST['POID'] . "'";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['reqno'] . "</td>";
				echo "<td><input type=text value='" . $record['Received'] . "' onchange=receiveChange(this,'" . $record['reqno'] . "') /></td>";
				echo "<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
				echo "<td>" . $record['Quantity'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['receiveupdate'])){
			$sql = "UPDATE requestlist SET Received = " . $_POST['receive'] . " WHERE RequestListNo = " . $_POST["RLID"] . " AND Temp = 0";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "updated succesfully";
			}
			exit();
		}

		if(isset($_POST['submitreceive'])){
			date_default_timezone_set("Asia/Manila");
			$date = date('Y-m-d H:i:s');
			$datetime = date('Y-m-d H:i:s', strtotime($date));

			$presql = "SELECT * FROM requestlist WHERE SupplyRequestNo = '" . $_POST['POID'] . "'";
			$preresult = mysqli_query($db,$presql);

			while($prefetch = mysqli_fetch_array($preresult)){
				$sql = "INSERT INTO supply(QuantityReceived, PendingQuantity, DateReceive, RequestListNo, SupplyRequestNo) VALUES('" . $prefetch['Received']  . "', '" . ($prefetch['Quantity'] - $prefetch['Received']) . "', '$datetime', '" . $prefetch['RequestListNo'] . "', '" . $prefetch['SupplyRequestNo'] . "')";
				$result = mysqli_query($db,$sql);

				$sql2 = "UPDATE supplyrequest SET isReceived = 1 WHERE SupplyRequestNo = '" . $prefetch['SupplyRequestNo'] . "'";
				$result2 = mysqli_query($db,$sql2);

				$presql2 = "SELECT *, itemvariant.VariantNo as VARNO FROM item, itemvariant, requestlist, supplyrequest WHERE item.ItemNo = requestlist.ItemNo AND item.ItemNo = itemvariant.ItemNo AND requestlist.SupplyRequestNo = supplyrequest.SupplyRequestNo AND requestlist.VariantNo = itemvariant.VariantNo AND supplyrequest.SupplyRequestNo = '" . $_POST['POID'] . "'";
				$preresult2 = mysqli_query($db,$presql2);
				$prefetch2 = mysqli_fetch_array($preresult2);

				$postsql = "UPDATE itemvariant SET Stocks = '" . ($prefetch2['Stocks']+$prefetch['Received']) . "' WHERE VariantNo ='" . $prefetch2['VARNO'] . "'";
                $postresult = mysqli_query($db,$postsql);
			}
			exit();
		}
        
        if(isset($_POST['showbackorders'])){
			$query = "SELECT *, requestlist.RequestListNo AS reqno FROM supplyrequest, requestlist, supply, itemvariant, item WHERE item.ItemNo = itemvariant.ItemNo AND item.ItemNo = requestlist.ItemNo AND itemvariant.VariantNo = requestlist.VariantNO AND requestlist.SupplyRequestNo = supplyrequest.SupplyRequestNo AND requestlist.RequestListNo = supply.RequestListNo AND PendingQuantity > 0";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['reqno'] . "</td>";
				echo "<td>" . $record['SupplierName'] . "</td>";
				echo "<td>" . $record['Name'] . "<br>". $record['Size'] ." ". $record['Color'] ." ". $record['Description'] ."</td>";
				echo "<td>" . $record['Received'] . "</td>";
				echo "<td>" . $record['PendingQuantity'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['showsupplier'])){
			$query = "SELECT * FROM supplier";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['SupplierNo'] . "</td>";
				echo "<td>" . $record['SupplierName'] . "</td>";
				echo "<td>" . $record['Address'] . "</td>";
				echo "<td>" . $record['ContactNo'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['buttonviewsupp'])){
			$suppNo = $_POST['vSuppNo'];

			$sql = "SELECT * FROM supplier WHERE SupplierNo = '$suppNo' ";
			$result = mysqli_query($db,$sql);

			while($record = mysqli_fetch_array($result)){
				echo "<input value='" . $record['SupplierName'] . "' readonly/><br>";
			}
			exit();
		}

		if(isset($_POST['buttonviewsuppdetails'])){
			$suppNo = $_POST['vSuppNo'];

			$sql = "SELECT * FROM supplier WHERE SupplierNo = '$suppNo' ";
			$result = mysqli_query($db,$sql);

			while($record = mysqli_fetch_array($result)){
				echo "<h3>Address: </h3><input class='SupInfo' value='" . $record['Address'] . "' readonly /><br>	
					<h3>Contact: </h3><input class='SupInfo' value='" . $record['ContactNo'] . "' readonly /><br>
					<h3>Email Address: </h3><input class='SupInfo' value='" . $record['Email'] . "' readonly/><br>";
			}
			exit();
		}

		if(isset($_POST['buttonviewsuppitems'])){
			$suppNo = $_POST['vSuppNo'];

			$sql = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant, level1, level2, level3 WHERE item.SupplierNo = '$suppNo' AND item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.ItemNo = itemvariant.ItemNo";
			$result = mysqli_query($db,$sql);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['ITNO'] . "-" . $record['VariantNo'] . "</td>";
				echo "<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
				echo "<td>" . $record['Name1'] . " > " . $record['Name2'] . " > " . $record['Name3'] . "</td>";
				echo "<td>" . $record['DPoCost'] . "</td>";
				echo "<td>" . $record['SRP'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['showaddlist'])){
			$sql = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND itemvariant.Owned = 0";
			$result = mysqli_query($db,$sql);

			while($record = mysqli_fetch_array($result)){
				echo "<tr>";
				echo "<td>" . $record['ItemNo'] . "-" . $record['VariantNo'] . "</td>";
				echo "<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
				echo "</tr>";
			}
			exit();
		}

		if(isset($_POST['showaddform'])){
            $str = $_POST['ItemID'];
            list($itmID, $varID) = explode("-",$str);
            
            $sql = "SELECT * FROM item, itemvariant, level1, level2, level3 WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.ItemNo = itemvariant.ItemNo AND item.ItemNo = '$itmID' AND VariantNo = '$varID'";
            $result = mysqli_query($db,$sql);
            $fetch = mysqli_fetch_array($result);

			echo "<tr><td>Variant No: </td><td>" . $fetch['VariantNo'] . "</td></tr>";
			echo "<tr><td>Name: </td><td>" . $fetch['Name'] . "</td></tr>";
			echo "<tr><td>Description: </td><td>" . $fetch['Size'] . " " . $fetch['Color'] . " " . $fetch['Description'] . "</td></tr>";
			echo "<tr><td>Family: </td><td>" . $fetch['Name1'] . "</td></tr>";
			echo "<tr><td>Category: </td><td>" . $fetch['Name2'] . "</td></tr>";
			echo "<tr><td>Subcategory: </td><td>" . $fetch['Name3'] . "</td></tr>";
			echo "<tr><td>Default PO Cost: </td><td>" . $fetch['DPOCost'] . "</td></tr>";
			echo "<tr><td>SRP: </td><td>" . $fetch['SRP'] . "</td></tr>";

			echo "<tr><td>Price: </td><td><input id='itmPrice' ></input></td></tr>";
			echo "<tr><td>Low Stock Level: </td><td><input id='itmLSLvl' ></input></td></tr>";
			echo "<tr><td>Critical Level: </td><td><input id='itmCritLvl' ></input></td></tr>";

			exit();
		}

		if(isset($_POST['submitadd'])){
            $str = $_POST['itemID'];
            list($itmID, $varID) = explode("-",$str);
            
            echo $itmID;
            echo $varID;
            
			$sql = "UPDATE item SET Owned = 1 WHERE ItemNo = '$itmID'";
			$result = mysqli_query($db,$sql);
            
            $postsql = "UPDATE itemvariant SET Price = $_POST[price], LowStock = $_POST[LStocks], Critical = $_POST[Crit], Owned = 1 WHERE ItemNo = '$itmID' AND VariantNo = '$varID'";
			$postresult = mysqli_query($db,$postsql);
            
			exit();
		}

		if(isset($_POST['familyoptions'])){
			$query = "SELECT * FROM family";
			$result = mysqli_query($db, $query);

			echo "<option selected hidden>Family</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['FamilyNo'] . "'>" . $record['Family'] . "</option>";
			}
			exit();
		}

		if(isset($_POST['categoryoptions'])){
			$query = "SELECT * FROM category, family WHERE category.FamilyNo = family.FamilyNo";
			$result = mysqli_query($db, $query);

			echo "<option selected hidden>Category</option>";
			while($record = mysqli_fetch_array($result)){
				echo "<option value='" . $record['CategoryNo'] . "'>" . $record['Category'] . "</option>";
			}
			exit();
		}			

		if(isset($_POST['buttonaddfam'])){
            if($_POST['catlvl1'] != ''){
                $sql = "UPDATE level1 SET Name1 = '". $_POST['name'] ."' WHERE Level1No = $_POST[catlvl1]";
            }
            if($_POST['catlvl2'] != ''){
                $sql = "UPDATE level2 SET Name2 = '". $_POST['name'] ."' WHERE Level2No = $_POST[catlvl2]";
            }
            if($_POST['catlvl3'] != ''){
                $sql = "UPDATE level3 SET Name3 = '". $_POST['name'] ."' WHERE Level3No = $_POST[catlvl3]";
            }
			$result = mysqli_query($db,$sql);

			if($result){
				echo "Successfully Added";
			}
			exit();
		}

		if(isset($_POST['buttonaddcat'])){
			$sql = "INSERT INTO category(Category, CategoryDescription, FamilyNo) VALUES('$_POST[aname]','$_POST[adesc]','$_POST[afam]')";
			$result = mysqli_query($db,$sql);

			if($result){
				echo "Successfully Added";
			}
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

		if(isset($_POST['efaminfo'])){
			$ID = $_POST['eFamID'];
			$name = $_POST['eName'];
			$desc = $_POST['eDesc'];

			$sql = "UPDATE family SET Family = '$name', FamilyDescription = '$desc' WHERE FamilyNo = $ID";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "Updated Successfully";
			}
			exit();
		}

		if(isset($_POST['ecatinfo'])){
			$ID = $_POST['eCatID'];
			$name = $_POST['eName'];
			$desc = $_POST['eDesc'];
			$fam = $_POST['efam'];

			$sql = "UPDATE category SET Category = '$name', CategoryDescription = '$desc', FamilyNo = '$fam' WHERE CategoryNo = $ID";

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

		if(isset($_POST['countconfirm'])){
			$physItemNo = $_POST['cnt_itmNo'];
			$physStock = $_POST['cnt_itmStock'];

			$sql = "UPDATE itemvariant SET Stocks = '$physStock' WHERE VariantNo = $physItemNo";

			$result = mysqli_query($db,$sql);

			if($result){
				echo "Updated Successfully";
			}
			exit();
		}

    if(isset($_POST['dfaminfo'])){
        if($_POST['catlvl1'] != ''){
                $sql = "DELETE FROM level1 WHERE Level1No = $_POST[catlvl1]";
            }
            if($_POST['catlvl2'] != ''){
                $sql = "DELETE FROM level2 WHERE Level2No = $_POST[catlvl2]";
            }
            if($_POST['catlvl3'] != ''){
                $sql = "DELETE FROM level3 WHERE Level3No = $_POST[catlvl3]";
            }
			$result = mysqli_query($db,$sql);

			if($result){
				echo "Successfully Added";
			}
        exit();
    }

	if(isset($_POST['dcatinfo'])){
		$ID = $_POST['dCatID'];

		$sql = "DELETE FROM category WHERE CategoryNo = $ID";
		$result = mysqli_query($db,$sql);

		if($result){
			echo "Successfully Added";
		}
		exit();
	}

	if(isset($_POST['showinventory'])){
		$query = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant, level1, level2, level3 WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.ItemNo = itemvariant.ItemNo AND itemvariant.Owned = 1";
		$result = mysqli_query($db, $query);

		while($record = mysqli_fetch_array($result)){
			echo "<tr>
				<td>" . $record['ITNO'] . "-" . $record['VariantNo'] . "</td>
				<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>
                <td>" . $record['Name1'] . " > " . $record['Name2'] . " > " . $record['Name3'] . "</td>";

            $prequery = "SELECT SUM(Quantity) AS Commit FROM item, itemvariant, tblorder, orderlist WHERE item.ItemNo = orderlist.ItemNo AND item.ItemNo = itemvariant.ItemNo AND itemvariant.VariantNo = orderlist.VariantNo AND orderlist.OrderNo = tblorder.OrderNo AND STATUS = 'Process' AND itemvariant.Removed = 0 AND itemvariant.Owned = 1 AND orderlist.VariantNo = " . $record['VariantNo'];
            $preresult = mysqli_query($db, $prequery);
            $prefetch = mysqli_fetch_array($preresult);

			echo "<td>" . ($record['Stocks'] - $prefetch['Commit']) . "</td>
				<td>" . $record['Stocks'] . "</td>
				<td>" . $prefetch['Commit'] . "</td>
				</tr>";
		}
		exit();
	}

	if(isset($_POST['showitem'])){
		$query = "SELECT * FROM item, level1, level2, level3, supplier WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.Removed = 0 AND item.Owned = 1 AND item.SRemoved = 0 AND item.SupplierNo = supplier.SupplierNo";
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
			echo "<td>" . $record['SupplierName'] . "</td>";
			echo "</tr>";
		}
		exit();
	}
    
    if(isset($_POST['itemvariant'])){
		$query = "SELECT * FROM itemvariant WHERE ItemNo = '". $_POST['viewItemNo'] ."'";
        echo $query;
		$result = mysqli_query($db, $query);	

		while($record = mysqli_fetch_array($result)){
            echo "<tr>";
			echo "<td>" . $record['VariantNo'] . "</td>";
			echo "<td>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
			echo "<td>" . $record['Stocks'] . "</td>";
			echo "<td>" . $record['LowStock'] . "</td>";
			echo "<td>" . $record['Critical'] . "</td>";
			echo "<td>" . $record['DPOCost'] . "</td>";
			echo "<td>" . $record['Price'] . "</td>";
			echo "</tr>";
		}
		exit();
	}

	if(isset($_POST['showremove'])){
		$query = "SELECT * FROM item, level1, level2, level3 WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND Removed = 1 AND Owned = 1";
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

		if(isset($_POST['showcritical'])){
			$query = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant, level1, level2, level3, supplier WHERE item.Level1No = level1.Level1No AND item.Level2No = level2.Level2No AND item.Level3No = level3.Level3No AND item.ItemNo = itemvariant.ItemNo AND item.SupplierNo = supplier.SupplierNo AND itemvariant.Owned = 1";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				if($record['Stocks'] <= $record['LowStock']){
                    echo "<tr>";
                    echo "<td>" . $record['ITNO'] . "-" . $record['VariantNo'] . "</td>";
                    echo "<td>" . $record['Name'] . "<br>" . $record['Size'] . " " . $record['Color'] . " " . $record['Description'] . "</td>";
                    echo "<td>" . $record['SupplierName'] . "</td>";
                    echo "<td>" . $record['Stocks'] . "</td>";
                    echo "<td>" . $record['LowStock'] . "</td>";
                    echo "<td>" . $record['Critical'] . "</td>";
                    echo "</tr>";
				}
			}
			exit();
		}

		if(isset($_POST['showcategory'])){
			$query = "SELECT * FROM level1";
			$result = mysqli_query($db, $query);

			while($record = mysqli_fetch_array($result)){
				$presql = "SELECT Level2No, COUNT(Name2) as cntr FROM level2 WHERE level1No = " . $record['Level1No'];
				$preresult = mysqli_query($db, $presql);
				$prefetch = mysqli_fetch_array($preresult);

				$postsql = "SELECT COUNT(Name3) as cntr2 FROM level3 WHERE level1No = " . $record['Level1No'];
				$postresult = mysqli_query($db, $postsql);
				$postfetch = mysqli_fetch_array($postresult);

				$cntr2 = $postfetch['cntr2'];

				echo "<tr class=hthead>";
				echo "<td class='border1st' onclick=valuetemp('" . $record['Level1No'] . "',1) rowspan='" . ($prefetch['cntr'] + $cntr2 + 1) . "'>" . $record['Name1'] . "</td>";

				$query1 = "SELECT * FROM level1, level2 WHERE level1.Level1No = level2.Level1No AND level2.Level1No =" . $record['Level1No'];
				$result1 = mysqli_query($db, $query1);

				while($record1 = mysqli_fetch_array($result1)){
					$postsql = "SELECT COUNT(Name3) as cntr2 FROM level3 WHERE level2No = " . $record1['Level2No'];
					$postresult = mysqli_query($db, $postsql);
					$postfetch = mysqli_fetch_array($postresult);

					$cntr2 = $postfetch['cntr2'];

					echo "<td class='border2nd' onclick=valuetemp('" . $record1['Level2No'] . "',2) rowspan='" . ($cntr2 + 1) . "'>" . $record1['Name2'] . "</td>";

					$query2 = "SELECT * FROM level1, level2, level3 WHERE level1.Level1No = level2.Level1No AND level2.Level2No = level3.Level2No AND level3.Level1No = level1.Level1No AND level3.Level2No = " . $record1['Level2No'];
					$result2 = mysqli_query($db, $query2);

					while($record2 = mysqli_fetch_array($result2)){
						echo "<td class='border3rd' onclick=valuetemp('" . $record2['Level3No'] . "',3)>" . $record2['Name3'] . "</td>";
						echo "</tr>";
						echo "<tr class=htbody>";
					}
					echo "<td id='catAddShift".$record['Level1No']."-".$record1['Level2No']."' class='border2nd'><a class=catAdd id=catAdd".$record['Level1No']."-".$record1['Level2No']." onclick=catAddText3(" . $record1['Level2No'] . "," . $record['Level1No'] . ")>Add</a></td></tr>";

					echo "</tr>";
					echo "<tr class=httail>";
				}
				echo "<td id='catAddShift".$record['Level1No']."' class='border1st' colspan=2><a class=catAdd id=catAdd".$record['Level1No']." onclick=catAddText2(" . $record['Level1No'] . ")>Add</a></td></tr>";

				echo "</tr>";
			}
			echo "<tr><td id='catAddShift' colspan=3><a class=catAdd id=catAdd onclick=catAddText()>Add</a></td>></tr>";
			exit();
		}

		if(isset($_POST['savecategory'])){
			$query = "INSERT INTO level1(Name1) VALUE('".$_POST['newCat']."')";
			$result = mysqli_query($db, $query);

			echo $query;
			exit();
		}
		if(isset($_POST['savecategory2'])){
			$query = "INSERT INTO level2(Name2, Level1No) VALUE('".$_POST['newCat']."','".$_POST['cat1No']."')";
			$result = mysqli_query($db, $query);

			echo $query;
			exit();
		}
		if(isset($_POST['savecategory3'])){
			$query = "INSERT INTO level3(Name3, Level1No, Level2No) VALUE('".$_POST['newCat']."','".$_POST['cat1No']."','".$_POST['cat2No']."')";
			$result = mysqli_query($db, $query);

			echo $query;
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

		if(isset($_POST['editcategory'])){
            if($_POST['catlvl1'] != ''){
                 $sql = "SELECT Name1 AS Category FROM level1 WHERE level1No = '" . $_POST['catlvl1'] . "'";
            }
            else if($_POST['catlvl2'] != ''){
                 $sql = "SELECT Name2 AS Category FROM level2 WHERE level2No = '" . $_POST['catlvl2'] . "'";
            }
            else if($_POST['catlvl3'] != ''){
                 $sql = "SELECT Name3 AS Category FROM level3 WHERE level3No = '" . $_POST['catlvl3'] . "'";
            }
           
			$result = mysqli_query($db, $sql);
			$fetch = mysqli_fetch_array($result);
            
			echo $fetch['Category'];
            
            exit();
		}

		if(isset($_POST['deletecategory'])){
            if($_POST['catlvl1'] != ''){
                 $sql = "SELECT Name1 AS Category FROM level1 WHERE level1No = '" . $_POST['catlvl1'] . "'";
            }
            else if($_POST['catlvl2'] != ''){
                 $sql = "SELECT Name2 AS Category FROM level2 WHERE level2No = '" . $_POST['catlvl2'] . "'";
            }
            else if($_POST['catlvl3'] != ''){
                 $sql = "SELECT Name3 AS Category FROM level3 WHERE level3No = '" . $_POST['catlvl3'] . "'";
            }
           
			$result = mysqli_query($db, $sql);
			$fetch = mysqli_fetch_array($result);
            
			echo $fetch['Category'];
            
            exit();
		}

		if(isset($_POST['edit_fam'])){
			$Query = "SELECT * FROM family, item WHERE item.FamilyNo = family.FamilyNo AND ItemNo = '" . $_POST["eItemID"] . "'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo '<option value= "' . $row['FamilyNo'] . '" selected hidden>' . $row['Family'] . '</option>';
			}
			$Query = "SELECT * FROM family";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo '<option value= "' . $row['FamilyNo'] . '">' . $row['Family'] . '</option>';
			}
			exit();
		}

		if(isset($_POST['edit_cat'])){
			$Query = "SELECT * FROM category, item WHERE item.CategoryNo = category.CategoryNo AND ItemNo = '" . $_POST["eItemID"] . "'";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo '<option value= "' . $row['CategoryNo'] . '" selected hidden>' . $row['Category'] . '</option>';
			}

			$Query = "SELECT * FROM category, family WHERE family.FamilyNo = category.FamilyNo ";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo '<option value= "' . $row['CategoryNo'] . '">' . $row['Category'] . '</option>';
			}
			exit();
		}

		if(isset($_POST['depInpt'])){
			$query = "SELECT * FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND VariantNo = '" . $_POST["itm_ID"] . "'";
			$result = mysqli_query($db, $query);

			while($row = mysqli_fetch_array($result)){
				echo '<script> $("#physName").val("' . $row["Name"] . ' (' . $row["Size"] . ' ' . $row["Color"] . ' ' . $row["Description"] . ')")</script>';
			}
			exit();
		}

		if(isset($_POST['physSlct'])){
			if($_POST["itm_name"] != ''){
				$Query = "SELECT ItemNo FROM item WHERE Name = '" . $_POST["itm_name"] . "'";

				$myResult = mysqli_query($db, $Query);

				while($row = mysqli_fetch_array($myResult)){
					echo '<option selected value= "' . $row['ItemNo'] . '">' . $row['ItemNo'] . '</option>';
				}

				$Query = "SELECT ItemNo FROM item WHERE Name != '" . $_POST["itm_name"] . "'";

				$myResult = mysqli_query($db, $Query);

				while($row = mysqli_fetch_array($myResult)){
					echo '<option value= "' . $row['ItemNo'] . '">' . $row['ItemNo'] . '</option>';
				}
				exit();
			}
			else{
				$Query = "SELECT ItemNo FROM item ORDER BY ItemNo";
				echo '<option selected hidden value= " ">Item No</option>';

				$myResult = mysqli_query($db, $Query);

				while($row = mysqli_fetch_array($myResult)){
					echo '<option value= "' . $row['ItemNo'] . '">' . $row['ItemNo'] . '</option>';
				}
				exit();
			}
		}

        if(isset($_POST['allorders'])){
                $query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo";
                $result = mysqli_query($db, $query);	

                while($record = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $record['OrderNo'] . "</td>";
                        echo "<td>" . $record['Lastname'] . ", " . $record['Firstname'] . "</td>";
                        echo "<td>" . $record['Address'] . "</td>";
                        echo "<td>" . $record['Date'] . "</td>";
                        echo "<td>" . $record['TotalAmount'] . "</td>";
                        echo "<td>" . $record['Status'] . "</td>";
                        echo "<td><a >View</></td>";
                        echo "</tr>";
                }
                exit();
        }
        if(isset($_POST['neworders'])){
                $query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='New'";
                $result = mysqli_query($db, $query);	

                while($record = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $record['OrderNo'] . "</td>";
                        echo "<td>" . $record['Lastname'] . ", " . $record['Firstname'] . "</td>";
                        echo "<td>" . $record['Address'] . "</td>";
                        echo "<td>" . $record['Date'] . "</td>";
                        echo "<td>" . $record['TotalAmount'] . "</td>";
                        echo "<td><a >View</></td>";
                        echo "</tr>";
                }
                exit();
        }

        if(isset($_POST['processorders'])){
                $query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='Process'";
                $result = mysqli_query($db, $query);	

                while($record = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $record['OrderNo'] . "</td>";
                        echo "<td>" . $record['Lastname'] . ", " . $record['Firstname'] . "</td>";
                        echo "<td>" . $record['Address'] . "</td>";
                        echo "<td>" . $record['Date'] . "</td>";
                        echo "<td>" . $record['TotalAmount'] . "</td>";
                        echo "<td><a >View</></td>";
                        echo "</tr>";
                }
                exit();
        }
        if(isset($_POST['shiporders'])){
                $query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='Ship'";
                $result = mysqli_query($db, $query);	

                while($record = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $record['OrderNo'] . "</td>";
                        echo "<td>" . $record['Lastname'] . ", " . $record['Firstname'] . "</td>";
                        echo "<td>" . $record['Address'] . "</td>";
                        echo "<td>" . $record['Date'] . "</td>";
                        echo "<td>" . $record['TotalAmount'] . "</td>";
                        echo "<td><a >View</></td>";
                        echo "</tr>";
                }
                exit();
        }
        if(isset($_POST['cancelorders'])){
                $query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='Cancel'";
                $result = mysqli_query($db, $query);	

                while($record = mysqli_fetch_array($result)){
                        echo "<tr>";
                        echo "<td>" . $record['OrderNo'] . "</td>";
                        echo "<td>" . $record['Lastname'] . ", " . $record['Firstname'] . "</td>";
                        echo "<td>" . $record['Address'] . "</td>";
                        echo "<td>" . $record['Date'] . "</td>";
                        echo "<td>" . $record['TotalAmount'] . "</td>";
                        echo "<td><a >View</></td>";
                        echo "</tr>";
                }
                exit();
        }     

        if(isset($_POST['neworderstopro'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='New' AND Temp = 0";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='addto' onclick=addToMove('Process','" . $row['OrderNo'] . "')>+</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['neworderstocan'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='New' AND Temp = 0";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='addto' onclick=addToMove('Cancel','" . $row['OrderNo'] . "')>+</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['proorderstoship'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='Process' AND Temp = 0";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='addto' onclick=addToMove('Ship','" . $row['OrderNo'] . "')>+</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['prolistshow'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='New' AND Temp = 1";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='removefrom' onclick=removeFromMove('Process','" . $row['OrderNo'] . "')>-</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['canlistshow'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='New' AND Temp = 1";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='removefrom' onclick=removeFromMove('Cancel','" . $row['OrderNo'] . "')>-</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['shplistshow'])){
			$Query = "SELECT * FROM tblorder, customer WHERE tblorder.OrderNo = customer.OrderNo AND tblorder.Status ='Process' AND Temp = 1";
			$myResult = mysqli_query($db, $Query);

			while($row = mysqli_fetch_array($myResult)){
				echo "<tr>";
									echo "<td><a class='removefrom' onclick=removeFromMove('Ship','" . $row['OrderNo'] . "')>-</a></td>";
									echo "<td>" . $row['OrderNo'] . "</td>";
									echo "<td>" . $row['Lastname'] . ", " . $row['Firstname'] . "</td>";
									echo "<td>" . date('Y-m-d', strtotime($row['Date'])). "</td>";
									echo "<td>" . $row['TotalAmount'] . "</td>";
									echo "</tr>";
			}
			exit();
		}

        if(isset($_POST['addtoprocess'])){
			$sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'New'";
			$result = mysqli_query($db, $sql);

							$presql = "UPDATE tblorder SET Temp = 1 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'New'";
							$preresult = mysqli_query($db,$presql);

							if($result){
									echo "List Successfully Added";
							}
							exit();
		}
        if(isset($_POST['addtocancel'])){
                $sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'New'";
                $result = mysqli_query($db, $sql);

                $presql = "UPDATE tblorder SET Temp = 1 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'New'";
                $preresult = mysqli_query($db,$presql);
                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }
        if(isset($_POST['addtoship'])){
                $sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'Process'";
                $result = mysqli_query($db, $sql);

                $presql = "UPDATE tblorder SET Temp = 1 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 0 AND Status = 'Process'";
                $preresult = mysqli_query($db,$presql);
                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }

        if(isset($_POST['removetoprocess'])){
			$sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'New'";
			$result = mysqli_query($db, $sql);

							$presql = "UPDATE tblorder SET Temp = 0 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'New'";
							$preresult = mysqli_query($db,$presql);

							if($result){
									echo "List Successfully Added";
							}
							exit();
		}
        if(isset($_POST['removetocancel'])){
                $sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'New'";
                $result = mysqli_query($db, $sql);

                $presql = "UPDATE tblorder SET Temp = 0 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'New'";
                $preresult = mysqli_query($db,$presql);
                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }
        if(isset($_POST['removetoship'])){
                $sql = "SELECT OrderNo FROM tblorder WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'Process'";
                $result = mysqli_query($db, $sql);

                $presql = "UPDATE tblorder SET Temp = 0 WHERE OrderNo = " . $_POST["ORNO"] . " AND Temp = 1 AND Status = 'Process'";
                $preresult = mysqli_query($db,$presql);
                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }

        if(isset($_POST['proccessingorders'])){
                $presql = "UPDATE tblorder SET Temp = 0, Status = 'Process' WHERE Temp = 1 AND Status = 'New'";
                $preresult = mysqli_query($db,$presql);

                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }
        if(isset($_POST['cancellingorders'])){
                $presql = "UPDATE tblorder SET Temp = 0, Status = 'Cancel' WHERE Temp = 1 AND Status = 'New'";
                $preresult = mysqli_query($db,$presql);
                if($result){
                        echo "List Successfully Added";
                }
                exit();
        }
        if(isset($_POST['shippingorders'])){
                $presql = "UPDATE tblorder SET Temp = 0, Status = 'Ship' WHERE Temp = 1 AND Status = 'Process'";
                $preresult = mysqli_query($db,$presql);

                $sql1 = "SELECT *, tblorder.OrderNo AS ORNO, itemvariant.VariantNo AS VARNO, Quantity FROM item, itemvariant, orderlist, tblorder WHERE tblorder.Temp = 0 AND STATUS = 'Ship' AND orderlist.ItemNo = item.ItemNo AND itemvariant.ItemNo = item.ItemNo AND itemvariant.VariantNo = orderlist.VariantNo AND orderlist.OrderNo = tblorder.OrderNo AND Ship = 0";
                $result1 = mysqli_query($db,$sql1);
                $fetch1 = mysqli_fetch_array($result1);

                $sql = "UPDATE itemvariant SET Stocks = Stocks - " . $fetch1['Quantity'] . " WHERE VariantNo = ". $fetch1['VARNO'];
                $result = mysqli_query($db,$sql);

                $postsql = "UPDATE tblorder SET Ship = 1 WHERE OrderNo = " . $fetch1['ORNO'];
                $postresult = mysqli_query($db,$postsql);

                if($result){
                    echo "List Successfully Added";
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
	<link rel="stylesheet" type="text/css" href="Assets/css/main.css"></link>
	<link rel="stylesheet" type="text/css" media="all" href="Assets/css/font.css"></link>

	<script src="Assets/js/Ajax1.9.1.js"></script>
	<script src="Assets/js/Jquery1.11.3.js"></script>
	<script src="Assets/js/Jquery1.12.4.js"></script>
	<script src="Assets/js/main.js"></script>
</head>
<body>
	<div class="navi">
		<div class="logo"></div>
		<div>
			<ul>
				<li id="naviPurchaseOrder" class="naviselected">Purchase Order</li>
				<li id="naviInventory">Inventory</li>
				<li id="naviOrders">Orders</li>
				<li id="naviReports">Reports</li>
			</ul>
		</div>
		<div class='admindrop'>
			<div class='dp'></div>
			<div class='arrowdown'></div>
		</div>
	</div>

	<div id='adminmenu'>
		<ul>
			<li id="Logout"><a href="logout.php">Log out</a></li>
							<li id="Logout"><a href="New folder/forum1/index.php">Forum</a></li>
		</ul>
	</div>

	<div class="topbar enable">
	<input type="text" placeholder="Search" id='searchCmd'></input>
		<div class="topbarcmd1 cmd">
			<div class='showNothing'>
			</div>
			<div>
				<a id='btnCrtReq'>Create Request</a>
			</div>
			<div>
				<a id='btnDirRec'>Direct Receive</a>
			</div>
			<div>
				<a id='btnSenNot'>Send Notification</a>
			</div>
			<div>
				<a id='btnViewSup'>View</a>
				<a id='btnAddSup'>Add</a>
			</div>
		</div>
		<div class="topbarcmd2 cmd">
			<div class='showNothing'>
			</div>
			<div>
				<a id='btnPC'>Physical Count</a>
			</div>
			<div>
				<a id='btnView'>View</a>
				<a id='btnAdd'>Add</a>
			</div>
			<div>
				<a id='btnDelete'>Delete</a>
				<a id='btnEdit'>Edit</a>
			</div>
			<div>
				<a id='btnViewRev'>View</a>
			</div>
		</div>
		<div class="topbarcmd3 cmd">
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
	</div>

	<div class="sidebar">
		<div class="topMenu">
			<div id="POMenu" class="visible">
				<ul>
					<li id='sdePO1' class='sdeselected'>Purchase Order</li>
					<li id='sdePO2'>Receivings</li>
					<li id='sdePO3'>Back Orders</li>
					<li id='sdePO4'>Suppliers</li>
				</ul>
			</div>
			<div id="ordersMenu">
				<ul>
					<li id='sdeOrders1' class='sdeselected'>All</li>
					<li id='sdeOrders2'>New</li>
					<li id='sdeOrders3'>Processing</li>
					<li id='sdeOrders4'>Shipped</li>
					<li id='sdeOrders5'>Cancelled</li>
				</ul>
			</div>
			<div id="inventoryMenu">
				<ul>
					<li id='sdeInventory1'>Inventory</li>
					<li id='sdeInventory2'>Items</li>
					<li id='sdeInventory3'>Low Stocks</li>
					<li id='sdeInventory4'>Categories</li>
					<li id='sdeInventory5'>Removed</li>
				</ul>
			</div>
			<div id="reportsMenu">
				<ul>
					<li id='sdeReports1'>Sales</li>
					<li id='sdeReports2'>Fast/Slow Moving</li>
					<li id='sdeReports3'>Customers</li>
				</ul>
			</div>
			<div class='selector'></div>
		</div>
	</div>

	<div class="table">
		<div id="divClone" class="whole">
			<h1>Purchase Order</h1>

			<div class="tblContainertwolines">
				<div class="unscrollabletwolines">
					<table id='tblPurchaseOrder'>
						<thead>
							<tr>
								<th style = 'width: 20%' >No<br>No</th>
								<th style = 'width: 20%'>No of Items<br>No of Items</th>
								<th style = 'width: 30%'>Supplier<br>Supplier</th>
								<th style = 'width: 10%'>Date<br>Date</th>
								<th style = 'width: 20%'>Action<br>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollabletwolines">
					<table>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="cloneviewSupForm" class="whole">
			<div id="supFormHead">
				<div>

				</div>
				<div>	

				</div>
			</div>
			<span class="divider"></span>
			<div id="supFormBody">
				<div class="tblContaineritemdisplay">
					<div class="unscrollableitemdisplay">
						<table>
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Description</th>
									<th>Size</th>
									<th>Unit</th>
									<th>Category</th>
									<th>Default<br>PO Cost</th>
									<th>SRP</th>
								</tr>
							</thead>
						</table>
					</div>
					<div class="scrollableitemdisplay">
						<table>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div id="PurchaseOrderFormClone" class="whole">
			<div id="POForm" class="Head">	
				<h2>Supplier: </h2>
				<select>
					<option selected>Select Supplier</option>
				</select>
			</div>
			<div id="POForm" class="Body">
				<div id="POForm" class="Left">
					<div class="tblContainerPOForm">
						<div class="unscrollablePOForm">
							<table>
								<thead>
									<tr>
										<th></th>
										<th>ID</th>
										<th>Item</th>
										<th>Qty</th>
										<th>DPO</th>
										<th>Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOForm">
							<table id =''>
								<tbody id=''>

								</tbody>
							</table>
						</div>
						<div class="unscrollablePOFormBot">
							<table>
								<thead>
									<tr>
										<th></th>
										<th>ID</th>
										<th>Item</th>
										<th>Qty</th>
										<th>UnitPrice</th>
										<th>Total</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div id="POForm" class="RTop">
					<div class="tblContainerPOFormSmall">
						<div class="unscrollablePOFormSmall">
							<table>
								<thead>
									<tr>
										<th></th>
										<th>ID</th>
										<th>Item</th>
										<th>UnitPrice</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOFormSmall">
							<table id =''>
								<tbody id=''>

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="POForm" class="RBot">
					<div class="tblContainerPOFormSmall">
						<div class="unscrollablePOFormSmall">
							<table>
								<thead>
									<tr>
										<th></th>
										<th>ID</th>
										<th>Item</th>
										<th>Stocks</th>
										<th>Low Stocks</th>
										<th>Critical</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOFormSmall">
							<table id =''>
								<tbody id=''>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="ReceivingFormClone" class="whole">
			<div id="RecForm" class="Head">	
				<h2>PO List to Recieve: </h2>
				<div id="Recbtns">
					<a id="">Submit</a>
					<a id="">Reset</a>
				</div>
			</div>
			<div id="RecForm" class="Body">
				<div id="RecForm" class="Left">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th>No</th>
										<th>Receive</th>
										<th>Item</th>
										<th>Requested</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="RecForm" class="Right">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th style='width:10%'>No</th>
										<th style='width:15%'>No of Items</th>
										<th style='width:25%'>Supplier</th>
										<th style='width:20%'>Date</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table>
								<tbody>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="divPurchaseOrder" class="whole visible">
			<h1>Purchase Order</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table id='tblPurchaseOrder'>
						<thead>
							<tr>
								<th style="width: 7%">No</th>
								<th style="width: 11%">No of Items</th>
								<th style="width: 25%">Supplier</th>
								<th style="width: 18%">Date</th>
								<th style="width: 15%">Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id = "tblPO">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="PurchaseOrderForm" class="whole">
			<div id="POForm" class="Head">	
				<h2>Supplier: </h2>
				<select id="supplierSelect">

				</select>
				<div id="PObtns">
					<a id="POSubmit">Submit</a>
					<a id="POReset">Reset</a>
				</div>
			</div>
			<div id="POForm" class="Body">
				<div id="POForm" class="Left">
					<div class="tblContainerPOForm">
						<div class="unscrollablePOForm">
							<table>
								<thead>
									<tr>
										<th style="width: 7%"></th>
										<th style="width: 11%">Qty</th>
										<th style="width: 13%">ID</th>
										<th style="width: 25%">Item</th>
										<th style="width: 18%"></th>
										<th style="width: 13%">DPO</th>
										<th style="width: 17%">Total</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOForm">
							<table id ='POListForm'>
								<tbody id='tblPOList'>     

								</tbody>
							</table>
						</div>
						<div class="unscrollablePOFormBot">
							<table>
								<thead>
									<tr>
										<th style="width: 7%">Total:</th>
										<th style="width: 11%"></th>
										<th style="width: 13%"></th>
										<th style="width: 25%"></th>
										<th style="width: 15%"></th>
										<th style="width: 13%"></th>
										<th style="width: 17%"><a id = "totalPOAmount"></a></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>

				<div id="POForm" class="RTop">
					<div class="tblContainerPOFormSmall">
						<div class="unscrollablePOFormSmall">
							<table>
								<thead>
									<tr>
										<th style="width: 10%"></th>
										<th style="width: 15%">ID</th>
										<th style="width: 55%">Item</th>
										<th style="width: 20%">DPO Cost</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOFormSmall">
							<table id ='suppliersList'>
								<tbody id='tblSuppliersList'>

								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div id="POForm" class="RBot">
					<div class="tblContainerPOFormSmall">
						<div class="unscrollablePOFormSmall">
							<table>
								<thead>
									<tr>
										<th>ID</th>
										<th>Item</th>
										<th>Stocks</th>
										<th>Low</th>
										<th>Critical</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollablePOFormSmall">
							<table id ='LaCStockList'>
								<tbody id='tblLaCStockList'>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="divReceiving" class="whole">
			<h1>Receivings</h1>

			<div class="tblContainertwolines">
				<div class="unscrollabletwolines">
					<table id='inv'>
						<thead>
							<tr>
								<th style="width: 7%">No</th>
								<th style="width: 14%">Date</th>
								<th style="width: 20%">Supplier</th>
								<th style="width: 15%">Item</th>
								<th style="width: 15%">Received</th>
								<th style="width: 13%">Back Order</th>
								<th style="width: 17%">Expected<br>Receive</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollabletwolines">
					<table>
						<tbody id = "tblReceivings">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="ReceivingForm" class="whole">
			<div id="RecForm" class="Head">	
				<h2>PO List to Recieve: </h2><input id="ReceiveTemp" readonly hidden/>
				<div id="Recbtns">
					<a id="RecSubmit">Submit</a>
					<a id="RecReset">Reset</a>
				</div>
			</div>
			<div id="RecForm" class="Body">
				<div id="RecForm" class="Left">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th>No</th>
										<th>Receive</th>
										<th>Item</th>
										<th>Requested</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table>
								<tbody id='tblReceivingForm'>

								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="RecForm" class="Right">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th>No</th>
										<th>No of Items</th>
										<th>Supplier</th>
										<th>Date</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table id ='RecListForm'>
								<tbody id='tblRequestList'>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="divBackOrder" class="whole">
			<h1>Back Order</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table>
						<thead>
							<tr>
								<th  style="width: 32%">No</th>
								<th  style="width: 30%">Item</th>
								<th  style="width: 18%">Recieve</th>
								<th  style="width: 25%">Pending</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id ="tblBackOrder">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divSuppliers" class="whole">
			<h1>Suppliers</h1>
			<input id='supptemp' readonly hidden/>

			<div class="tblContainer">
				<div class="unscrollable">
					<table>
						<thead>
							<tr>
								<th style="width: 2%">No</th>
								<th style="width: 13%">Supplier</th>
								<th style="width: 15%">Address</th>
								<th style="width: 6%">Contact No.</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table id='suppliertable'>
						<tbody id='tblSupplier'>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="addSupForm" class="whole">
		<h1>Add Supplier</h1>
			<div class="addFields">
				<h4>Name: </h4><br>
				<h4>Address: </h4><br>
				<h4>Contact: </h4><br>
				<h4>Email Address: </h4><br>
				<h4>Username: </h4><br>
				<h4>Password: </h4><br>
				<h4>Confirm Password: </h4><br>
			</div>
			<div class="addInputs">
				<input type='text' id ="name" autocomplete="off"/><br>
				<input type='text' id ="address" autocomplete="off"/><br>
				<input type='text' id ="contact" autocomplete="off"/><br>
				<input type='text' id = "email"  autocomplete="off"/><br>
				<input type='text' id ="Username" autocomplete="off"/><br>
				<input type='password' id ="Password" autocomplete="off"/><br>
				<input type='password' id ="CPassword" autocomplete="off"/><br>
			</div>
			<div id="addButtons">
				<a id="addSupConfirm" >Confirm</a>
				<a id="addSupReset">Reset</a>
			</div>
	   </div>

		<div id="viewSupForm" class="whole">
			<div id="supFormHead">
				<div id = "supName">

				</div>
				<div id = "supDetails">	

				</div>
			</div>
			<span class="divider"></span>
			<div id="supFormBody">
				<div class="tblContaineritemdisplay">
					<div class="unscrollableitemdisplay">
						<table>
							<thead>
								<tr>
									<th>ID</th>
                                    <th>Name</th>
									<th>Category</th>
									<th>Default<br>PO Cost</th>
									<th>SRP</th>
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

		<div id="divInventory" class="whole">
			<h1>Inventory</h1>

			<div class="tblContainertwolines">
				<div class="unscrollabletwolines">
					<table id='inv'>
						<thead>
							<tr>
								<th style="width: 6%">ID</th>
								<th style="width: 18%">Name</th>
								<th style="width: 18%">Category</th>  
								<th style="width: 10%">Available<br>Quantity</th>
								<th style="width: 10%">Onhand<br>Stocks</th>
								<th style="width: 10%">Quantity<br>Committed</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollabletwolines">
					<table id='inv'>
						<tbody id='tblInventory'>    

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divItems" class="whole">
			<h1>Items</h1><input id='viewitemtemp' readonly hidden/>

			<div class="tblContainer">
				<div class="unscrollable">
					<table class='tblRequests'>
						<thead>
							<tr>
								<th style="width: 5%">ItemNo</th>
								<th style="width: 16%">Name</th>
								<th style="width: 8%">No of<br>Variant</th>
								<th style="width: 10%">Family</th>
								<th style="width: 8%">Category</th>
								<th style="width: 9%">Subcategory</th>
								<th style="width: 15%">Supplier</th>
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

		<div id="addForm" class="whole">
			<div id="RecForm" class="Head">	
				<h2>Add Item: </h2><input id="addTemp" readonly hidden/>
				<div id="addbtns">
					<a id="addSubmit">Submit</a>
					<a id="addReset">Reset</a>
				</div>
			</div>
			<div id="RecForm" class="Body">
				<div id="RecForm" class="Left">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th>Item Information</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table>
								<tbody id='tblAddForm'>

								</tbody>
							</table>
						</div>
					</div>
				</div>

				<div id="RecForm" class="Right">
					<div class="tblContainerRecForm">
						<div class="unscrollableRecForm">
							<table>
								<thead>
									<tr>
										<th>New Items</th>
									</tr>
								</thead>
							</table>
						</div>
						<div class="scrollableRecForm">
							<table id ='addListForm'>
								<tbody id='tblAddItmList'>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="divItemView" class="whole">
			<div>
				<h2 class='itemname'>Item:</h2><input id="itmName" readonly class='change1' />
				<span></span>
				<h5>Item No.:</h5><input id="itmNo" name='ItemID' readonly class='item0'/>
			</div>
			<div class='orderDisplayed viewFunc'>
				<a class='viewBtn' id='viewEdit'>Edit</a>
				<a class='viewBtn' id='viewRemove'>Remove</a>
			</div>		
            <br>  
            <h2>Variants</h2>
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
                                    <th>Unit Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="scrollableitemdisplay">
                        <table>
                            <tbody id='tblItemVariant'>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

		<div id="divLowStock" class="whole">
			<h1>Low Stocks and Critical</h1>

			<div class="tblContainertwolines">
				<div class="unscrollabletwolines">
					<table id='inv' >
						<thead>
							<th style="width: 10%">ID</th>
							<th style="width: 40%">Name</th>
                            <th style="width: 12%">Supplier</th>
                            <th style="width: 13%">Stocks</th>
							<th style="width: 12%">Low Stock<br>Threshold</th>
							<th style="width: 13%">Critical</th>
						</thead>
					</table>
				</div>
				<div class="scrollabletwolines">
					<table id='inv'>
						<tbody id = 'tblCritical'>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divFaC" class="whole">
			<h1>Categories</h1><input id='lvl1temp' readonly hidden/><input id='lvl2temp' readonly hidden/><input id='lvl3temp' readonly hidden/>

			<div class='tblContainer'>
				<div class="unscrollable">
					<table>
						<thead>
							<tr>
								<th style="width: 33.333333333333333%">Family</th>
								<th style="width: 33.333333333333333%">Category</th>
								<th style="width: 33.333333333333333%">Sub-category</th>
							</tr>
						</thead> 
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id = 'tblCategories'>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divRemoved" class="whole">
			<h1>Removed Items</h1><input id='viewtemprev' readonly hidden />

			<div class="tblContainertwolines">
				<div class="unscrollabletwolines">
					<table id='inv5'>
						<thead>
							<tr>
								<th>ItemNo</th>
								<th>Name</th>
								<th>Description</th>
								<th>Family</th>
								<th>Category</th>
								<th>Size</th>
								<th>Unit</th>
								<th>Unit<br>Price</th>
							</tr>
						</thead>	
					</table>
				</div>
				<div class="scrollabletwolines">
					<table id='inv5'>
						<tbody id = 'tblItemRemoved'>

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divAllOrders" class="whole">
			<h1>All Orders</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table id='newOrder'>
						<thead>
							<tr>
								<th>OR#</th>
								<th>Customer Name</th>
								<th>Location</th>
								<th>Date</th>
								<th>Total Amount</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id="tblAllOrders">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divNewOrders" class="whole">
			<h1>New Orders</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table id='newOrder'>
						<thead>
							<tr>
								<th>OR#</th>
								<th>Customer Name</th>
								<th>Location</th>
								<th>Date</th>
								<th>Total Amount</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id="tblNewOrders">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divProOrders" class="whole">
			<h1>Processing Orders</h1>
			<div class="tblContainer">
				<div class="unscrollable">
					<table id='newOrder'>
						<thead>
							<tr>
								<th>OR#</th>
								<th>Customer Name</th>
								<th>Location</th>
								<th>Date</th>
								<th>Total Amount</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id="tblProcessOrders">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divShipOrders" class="whole">
			<h1>Shipped Orders</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table id='newOrder'>
						<thead>
							<tr>
								<th>OR#</th>
								<th>Customer Name</th>
								<th>Location</th>
								<th>Date</th>
								<th>Total Amount</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id="tblShipOrders">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divCancelOrders" class="whole">
			<h1>Cancelled Orders</h1>

			<div class="tblContainer">
				<div class="unscrollable">
					<table id='newOrder'>
						<thead>
							<tr>
								<th>OR#</th>
								<th>Customer Name</th>
								<th>Location</th>
								<th>Date</th>
								<th>Total Amount</th>
								<th>Action</th>
							</tr>
						</thead>
					</table>
				</div>
				<div class="scrollable">
					<table>
						<tbody id="tblCancelOrders">

						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div id="divReports1" class="whole">
			<h1>Sales Reports</h1>
		</div>

		<div id="divReports2" class="whole">
			<h1>Fast Moving Items</h1>
		</div>

		<div id="divReports3" class="whole">
			<h1>Customer Reports</h1>
		</div>
	</div>

	<div class="modalShadow"></div>

	<div class="addmodal" id="addModal"> 
		<h1 class='head'>Add Item</h1>
		<div class='modalhalf'>
			<div class='one'>
				<div id='addImage'>
					<h1 class='addImg'>+</h1>
					<input type="file" name="file" id="file" class="inputfile" />
					<label for="file">Choose a file</label>
				</div>
				<input type='text' id="name" /><h4>Name</h4>
				<input type='text' id ="size"/><h4>Size</h4>
				<input type='text' id ="unit"/><h4>Unit</h4>
			</div>
			<div class='two'>
				<input type='text' id="description" /><h4>Description</h4>
				<br>
				<select id='addSlctFam'>

				</select>
				<select id='addSlctCat'>

				</select>
			</div>
		</div>
		<div class='modalhalf'>
			<div class='three'>
				<input type='text' id ="dpo"/><h4>Default PO cost</h4>
				<input type='text' id ="unit_cost"/><h4>Average Unit Cost</h4>
				<input type='text' id ="low_stock"/><h4>Low Stock Threshold</h4>
				<input type='text' id = "unit_price"/><h4>Unit Price</h4>
			</div>
			<div class='four'>
				<input type='text' id ="retail"/><h4>Retail</h4>
				<input type='text' id ="wholesale"/><h4>Wholesale</h4>
				<input type='text' id ="distribution"/><h4>Distribution</h4>
			</div>
		</div>
		<div class='addBtns'>
			<a id="additmConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>

	<div class="addmodal" id="editModal">
		<h1 class='head'>Edit Item</h1>
		<div class='modalhalf'>
			<div class='one'>
				<div id='addImage'>
					<h1 class='addImg'>+</h1>
					<input type='file' name='file' id='file' class='inputfile' />
					<label for='file'>Choose a file</label>
				</div>
				<input type='text' id='edtName' /><h4>Name</h4>
				<input type='text' id='edtSize' /><h4>Size</h4>
				<input type='text' id='edtUnit' /><h4>Unit</h4>
			</div>
			<div class='two'>
				<input type='text' id='edtDescription' /><h4>Description</h4>
				<br>
				<select id='slctEdtFam'>

				</select>
				<select id='slctEdtCat'>

				</select>
			</div>
		</div>
		<div class='modalhalf' style='border-right: none;'>
			<div class='three'>
				<input type='text' id ='edtDpo' /><h4>Default PO cost</h4>
				<input type='text' id ='edtUnit_cost' /><h4>Average Unit Cost</h4>
				<input type='text' id ='edtlow_stock' /><h4>Low Stock Threshold</h4>
				<input type='text' id ='edtUnit_price' /><h4>Unit Price</h4>
			</div>
			<div class='four'>
				<input type='text' id ='edtRetail' /><h4>Retail</h4>
				<input type='text' id ='edtWholesale' /><h4>Wholesale</h4>
				<input type='text' id ='edtDistribution' /><h4>Distribution</h4>
			</div>
		</div>
		<div class='addBtns'>
			<a id='edtItmConfirm' class='btnConfirm'>Confirm</a>
			<a class='additmCancel'>Cancel</a>
		</div>
		<div class='btnClose'></div>
	</div>

	<div class="addmodal" id="removeModal">
		<h1 class='head'>Remove Item</h1>
		<div>
			<h2 id = 'removeText'>Do you wish to remove item: <input readonly/></h2>
			<h4 id = 'removeNote'>Note:<br>
			This will not remove the item from inventory instead will be remove from the display in the ordering site.</h4>
		</div>
		<div class='addBtns'>
			<a id='rmvItmConfirm' class='btnConfirm'>Confirm</a>
			<a class='additmCancel'>Cancel</a>
		</div>
		<div class='btnClose'></div>
	</div>

	<div class="addmodal" id="restoreModal">
		<h1 class='head'>Remove Item</h1>
		<div>
			<h2 id = 'restoreText'>Do you wish to restore item: <input /></h2>
		</div>
		<div class='addBtns'>
			<a id='rstItmConfirm' class='btnConfirm'>Confirm</a>
			<a class='additmCancel'>Cancel</a>
		</div>
		<div class='btnClose'></div>
	</div>

	<div class="addmodal" id="physCount">
		<div class='first'>
			<select id='physSlct'>
				<option selected hidden value=''>Item No</option>
			<?php
				$query = "SELECT *, item.ItemNo AS ITNO FROM item, itemvariant WHERE item.ItemNo = itemvariant.ItemNo AND itemvariant.Owned = 1";
				$result = mysqli_query($db, $query);

				while($record = mysqli_fetch_array($result)){
					echo "<option value='" . $record['VariantNo'] . "'>" . $record['ITNO'] . "-" . $record['VariantNo'] . "</option>";
				}
			?>
			</select>
			<h4>ID</h4>
		</div>
		<div class='second'>
			<input type='text' id='physName' /><h4>Name</h4>
		</div>
		<div class='third'>
		<input type='text' id='physStock' /><h4>Stock Count</h4>
		</div>
		<div class='addBtns'>
			<a id="physCountConfirm" class="btnConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>

	<div class="addmodal" id="modalProcess">
		<div class='selection'>
			<h2>New</h2>
			<div class='orderTrans' id="NewList">

							</div>
		</div>
		<div class='selection'>
			<h2>To Process</h2>
			<div class='orderTrans' id="NewToPro">

							</div>
		</div>
		<div class='addBtns'>
			<a id="processConfirm" class="btnConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>

	<div class="addmodal" id="modalCancel">
		<div class='selection'>
			<h2>New</h2>
			<div class='orderTrans' id="NewList2">

							</div>
		</div>
		<div class='selection'>
			<h2>To Cancel</h2>
			<div class='orderTrans' id="NewToCancel">

							</div>
		</div>
		<div class='addBtns'>
			<a id="cancelConfirm" class="btnConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>

	<div class="addmodal" id="modalShip">
		<div class='selection'>
			<h2>Orders</h2>
			<div class='orderTrans' id="ProList">

							</div>
		</div>
		<div class='selection'>
			<h2>To Ship</h2>
			<div class='orderTrans' id="ProToShip">

							</div>
		</div>
		<div class='addBtns'>
			<a id="shipConfirm" class="btnConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>
    
    <div class="addmodal" id="famEdit">
        <h2>Edit Category</h2>
        <div class='FaC'>
            <input id="editName"/>
        </div>
        <div class='addBtns'>
            <a id="addFamConfirm" class="btnConfirm">Confirm</a>
            <a class="additmCancel">Cancel</a>
        </div>
        <div class="btnClose"></div>
    </div>
    
	<div class="addmodal" id="famDel">
		<div class='FaC'>
			<h2 id='removeText' class='fam'>Do you want to remove : <br><input id='catdel' readonly/></h2>
			<br>
			<h4 id='removeNote'>Note:<br>
			Removing this will remove all the categories under it.<br>
			This Action cannot be undone.</h4>
		</div>
		<div class='addBtns'>
			<a id="delFamConfirm" class="btnConfirm">Confirm</a>
			<a class="additmCancel">Cancel</a>
		</div>
		<div class="btnClose"></div>
	</div>


		<script type="text/javascript">
			$(function(){

				showdata();
				showdata1();
				showorder();

				$('#btnCrtReq').click(function(){
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
							createrequest : 1
						},
						success: function(boom){
							$('#supplierSelect').html(boom);
							$('#PurchaseOrderForm').toggleClass('visible');
						}
					});
				});

				$('#btnView').click(function(){
					var viewItemNo = $('#viewitemtemp').val();

					if(viewItemNo != ''){                   
                        $.ajax({
							url: "main.php",
							type: "POST",
							async: false,
							data: {
								itemvariant : 1,
								viewItemNo : viewItemNo
							},
							success: function(boom){
                                $('#tblItemVariant').html(boom);
                                $('#divItemView').toggleClass('visible');
							}
						});
					}
					else{
						alert('Please choose an item to view.');
					}
				});

				$('#btnEdit').click(function(){
					var catlvl1 = $('#lvl1temp').val();
					var catlvl2 = $('#lvl2temp').val();
					var catlvl3 = $('#lvl3temp').val();
                    
                    if(catlvl1 != '' || catlvl2 != '' || catlvl3 != ''){
                        $.ajax({
                            url: "main.php",
                            type: "POST",
                            async: false,
                            data: {
                                editcategory: 1,
                                catlvl1 : catlvl1,
                                catlvl2 : catlvl2,
                                catlvl3 : catlvl3
                            },
                            success: function(boom){
                                $('#editName').val(boom);
                                $('#famEdit').addClass('show');
                                $('.modalShadow').addClass('show');
                                
                            }
                        });
                    }
                    else{
                        alert('Please choose a category to change');
                    }
				});
                
                $('#btnDelete').click(function(){
                    var catlvl1 = $('#lvl1temp').val();
					var catlvl2 = $('#lvl2temp').val();
					var catlvl3 = $('#lvl3temp').val();
                    
                    if(catlvl1 != '' || catlvl2 != '' || catlvl3 != ''){
                        $.ajax({
                            url: "main.php",
                            type: "POST",
                            async: false,
                            data: {
                                deletecategory: 1,
                                catlvl1 : catlvl1,
                                catlvl2 : catlvl2,
                                catlvl3 : catlvl3
                            },
                            success: function(boom){
                                $('#catdel').val(boom);
                                $('#famDel').addClass('show');
                                $('.modalShadow').addClass('show');
                            }
                        });
                    }
                    else{
                        alert('Please choose an item to delete.');
                    }	
                });

				$('#btnAdd').click(function(){
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
							familyoptions : 1,
						},
						success: function(boom){
							$('#addSlctFam').html(boom);
						}
					});
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
							categoryoptions : 1,
						},
						success: function(boom){
							$('#addSlctCat').html(boom);
						}
					});
				});

				$('#btnViewRev').click(function(){
					var viewItemNo = $('#viewtemprev').val();

					if(viewItemNo != ''){
						$.ajax({
							url: "main.php",
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
							url: "main.php",
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
							url: "main.php",
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
                    var catlvl1 = $('#lvl1temp').val();
					var catlvl2 = $('#lvl2temp').val();
					var catlvl3 = $('#lvl3temp').val();
					var name = $('#editName').val();

                    
                    if(catlvl1 != '' || catlvl2 != '' || catlvl3 != ''){
                        $.ajax({
                            url: "main.php",
                            type: "POST",
                            async: false,
                            data: {
                                buttonaddfam: 1,
                                catlvl1 : catlvl1,
                                catlvl2 : catlvl2,
                                catlvl3 : catlvl3,
                                name : name
                            },
                            success: function(boom){
                                alert("Successfully Edited");
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
							url : "main.php",
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

				$('#addSupConfirm').click(function(){
					var name = $('#name').val();
					var address = $('#address').val();
					var contact = $('#contact').val();
					var email = $('#email').val();
					var username = $('#Username').val();
					var password = $('#Password').val();
					var cpassword  = $('#CPassword').val();

					if( name != '' && address !='' && contact !='' && email !='' && username !='' && password !='' && cpassword !='' ){
						if( password == cpassword ){
							$.ajax({
							url : "main.php",
							type : "POST",
							async	: false,
							data : {
								buttonsave : 1,
								aname : name,
								aaddress : address,
								acontact : contact,
								aemail : email,
								ausername : username,
								apassword : password
							},
							success : function(result){
								alert("Succesfully Added");
								showdata1();
								$("input").val("");
								$("input").css("border", "1px solid #ccc");
								$('.visible').removeClass("visible");
								$('#divSuppliers').addClass('visible');
								$('#POMenu').addClass('visible');
								sdeSelect = 46*3+12.5;
								$('.selector').css('top',sdeSelect+'px');
								$('.topbarcmd1').css('top','-24vh');
																	$('li').removeClass('sdeselected');
							}
						});
						}
						else{
							$('#name').css("border","1px solid #ccc");
							$('#address').css("border","1px solid #ccc");
							$('#contact').css("border","1px solid #ccc");
							$('#email').css("border","1px solid #ccc");
							$('#Username').css("border","1px solid #ccc");
							$('#Password').css("border","1px solid red");
							$('#CPassword').css("border","1px solid red");
							alert("Password don't match");
						}
					}
					else{
						if( name == '' ){
							$('#name').css("border","1px solid red");
						}
						else{
							$('#name').css("border","1px solid #ccc");
						}
						if( address == '' ){
							$('#address').css("border","1px solid red");
						}
						else{
							$('#address').css("border","1px solid #ccc");
						}
						if( contact == '' ){
							$('#contact').css("border","1px solid red");
						}
						else{
							$('#contact').css("border","1px solid #ccc");
						}
						if( email == '' ){
							$('#email').css("border","1px solid red");
						}
						else{
							$('#email').css("border","1px solid #ccc");
						}
						if( username == '' ){
							$('#Username').css("border","1px solid red");
						}
						else{
							$('#Username').css("border","1px solid #ccc");
						}
						if( password == '' ){
							$('#Password').css("border","1px solid red");
						}
						else{
							$('#Password').css("border","1px solid #ccc");
						}
						if( cpassword == '' ){
							$('#CPassword').css("border","1px solid red");
						}
						else{
							$('#CPassword').css("border","1px solid #ccc");
						}

						alert("Please Fill All.");
					}
				});

				$('#btnViewSup').click(function(){
					var viewSuppNo = $('#supptemp').val();

					if(viewSuppNo != ''){
						$.ajax({
							url: "main.php",
							type: "POST",
							async: false,
							data: {
								buttonviewsupp : 1,
								vSuppNo : viewSuppNo
							},
							success: function(boom){
									$('#supName').html(boom);
									$('#viewSupForm').toggleClass('visible');
							}
						});

						$.ajax({
							url: "main.php",
							type: "POST",
							async: false,
							data: {
								buttonviewsuppdetails : 1,
								vSuppNo : viewSuppNo
							},
							success: function(boom){
									$('#supDetails').html(boom);
							}
						});

						$.ajax({
							url: "main.php",
							type: "POST",
							async: false,
							data: {
								buttonviewsuppitems : 1,
								vSuppNo : viewSuppNo
							},
							success: function(boom){
								$('#tblSupplierItems').html(boom);
							}
						});
					}
					else{
						alert('Please choose an item to view.');
					}
				});

				$('#additmConfirm').click(function(){
					var name = $('#name').val();
					var size = $('#size').val();
					var unit = $('#unit').val();
					var description = $('#description').val();
					var fam = $('#addSlctFam').val();
					var cat = $('#addSlctCat').val();
					var dpo  = $('#dpo').val();
					var unit_cost = $('#unit_cost').val();
					var low_stock = $('#low_stock').val();
					var unit_price = $('#unit_price').val();
					var retail = $('#retail').val();
					var wholesale = $('#wholesale').val();
					var distribution = $('distribution').val();

					if( name != '' && fam !='' && cat !='' ){
						$.ajax({
							url : "main.php",
							type : "POST",
							async	: false,
							data : {
								buttons : 1,
								aname : name,
								asize : size,
								aunit : unit,
								adescription : description,
								afam : fam,
								acat : cat,
								adpo : dpo,
								aunit_cost : unit_cost,
								alow_stock : low_stock,
								aunit_price : unit_price,
								aretail : retail,
								awholesale : wholesale,
								adistribution : distribution,
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

				$('#viewEdit').click(function(){
					var item_id = $('#viewtemp').val();

					if(item_id != ''){
						$.ajax({
							url : "main.php",
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
							url : "main.php",
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

				$('#edtFamConfirm').click(function(){
					var fam_id = $('#sctdFam').val();
					var name = $('#edtFamName').val();
					var desc = $('#edtFamDesc').val();

					if(fam_id != ''){
						$.ajax({
							url : "main.php",
							type : "POST",
							datatype : "JSON",
							data : {
								efaminfo : 1,
								eFamID : fam_id,
								eName : name,
								eDesc : desc,
							},
							success : function(result){
								alert("Information Updated");
								$('.addmodal').removeClass('show');
								showdata();

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 )
							}
						});
					}
				});

				$('#edtCatConfirm').click(function(){
					var cat_id = $('#sctdCat').val();
					var name = $('#edtCatName').val();
					var desc = $('#edtCatDesc').val();
					var fam = $('#adtCatSlct').val();

					if(cat_id != ''){
						$.ajax({
							url : "main.php",
							type : "POST",
							datatype : "JSON",
							data : {
								ecatinfo : 1,
								eCatID : cat_id,
								eName : name,
								eDesc : desc,
								efam : fam,
							},
							success : function(result){
								alert("Information Updated");
								$('.addmodal').removeClass('show');
								showdata();

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 )
							}
						});
					}
				});

				$('#delFamConfirm').click(function(){
                    var catlvl1 = $('#lvl1temp').val();
					var catlvl2 = $('#lvl2temp').val();
					var catlvl3 = $('#lvl3temp').val();
               
                    if(catlvl1 != '' || catlvl2 != '' || catlvl3 != ''){
                        $.ajax({
                            url: "main.php",
                            type: "POST",
                            async: false,
                            data: {
                                dfaminfo: 1,
                                catlvl1 : catlvl1,
                                catlvl2 : catlvl2,
                                catlvl3 : catlvl3
                            },
                            success: function(boom){
                               alert("Deleted Successfully");
								$('.addmodal').removeClass('show');
								showdata();

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 ); 
                            }
                        });
                    }	
                    
					/*var fam_id = $('#sctdFam').val();

					if(fam_id != ''){
						$.ajax({
							url : "main.php",
							type : "POST",
							datatype : "JSON",
							data : {
								dfaminfo : 1,
								dFamID : fam_id,
							},
							success : function(result){
								alert("Family Deleted");
								$('.addmodal').removeClass('show');
								showdata();

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 )
							}
						});
					}*/
				});

				$('#delCatConfirm').click(function(){
					var cat_id = $('#sctdCat').val();

					if(cat_id != ''){
						$.ajax({
							url : "main.php",
							type : "POST",
							datatype : "JSON",
							data : {
								dcatinfo : 1,
								dCatID : cat_id,
							},
							success : function(result){
								alert("Category Deleted");
								$('.addmodal').removeClass('show');
								showdata();

								setTimeout(function(){
									$('.modalShadow').removeClass('show');
									$('.addmodal input').val("");
								}, 300 )
							}
						});
					}
				});

				$('#edtItmConfirm').click(function(){
					var item_id = $('#viewtemp').val();
					var name = $('#edtName').val();
					var size = $('#edtSize').val();
					var unit = $('#edtUnit').val();
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
							url : "main.php",
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
							url : "main.php",
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
							url : "main.php",
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

				$('#POSubmit').click(function(){
					var suppID = $('#supplierSelect').val();
                    
                    if($('#tblPOList input:text').val() != ''){
                        if(suppID){
                            $.ajax({
                                url : "main.php",
                                type : "POST",
                                datatype : "JSON",
                                data : {
                                    submitpo : 1,
                                    suppID : suppID
                                },
                                success : function(result){
                                    alert(result); // <---wag burahin
                                    $('#PurchaseOrderForm').toggleClass('visible');
                                    showdata1();
                                }
                            });
                        }
                        else{
                            alert("Transaction Failed");
                        }
                    }
                    else{
                        alert("Please input your requested quantity.");
                    }
				});

				$('#btnDirRec').click(function(){
					$.ajax({
						url : "main.php",
						type : "POST",
						async : false,
						data : {
							showreceive : 1,
						},
						success : function(result){
							$('#ReceivingForm').toggleClass('visible');
							$('#tblRequestList').html(result);
							showdata1();
						}
					});
					$.ajax({
						url : "main.php",
						type : "POST",
						async : false,
						data : {
							showrecevingform : 1,
							POID : POID
						},
						success : function(result){
						$('#tblReceivingForm').html(result);
						}
					});
				});

				$('#RecSubmit').click(function(){
					var POID = $('#ReceiveTemp').val();

					if(POID != ''){
						$.ajax({
							url : "main.php",
							type : "POST",
							datatype : "JSON",
							data : {
								submitreceive : 1,
								POID : POID
							},
							success : function(result){
                                alert("Updated.");
								$('#ReceivingForm').toggleClass('visible');
								showdata1();
								showdata();
							}
						});
					}
					else{
						alert("Transaction Failed");
					}
				});

				$('#addSubmit').click(function(){
					var itemID = $('#addTemp').val();
					var price = $('#itmPrice').val();
					var LStocks = $('#itmLSLvl').val();
					var Crit = $('#itmCritLvl').val();

					if(itemID != ''){
						if(price != '' && LStocks != '' && Crit != '' ){
							$.ajax({
								url : "main.php",
								type : "POST",
								datatype : "JSON",
								data : {
									submitadd : 1,
									itemID : itemID,
									price : price,
                                    LStocks : LStocks,
                                    Crit : Crit
								},
								success : function(result){
									showdata();
									alert("Successfully Added.");
									$('#addForm').removeClass('visible');
									$('#divItems').addClass('visible');
									showdata1();
									$('#itmPrice').css("border","1px solid #ccc");
								}
							});
						}else{
                            if(price == ''){
                                $('#itmPrice').css("border","1px solid red");
                            }
                            else{
                                $('#itmPrice').css("border","1px solid #ccc");
                            }
							if(LStocks == ''){
                                $('#itmLSLvl').css("border","1px solid red");
                            }
                            else{
                                $('#itmLSLvl').css("border","1px solid #ccc");
                            }
                            if(Crit == ''){
                                $('#itmCritLvl').css("border","1px solid red");
                            }
                            else{
                                $('#itmCritLvl').css("border","1px solid #ccc");
                            }
							alert("Please fill fields.");
						}
					}else{
						alert("Please Choose an Item to Add.");
					}
				});

				$('#btnPro').click(function(){
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								neworderstopro : 1,
						},
						success : function(result){
								$('#NewList').html(result);
						}
					});  
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							prolistshow : 1,
						},
						success : function(result){
							$('#NewToPro').html(result);
						}
					}); 
				});

				$('#btnCan').click(function(){
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							neworderstocan : 1,
						},
						success : function(result){
							$('#NewList2').html(result);
						}
					});  
					 $.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							canlistshow : 1,
						},
						success : function(result){
							$('#NewToCancel').html(result);
						}
					}); 
				});

				$('#btnShp').click(function(){
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							proorderstoship : 1,
						},
						success : function(result){
							$('#ProList').html(result);
						}
					}); 
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							shplistshow : 1,
						},
						success : function(result){
							$('#ProToShip').html(result);
						}
					});
				});

				$('#processConfirm').click(function(){
					$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
							proccessingorders : 1,
						},
						success : function(result){
							showorder();
							showdata();
							$('.addmodal').removeClass('show');
							$('.modalShadow').removeClass('show');
						}
					});
				});

				 $('#cancelConfirm').click(function(){
					 $.ajax({
						 url : "main.php",
						 type : "POST",
						 datatype : "JSON",
						 data : {
							 cancellingorders : 1,
						 },
						 success : function(result){
							 showorder();
                             showdata();
							 $('.addmodal').removeClass('show');
							 $('.modalShadow').removeClass('show');
						 }
					 });
				 });

			 $('#shipConfirm').click(function(){
				 $.ajax({
					 url : "main.php",
					 type : "POST",
					 datatype : "JSON",
					 data : {
						 shippingorders : 1,
					 },
					 success : function(result){
						 showorder();
						 showdata();
						 $('.addmodal').removeClass('show');
						 $('.modalShadow').removeClass('show');
					 }
				 }); 
			 });
		});				

			function showdata(){
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showinventory : 1,
						},
						success : function(result){
							$('#tblInventory').html(result);
						}
				});

				$.ajax({
					url : "main.php",
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
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showremove : 1,
					},
					success : function(result){
						$('#tblItemRemoved').html(result);
					}
				});

				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showcritical : 1,
						},
						success : function(result){
							$('#tblCritical').html(result);
						}
				});

				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showfamily : 1,
						},
						success : function(result){
							$('#tblFamily').html(result);
						}
				});

				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showcategory : 1,
						},
						success : function(result){
							$('#tblCategories').html(result);
						}
				});

				var viewItemNo = $('#viewtemp').val();
				if(viewItemNo != ''){
					$.ajax({
						url: "main.php",
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

			function showdata1(){
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showpo : 1,
						},
						success : function(result){
							$('#tblPO').html(result);
						}
				});

				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showsupplier : 1,
						},
						success : function(result){
							$('#tblSupplier').html(result);
						}
				});
                
                var supplierID = $('#supplierSelect').val();
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						showpurchase : 1,
                        supplierID : supplierID
						},
						success : function(result){
							$('#tblPOList').html(result);
						}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					datatype	: "JSON",
					data: {
						totalamount : 1,
					},
					success: function(boom){
						$('#totalPOAmount').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						showreceivings : 1,
					},
					success: function(boom){
						$('#tblReceivings').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						showbackorders : 1,
					},
					success: function(boom){
						$('#tblBackOrder').html(boom);
					}
				});    
                
                var itemZero = 0-1;
				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						showaddform : 1,
						ItemID : itemZero
					},
					success: function(boom){
						$('#tblAddForm').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						showaddlist : 1,
					},
					success: function(boom){
						$('#tblAddItmList').html(boom);
					}
				});

			}

			function showorder(){
				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						allorders : 1,
					},
					success: function(boom){
						$('#tblAllOrders').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						neworders : 1,
					},
					success: function(boom){
						$('#tblNewOrders').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						processorders : 1,
					},
					success: function(boom){
						$('#tblProcessOrders').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						shiporders : 1,
					},
					success: function(boom){
						$('#tblShipOrders').html(boom);
					}
				});

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						cancelorders : 1,
					},
					success: function(boom){
						$('#tblCancelOrders').html(boom);
					}
				});

				$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								neworderstopro : 1,
						},
						success : function(result){
								$('#NewList').html(result);
						}
				});  
				$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								prolistshow : 1,
						},
						success : function(result){
								$('#NewToPro').html(result);
						}
				}); 

				$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								neworderstocan : 1,
						},
						success : function(result){
								$('#NewList2').html(result);
						}
				});  
				 $.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								canlistshow : 1,
						},
						success : function(result){
								$('#NewToCancel').html(result);
						}
				}); 

				$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								proorderstoship : 1,
						},
						success : function(result){
								$('#ProList').html(result);
						}
				}); 
				$.ajax({
						url : "main.php",
						type : "POST",
						datatype : "JSON",
						data : {
								shplistshow : 1,
						},
						success : function(result){
								$('#ProToShip').html(result);
						}
				});
		}

			function addToPO(itemID,variantID){
				var itmID = itemID;
                var varID = variantID;

				$.ajax({
					url: "main.php",
					type: "POST",
					async: false,
					data: {
						addtopurchase : 1,
						itemID : itmID,
                        varID : varID
					},
					success: function(boom){
						showdata();
					}
				});

			}

			function removeFromPO(itemID,variantID){
				var itmID = itemID;
                var varID = variantID;

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						removefrompo : 1,
						itemID: itmID,
                        varID : varID
					},
					success: function(boom){
						showdata();
					}
				});
			}

			function quantityChange(cnt, itemID, variantID){
				var itmID = itemID;
                var varID = variantID;
				var qty = cnt.value;

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						quantityupdate : 1,
						itemID : itmID,
                        varID : varID,
						quantity : qty
					},
					success: function(boom){
						showdata();
					}
				});
			}

			function receiveChange(cnt, RListID){
				var RLID = RListID;
				var rec = cnt.value;

				$.ajax({
					url: "main.php",
					type : "POST",
					datatype	: "JSON",
					data: {
						receiveupdate : 1,
						RLID : RLID,
						receive : rec
					},
					success: function(boom){
						showdata1();
					}
				});	
			}

			function addToMove(x, itemID){
				var holder = x;
				var ORNO = itemID;

				if(holder == 'Process'){
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
								addtoprocess : 1,
								ORNO: ORNO
						},
						success: function(boom){
								showdata1();
						}
					});
				}
				if(holder == 'Cancel'){
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
								addtocancel : 1,
								ORNO: ORNO
						},
						success: function(boom){
								showdata1();
						}
					});
				}
				if(holder == 'Ship'){
					$.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
								addtoship : 1,
								ORNO: ORNO
						},
						success: function(boom){
								showdata1();
						}
					});
				}
			showorder();
		}

		function removeFromMove(x, itemID){
			var holder = x;
			var ORNO = itemID;

			if(holder == 'Process'){
				$.ajax({
					url: "main.php",
					type: "POST",
					async: false,
					data: {
							removetoprocess : 1,
							ORNO: ORNO
					},
					success: function(boom){
							showdata1();
					}
				});
			}
			if(holder == 'Cancel'){
				$.ajax({
					url: "main.php",
					type: "POST",
					async: false,
					data: {
							removetocancel : 1,
							ORNO: ORNO
					},
					success: function(boom){
							showdata1();
					}
				});
			}
			if(holder == 'Ship'){
				$.ajax({
					url: "main.php",
					type: "POST",
					async: false,
					data: {
						removetoship : 1,
						ORNO: ORNO
					},
					success: function(boom){
						showdata1();
					}
				});
			}
			showorder();
		}

		function catAddText(){
			$('#catAddShift').append('<input placeholder="Add Category" onchange=saveCat(this,1) /><a class=removefrom onclick=showdata()>x</a>');
			$('#catAdd').css('display','none');
		}

		function catAddText2(cat1){
			var c1 = cat1;
			$('#catAddShift'+c1).append('<input placeholder="Add Category" onchange=saveCat(this,2,'+c1+') /><a class=removefrom onclick=showdata()>x</a>');
			$('#catAdd'+c1).css('display','none');
		}

		function catAddText3(cat2, cat1){
			var c1 = cat1;
			var c2 = cat2;
			$('#catAddShift'+c1+'-'+c2).append('<input placeholder="Add Category" onchange=saveCat(this,3,'+c1+','+c2+') /><a class=removefrom onclick=showdata()>x</a>');
			$('#catAdd'+c1+'-'+c2).css('display','none');
		}

		function saveCat(catText, level, cat1, cat2){
			$('.catAdd').css('display','block');
			var newCat = catText.value;
			var x = level;
			var y = cat1;
			var z = cat2;

			if(x == 1){
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						savecategory : 1,
						newCat : newCat
						},
						success : function(result){
							alert('Successfully Added.');
						}
				});
			}
			if(x == 2){
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						savecategory2 : 1,
						newCat : newCat,
						cat1No : y
						},
						success : function(result){
							alert('Successfully Added.');
						}
				});
			}
			if(x == 3){
				$.ajax({
					url : "main.php",
					type : "POST",
					async : false,
					data : {
						savecategory3 : 1,
						newCat : newCat,
						cat1No : y,
						cat2No : z
						},
						success : function(result){
							alert('Successfully Added.');
						}
				});
			}

			$.ajax({
				url : "main.php",
				type : "POST",
				async : false,
				data : {
					showcategory : 1,
					},
					success : function(result){
						$('#tblCategories').html(result);
					}
			});   
		}
            
        function valuetemp(id,lvl){
			var catid = id;
			var catlvl = lvl;
                
			if(catlvl == 1){
                $('#lvl1temp').val(catid);
                $('#lvl2temp').val('');
                $('#lvl3temp').val('');
            }
            else if(catlvl == 2){
                $('#lvl2temp').val(catid);
                $('#lvl1temp').val('');
                $('#lvl3temp').val('');
            }
            else if(catlvl == 3){
                $('#lvl3temp').val(catid);
                $('#lvl1temp').val('');
                $('#lvl2temp').val('');
            }
		}

		</script>

		<script>
			$(document).ready(function(){
				$('#supplierSelect').change(function(){
					var supplierID = $(this).val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						data: {
							supplierselect: 1,
							supplierID : supplierID
						},
						datatype: 'text',
						success:function(data){
							$('#tblSuppliersList').html(data);
						}
					});
                    
                    $.ajax({
						url: "main.php",
						type: "POST",
						async: false,
						data: {
							lowstockscheck : 1,
                            supplierID : supplierID
						},
						success: function(boom){
							$('#tblLaCStockList').html(boom);
						}
					});
				});

				$('#RecListForm tbody').on( 'click', 'tr', function () {
					var POID = $('#ReceiveTemp').val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						async: false,
						data: {
							showrecevingform : 1,
							POID : POID
						},
						success:function(data){
							$('#tblReceivingForm').html(data);
						}
					});
				});

				$('#addListForm tbody').on( 'click', 'tr', function () {
					var itemID = $('#addTemp').val();

					$.ajax({
						url: 'main.php',
						method: 'POST',
						async: false,
						data: {
							showaddform : 1,
							ItemID : itemID
						},
						success:function(data){
							$('#tblAddForm').html(data);
						}
					});
				});

				$('#addSlctFam').change(function(){
					var famID = $(this).val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						data: {
							depSlct: 1,
							fam_ID : famID
						},
						datatype: 'text',
						success:function(data){
							$('#addSlctCat').html(data);
						}
					});
				});

				$('#slctEdtFam').change(function(){
					var famID = $(this).val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						data: {
							depSlct: 1,
							fam_ID : famID
						},
						datatype: 'text',
						success:function(data){
							$('#slctEdtCat').html(data);
						}
					});
				});

				$('#physSlct').change(function(){
					var itmID = $(this).val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						data: {
							depInpt: 1,
							itm_ID : itmID
						},
						datatype: 'text',
						success:function(data){
							$('#physName').html(data);
						}
					});
				});

				$('#physName').change(function(){
					var itmname = $(this).val();
					$.ajax({
						url: 'main.php',
						method: 'POST',
						data: {
							physSlct: 1,
							itm_name : itmname
						},
						datatype: 'text',
						success:function(data){
							$('#physSlct').html(data);
						}
					});
				});
			});
		</script>
	</div>
</body>
</html>