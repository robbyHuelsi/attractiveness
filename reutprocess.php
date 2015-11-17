<html>
	<head>
		<title>The Olympic Pairs Attractiveness Survey</title>
  		<link href="style.css" rel="stylesheet" media="screen" type="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Exo' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Gloria+Hallelujah' rel='stylesheet' type='text/css'>
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
		<script language="javascript" type="text/javascript" src="script.js"></script>
 	</head>

	<body>
 		<?php
 			include_once "functions.php"; //Ruft neue .php auf, aber nur einmal!

 			$writeFinish = 0;

 			if (!($_POST['userName'] || $_POST['numberVal'] || $_POST['totalNumbers'])) { ?>
 				<header>
					<h1>The Olympic Pairs Attractiveness Survey</h1>
				</header>

				<main>
					<div class="sticky taped">User not found :-(</div>
				</main>

 			<?php } else {
	
				// directories
				$saveDirectory = "attractivenesssurveysolympics/";
				$annFile = "annotations/annotations.dat";
	
				$imageSourceDB = 'olympicstomato/';

				// name processing, remove after Firstname for Olympic images
    			$fixNameCountry = True;
	  
   				//check if good rather than error
   				$goodCheck = $_POST['goodOrError'];
  
   				checkAllVals($saveDirectory . $_POST['userName'] . '/values.txt', $_POST['numberVal'], " ");
	   			checkAllVals($saveDirectory . $_POST['userName'] . '/images.txt', $_POST['numberVal'], " ");
	   			checkAllVals($saveDirectory . $_POST['userName'] . '/values.csv', $_POST['numberVal'], ";");


   				if($goodCheck == "good") { //Wenn auf Links oder Rechts geklickt wurde
					$debugText[] = "good";

     			} else if($goodCheck == "error") { // Wenn auf "Error, Go Back One" gedrückt wurde, werden die hinteren Zeilen abgeschnitten
	   				//removeAllBehindValNum($saveDirectory . $_POST['userName'] . '/values.txt', $_POST['numberVal']);
	   				//removeAllBehindValNum($saveDirectory . $_POST['userName'] . '/images.txt', $_POST['numberVal']);
	   				//removeAllBehindValNum($saveDirectory . $_POST['userName'] . '/values.csv', $_POST['numberVal']);
       			
	       			$debugText[] = "You went back. Files was modified";
	       		}

	       		//Menüleiste erstellen
	       		$totalVal = $_POST['totalNumbers']/2-1; //Die Anzahl der Bewertungen ist die Hälfte der Bilderanzahl minus letzte leere Zeile
 				$percent = round(($_POST['numberVal']-1) / $totalVal * 100, 2);
 				if ($percent > 99.7) {$percent = 100;}
 				$cell1 = "User: " . $_POST['userName'];
 				$cell2 = ($_POST['numberVal'] > 576) ? ("Done!") : ("#" . $_POST['numberVal']);
 				$cell3 = $percent . "%";
 				$cell4 = $totalVal - $_POST['numberVal'] . " left";
 				?>
 				<header>
 					<div id='progressbar'>			
	  					<table id="progress_info">
	  						<tr style="100%">
	  							<td class="progress_infoCell_notOnSmall"><?php echo $cell1; ?></td>
		  						<td class="progress_infoCell"><?php echo $cell2; ?></td>
		  						<td class="progress_infoCell"><?php echo $cell3; ?></td>
		  						<td class="progress_infoCell_notOnSmall"><?php echo $cell4; ?></td>
	 						</tr>
	 					</table>
 					
 						<div id='bar' style="width: <?php echo $percent; ?>%"></div>
 					</div>
 				</header>

 				<main>
 				<?php


   
	  			//check if all are already rated	  
				if(($_POST['numberVal'] ) == $totalVal) { ?>
	      			<br>
					<div class="sticky taped">
						Done!<br>Thank you for your efforts on rating the attractiveness of people.<br>Without your efforts, computers would not be able to automatically estimate the attractiveness of humans.
						<p>Greetings Attractiveness Rater<br>and <a id="link" href="http://www.grafiksoft3.de">grafiksoft&#179;</a></p>
					</div>
	      
                    <?php
	    			
	    			// write last rating
	    			writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/values.txt', $_POST['numberVal'] -1, $_POST['rating'], " ", "\n");
	      			//write last image		      
	      			writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/images.txt', $_POST['numberVal'] -1, $_POST['previousImage'], " ", "\n");
	      			//write last CSV entry
	    			writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/values.csv', $_POST['numberVal'] -1, $_POST['rating'] . ";" . $_POST['previousImage'] , ";", "\n");
		  

				} else { //Noch nicht zu Ende abgestimmt
	      			//load in values
	      			//check if file exists
	      			$filename = $saveDirectory . $_POST['userName'] . '/randomized.txt';

	  	
	      			if(file_exists($filename)) {
		  				$debugText[] = $filename;
	      				
			  			$allData = readAllData($filename); //Alle Einträge aus Random-Datei

		  				$totalNumbers = $_POST['totalNumbers'];
			  			$debugText[] = "Total Numbers " . $totalNumbers;

			  			$linecount = $_POST['numberVal']; //Zeile der passenden ersten Bildnummer in Random-Datei
			  			$stuff = $allData[$linecount-1];  //Nummer des ersten Bildes aus der Radom-Datei

			  			$debugText[] = "Index " . ($linecount-1) . " Randomized Value " . $stuff;

    	    	  		//$modval = ($linecount + 50)%$totalNumbers;
			  			$modval = ($linecount-1 + $totalNumbers/2)%$totalNumbers; //Zeile der passenden zweiten Bildnummer in Random-Datei

	 	  				$debugText[] = "modval " . $modval;

		  				$stuff2 = $allData[$modval]; //Nummer des zweiten Bildes aus der Radom-Datei
	
		  				$debugText[] = "Index Two Randomized Value " . $stuff2;

			  			//append value if not first time through
			  			if(($linecount-1) == 0){
			      			$debugText[] = "First Rating";
		    			} else {
		    				$debugText[] = "Non-First Rating";
		      
			      			$testcase = $_POST['rating'];
				      		if (ctype_digit($testcase)) { //Prüft, ob Rating-String nur aus Ziffern besteht
					        	$debugText[] = "The string " . $testcase . " consists of all digits.";
			    	  		} elseif (!ctype_digit($testcase) && $testcase) { //Wenn der Rating-String nicht nur aus Zahlen besteht und nicht leer ist, dann Warnmeldung
		    	    			echo '<br><h1><FONT COLOR="FF0000">Warning!!!!!!!!!!!!!!!!!!</FONT></h1><br>The previous rating does not consist of all digits!!!!!!! Go back and rerate the previous image. <br>Disregard if you have just restarted rating images again.<br> ' . $testcase . 'X';
		      				}

		     		
			      		if($_POST['rating'] == 1) {
							$debugText[] = "Previous Rating: Left Image";
						}
		    	  		if($_POST['rating'] == 0) {
							$debugText[] = "Previous Rating: Right Image";
						}
			      		if($_POST['rating'] == 9) {
							$debugText[] = "Previous Rating: Error, Rerating";

							// show previous image name
							// fix for Olympics country
		      				if($fixNameCountry == true) {
								$namePreviousFull = $_POST['previousImage'];;
								$countryIndex = strpos($namePreviousFull,"COUNTRY");
								$namePrevious = substr($namePreviousFull,0,$countryIndex-1);
							} else {
								$namePrevious = $_POST['previousImage'];;
							}
		      
			  				$debugText[] = "Previous Image: "  . $namePrevious;
			  			}


			    		// only write if not an error
			    		if($goodCheck == "good") {
			    			// write last rating
	    						writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/values.txt', $_POST['numberVal'] -1, $_POST['rating'], " ", "\n");
	    		  			//write last image		      
	      						writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/images.txt', $_POST['numberVal'] -1, $_POST['previousImage'], " ", "\n");
	      					//write last CSV entry
	    						writeAtLeastIfNotSameValNum($saveDirectory . $_POST['userName'] . '/values.csv', $_POST['numberVal'] -1, $_POST['rating'] . ";" . $_POST['previousImage'], ";", "\n");	      						
						}
		    		}

					if(file_exists($annFile)) {

						usleep(250000); //Warten in Micro-Sekunden
						/*Warten bis Dateien fertig geschrieben wurden
						$timeout = 100;
						$iWait = 0;
						while ($iWait < $timeout && $writeFinish == 0) {
							usleep(1);
							$iWait++;
						}
						$debugText[] = "Wait time: " . $iWait;*/
						printImages($imageSourceDB, $annFile, $stuff, $stuff2, $linecount, $totalNumbers, $fixNameCountry, $_POST['userName'], $_POST['debug']);
					}
		    	}     
	    	}
	    }

	if ($_POST['debug']) {
		echo "<h3>Debug results:</h3>";
		echo "<div id='debugBox'><lo>";
		$len = count($debugText);
		for ($i=0; $i < $len; $i++) { 
			echo "<li>" . $debugText[$i] . "</li>";
		}
		echo "</lo></div>";
	}

	?>

  </main>
 </body>
</html>
