
<html>
	<head>
		<title>The Olympic Pairs Attractiveness Survey</title>
		<link href="style.css" rel="stylesheet" media="screen" type="text/css" />
		<link href='http://fonts.googleapis.com/css?family=Exo' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Gloria+Hallelujah' rel='stylesheet' type='text/css'>
	</head>
	
	<body>
		<header>
			<h1>The Olympic Pairs Attractiveness Survey</h1>
		</header>

<?php
	$newdirectory = "notfound";

	//directories
	$saveDirectory = "attractivenesssurveysolympics/";
	$annFile = "annotations/annotations.dat";

	// where images are located  	
	$imageDirectory = "olympicstomato";

	$debugText = array();
?>

<main>
	<div class="sticky taped">

		<?php
			if (!$_POST['yourname']) {
				//User konnte nicht von dem Eingabefeld der vorherigen Seite übernommen werden
				echo "User not found :-(";
    		} else {
      			//User wurde von Eingabefeld übernommen
      			$newdirectory = $_POST['yourname'];  
  				$previousDirectory = $saveDirectory . $newdirectory;

  				if(is_dir($previousDirectory)) { //Gibt es ein Ordner für den User?
      				echo "Welcome back, " . $_POST['yourname'] . "!<br>Please continue rating attractiveness.<br>";
      
      				//find out count of ratings already            
      				$ratingFile = $saveDirectory . $newdirectory . '/values.txt';
					$debugText[] = "Rating file: " . $ratingFile;

					if(file_exists($ratingFile)) { //Gibt es die Ergebisdatei?
	  					$debugText[] = "File exists";
			  			//check number of lines in values.txt file	  
			  			$linecount = 0;
	  					$handle = fopen($ratingFile, "r");
	  					while(!feof($handle)){
	    					$line = fgets($handle);
	    					$linecount++;
		  				}	  
		  				fclose($handle);


			  			//check total number
			  			$totalFile = $saveDirectory . $newdirectory . '/randomized.txt';
	  					$totalNumbers = 0;
	  					$handleTotal = fopen($totalFile, "r");
	  					while(!feof($handleTotal)){
	    					$line = fgets($handleTotal);
		    				$totalNumbers++;
			  			}	  
		  				fclose($handleTotal);

		  				//------
		  					$linecount;
		  				//------

			  			// -2 used to correct for blank line at end plus some other factor
			  			$totalNumbers = $totalNumbers -2;
			  			echo "<br>Previous ratings: " . $linecount . " of " . $totalNumbers / 2;
	  					$debugText[] = "Number of images: " .$totalNumbers;

		  				//Variablen zur Weitergabe festlegen
		  				$userName = $newdirectory;
	  					$numberVal = $linecount;
		  				//$totalNumbers ist schon festgelegt
						$goodOrErrorVal = "restart";

					} else {
		  				echo "<br>There is no rating file :-(";
					}

				} else { //create new directory and associated data files
      				echo "Welcome, " . $_POST['yourname'] . "!<br><br>";

      				//load in all images
		      		$myFile = $annFile;          
      				if(file_exists($myFile)) {
			  			$debugText[] = "Afdb directory exists " . $myFile;
					} else {
			  			$debugText[] = "Afdb directory doesn't exist " . $myFile;
					}

		      		$fh = fopen($myFile, 'r');
      				$ii = 0;
		      		while(! feof($fh)) {
	  					$theData = fgets($fh);	 
		  				$allData[$ii] = $theData; 	  
				  		$ii = $ii+1;
					}

    	  			$debugText[] = "Database size: " . sizeof($allData) . "<br>";
		      		$debugText[] = "First data item: " . $allData[0] . "<br>";
	    		  	$fileraw  = explode(" ",$allData[0]);
		    	  	$picturefile = $imageDirectory . "/" . $fileraw[1];
      				$debugText[] = "URL: " . $picturefile . "<br>";
  
			      	//do one less than sizeof($allData) since \n gets included as extra
    			  	//item besides image names
      				for($jj=0 ; $jj<sizeof($allData) -1; $jj++) {
			  			$randomized[$jj] = mt_rand();
				  		$randomizedvals[$jj] = $jj; 	 
					}

		      		// randomize      
	    		  	for($jj=0 ; $jj<sizeof($randomized) ; $jj++) {
		  				for($kk=0;$kk<sizeof($randomized)-1;$kk++) {
					      	if($randomized[$kk] > $randomized[$kk+1]) {
						  		$temp = $randomized[$kk];
			  					$randomized[$kk] = $randomized[$kk+1];
		  						$randomized[$kk+1] = $temp;
					  			$tempx = $randomizedvals[$kk];
						  		$randomizedvals[$kk] = $randomizedvals[$kk+1];
						  		$randomizedvals[$kk+1] = $tempx;
							}
				    	}
					}

      				$debugText[] = $randomizedvals[0];
			      	$fileraw  = explode(" ",$allData[$randomizedvals[0]]);
    			  	$picturefile2 = $imageDirectory . $fileraw[1];
 
		 	     	//change umask so directory gets full permissions
    			  	$oldmask = umask(0);
		      		if(mkdir($saveDirectory . $newdirectory, 0777)) {
	  					umask($oldmask);
		  				//write randomized data
				  		$randomizedfile = $saveDirectory . $newdirectory . '/randomized.txt';
				  		$debugText[] = "Path: " . $randomizedfile . "<br>";

				  		$fp = fopen($randomizedfile, 'w');
		  				for($ll=0;$ll<sizeof($allData);$ll++) {
			    	  		fwrite($fp, $randomizedvals[$ll]);
	    		  			fwrite($fp,"\n");
	  					}
				  		fclose($fp);

				  		//change permissions
	  					chmod($randomizedfile, 0777);
	  
			  			//start file with attractivenessvalues
				  		$attractivenessfile = $saveDirectory . $newdirectory . '/values.txt';

		  				$fp2 = fopen($attractivenessfile,'w');
			  			fclose($fp);
	  					//change permissions
		  				chmod($attractivenessfile, 0777);

				  		//start file with image names
	  					$imagefile = $saveDirectory . $newdirectory . '/images.txt';
	  					$fp3 = fopen($imagefile,'w');
				  		fclose($fp);
				  		//change permissions
	  					chmod($imagefile, 0777);

	  					//start file with image names
	  					$csvfile = $saveDirectory . $newdirectory . '/values.csv';
	  					$fp3 = fopen($csvfile,'w');
				  		fclose($fp);
				  		//change permissions
	  					chmod($csvfile, 0777);
	 
					}

					echo "You're new here, so we just created your new account.<br> Let's rate!";

					//Variablen zur Weitergabe festlegen
					$userName = $_POST['yourname'];
					$numberVal = "1";
					$totalNumbers = sizeof($randomized);
					$goodOrErrorVal = "good";
		    	}
    
    		}
		?>
	</div>

    <!--send more to next file via form-->
    <div class="buttons">
		<form action="reutprocess.php" method="post">      
			<?php
				echo '<input type="hidden" name="userName" value=' . $userName . '>';
				echo '<input type="hidden" name="debug" value=' . $_POST['debug'] . '>';
	    		echo '<input type="hidden" name="numberVal" value=' . '"'. $numberVal  . '"' .  '>';
				echo '<input type="hidden" name="totalNumbers" value=' . '"'. $totalNumbers  . '"' .  '>';
				echo '<input type="hidden" name="goodOrError" value=' . '"'. $goodOrErrorVal  . '"' .  '>';

				if ($_POST['yourname']){
					echo '<button name="submit" type="submit" class="submitButton">Proceed</button>';
				}
			?>
		</form>
	</div>

	<?php
	if ($_POST['debug']) {
		echo "<h3>Debug results:</h3>";
		echo "<div id='debugBox'><lo>";
		$len = count($debugText);
		for ($i=0; $i < $len; $i++) { 
			echo "<li>" . $debugText[$i] . "</li>";
		}
		echo "</lo></div>";
	} ?>

</main>
 </body>
</html>

