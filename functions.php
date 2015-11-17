<?php
	
	function readAllData($filename){
		$file = fopen($filename, 'r'); //Nur zum Lesen geöffnet; platziere Dateizeiger auf Dateianfang.
		$ii = 0;
		while(! feof($file)) {
		    $theData = fgets($file);
		    //$GLOBALS["debugText"][] = $theData;
		    $allData[$ii] = $theData; 
		    $ii = $ii+1;
		}
		fclose($filename);
  		$GLOBALS["debugText"][]= "read all from " . $filename;

		return $allData;
	}

	function checkAllVals($filename, $valNum, $seperateColumn)
	{
		if(file_exists($filename)) {
	       	$GLOBALS["debugText"][]= "START checkAllVals of file " . $filename;

			$allData = readAllData($filename);	//Alle Zeilen einlesen
			$foundNum[] = array();				//Wie oft eine ValNum in der Datei vorhanden ist.

			//write data
       		$fw = fopen($filename,"w");
       		for($iVN = 1; $iVN <= $valNum; $iVN++) {	//Laufvar. von 1 bis ValNum
       			$foundNum[$iVN] = 0;
	   			for ($iR=0; $iR < count($allData)-1; $iR++) { //Laufvar. der Zeilen - 2. Mach solange es noch Zeilen gibt
	   				$rVN = (int)strtok($allData[$iR], $seperateColumn); //ValNum der Reihe ist Eintrag in der Reihe VOR dem ersten Seperator
	   				if ($rVN == $iVN) {
	   					$foundNum[$iVN]++;
						//$GLOBALS["debugText"][]= "-- Equivalent: " . $iVN . " - " . $rVN . " (in row " . $iR . ")";
						if ($foundNum[$iVN] == 1) { //Wenn diese ValNum nur zu ersten mal gefunden wurde, dann schreiben
							fwrite($fw,$allData[$iR]);
						}else{
							
						}//end if
					}//end if
	   			}//end for Row
			}//end for ValNum
			fclose($fw);

			for ($iFN=1; $iFN < count($foundNum)-2; $iFN++) {  //-2, da die aktuelle noch nicht vorhaneden sein kann, und die vorherige erst noch geschrieben werden muss!)
				if ($foundNum[$iFN] == 0) { //Wenn die ValNum kein einziges Mal geschrieben wurde
	   				$GLOBALS["debugText"][]= "= ValNum " . ($iFN) . " NOT found.";
	   				?><script type="text/javascript" language="Javascript">  alert(<?php echo '"Val.-Number ' . ($iFN) . ' NOT found! Please go back to this Val."';?>); </script><?php

	   			}elseif ($foundNum[$iFN] == 1){
	   				$GLOBALS["debugText"][]= "= ValNum " . ($iFN) . " found " . $foundNum[$iFN] . " times. Good!";
	   			}else{
	   				$GLOBALS["debugText"][]= "= ValNum " . ($iFN) . " found " . $foundNum[$iFN] . " times. Overmuch entries was deleted.";
	   				/*?><script type="text/javascript" language="Javascript">  alert(<?php echo '"ValNum ' . ($iFN) . " found " . $foundNum[$iFN] . ' times!"';?>); </script><?php*/
	   			}
	   		}

			$GLOBALS["debugText"][]= "END checkAllVals";
			$GLOBALS["debugText"][]= "";
     	}
	}

	function removeAllBehindValNum($filename, $valNum) {
		if(file_exists($filename)) {
	       	
			$allData = readAllData($filename);

			//write data
       		$fw = fopen($filename,"w");
       		for($i = 0;$i< $valNum-1;$i++) {
	   			fwrite($fw,$allData[$i]);
			}
			fclose($fw);
     	}
	}



	function writeAtLeastIfNotSameValNum($filename, $valNum, $value, $seperateColumn, $seperateRow) {
		if(file_exists($filename)) {
		  	
		  	$allData = readAllData($filename); //Liest erstmal alle Daten ein, um zu überprüfen, ob nächster Eintrag identisch sein könnte mit aktuell leztem

		  	for ($i=0; $i < count($allData)-1; $i++) {
		  		//$GLOBALS["debugText"][]= strtok($allData[$i], ' ') . " = " . $valNum;
				if (!strcmp(strtok($allData[$i], $seperateColumn), $valNum)) { //0 = identisch; Wird ausgeführt, wenn der Zeileneintrag in der Datei VOR dem ersten Seperator identisch ist mit valNum.
					$countDublicates++;
					$GLOBALS["debugText"][]= strtok($allData[$i], ' ') . " = " . $valNum . " --> Result: YES --> CountDublicates = " . $countDublicates;
					/*?><script type="text/javascript" language="Javascript">  alert(<?php echo '"' . strtok($allData[$i], ' ') . " = " . $valNum . " --> Result: YES --> CountDublicates = " . $countDublicates . '"';?>); </script><?php */
				}
			}


			if (!$countDublicates){ //nur wenn die Zeile noch nie zuvor geschrieben wurde (dublicates == 0), wird sie ausgeführt.
				$file = fopen($filename, 'a'); //Nur zum Schreiben geöffnet; platziere Dateizeiger auf Dateiende. Existiert die Datei nicht, versuche, diese zu erzeugen.
				$countDublicates = 0;
				fwrite($file, $valNum . $seperateColumn . $value . $seperateRow);
				fclose($file);
			} else {
				/*?><script type="text/javascript" language="Javascript">  alert(<?php echo '"' . $valNum . ': Dont add new row!"';?>); </script><?php*/
			}  

			$GLOBALS["writeFinish"] = 1;

		} else {
			$GLOBALS["debugText"][] = "ERROR! " . $filename . " does not exist";
		}
	}



	function printImages($imageSourceDB, $annFile, $stuff, $stuff2, $linecount, $totalNumbers, $fixNameCountry, $userName, $debug) {
		$allDataAnn = readAllData($annFile);

		//print out annotations filename
		$individualAnn = explode(" ",$allDataAnn[(int)$stuff]);

		//show image name
		if($fixNameCountry == true) {
			$name1Full = $individualAnn[0];
			//$countryIndex = strpos($name1Full,"COUNTRY");
			$countryIndex = strpos($name1Full,"_");
			$name1 = substr($name1Full,0,$countryIndex);
		} else {
			$name1 = $individualAnn[0];
		}

		$GLOBALS["debugText"][] = "Image1: " . $individualAnn[0];

		      
		$individualFile = rtrim($individualAnn[0],"|");
		//show randomized image
		$picturefile = $imageSourceDB . $individualFile;


		//get second image
		$individualAnn2 = explode(" ",$allDataAnn[(int)$stuff2]);
		//show image name
		if($fixNameCountry == true) {
			$name2Full = $individualAnn2[0];
			//$countryIndex = strpos($name2Full,"COUNTRY");
			$countryIndex = strpos($name2Full,"_");
			$name2 = substr($name2Full,0,$countryIndex);
		} else {
			$name2 = $individualAnn2[0];
		}

		$GLOBALS["debugText"][] = "Image2: " . $individualAnn2[0];
		      
		$individualFile2 = rtrim($individualAnn2[0],"|");
		//show randomized image
		$picturefile2 =  $imageSourceDB . $individualFile2;
			
		$GLOBALS["debugText"][] = $picturefile;
		if(file_exists($picturefile)) {

			$formText ='<input type="hidden" name="userName" value=' . $userName . '>
						<input type="hidden" name="debug" value=' . $debug . '>
						<input type="hidden" name="totalNumbers" value=' . '"'. ($totalNumbers)  . '"' .  '>';
						?>

			<h4>Choose most attractive image:</h4>
			<div id="pictures">
				<div id="picRowName">
			  		<div><p><?php echo $name1; ?></p></div>
					<div><p><?php echo $name2; ?></p></div>
			  	</div>
			  	<div id="picRowPic">
					<div class="picColumn">
						<form id="formLeft" action="reutprocess.php" method="post">
							<?php echo $formText; ?>
							<input type="hidden" name="numberVal" value="<?php echo ($linecount+1); ?>">
							<input type="hidden" name="rating" value="1">
							<input type="hidden" name="goodOrError" value="good">
							<input type="hidden" name="previousImage" value="<?php echo $individualFile; ?>">
							<input name="submit" type="image" id="LeftPicSubmit" class="picRowPicButton" onclick="avertAdditionalActions()" src="<?php echo $picturefile; ?>" alt="Left Image Is Most Attractive">
						</form>
					</div>
			 		<div class="picColumn">
			 			<form id="formRight" action="reutprocess.php" method="post">
			 				<?php echo $formText; ?>
							<input type="hidden" name="numberVal" value="<?php echo ($linecount+1); ?>">
							<input type="hidden" name="rating" value="0">
							<input type="hidden" name="goodOrError" value="good">
							<input type="hidden" name="previousImage" value="<?php echo $individualFile2; ?>">
							<input name="submit" type="image" id="RightPicSubmit" class="picRowPicButton" onclick="avertAdditionalActions()" src="<?php echo $picturefile2; ?>" alt="Right Image Is Most Attractive">
						</form>
					</div>
				</div>
			</div>		      		      			  
			<h5><i class="fa fa-arrow-left"></i> You can use the left and right arrow keys! <i class="fa fa-arrow-right"></i></h5>
			  				
			<?php if ($linecount > 1) {?>
				<div class='buttons'>
					<h3>Choose this button if previous choice was a mistake:</h3>
					<form id="formBack" action='reutprocess.php' method='post'>
						<?php echo $formText; ?>
						<input type="hidden" name="numberVal" value="<?php echo ($linecount-1); ?>">
					  	<input type="hidden" name="rating" value="9">
					  	<input type="hidden" name="goodOrError" value="error">
				  		<button name="submit" type="submit" class="submitButton" onclick="avertAdditionalActions()">Error, Go Back One</button>
				  	</form>
				</div>
				<?php
			}
		}
	}

	
?>