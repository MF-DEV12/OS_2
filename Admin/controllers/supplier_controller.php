<?php
	session_start();
	if(!isset($_SESSION)){
			header('location:index.php');
	}
	if($_SESSION['LoginType']=='Admin'){
			header('location:main.php');
	}
		include('../../config/db.php');
		 

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

