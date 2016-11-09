<?php
		include('../../config/db.php');
		
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