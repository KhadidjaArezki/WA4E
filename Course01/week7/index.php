<!DOCTYPE html>
<head><title>Khadidja Arezki MD5 Cracker</title></head>
<body>
<h1>MD5 cracker</h1>
<p>This application takes an MD5 hash of a four-digit PIN and attempts to hash all four-digit combinations to determine the original PIN.</p>
<pre>
Debug Output:
<?php
   $goodtext = "Not Found";
   $PIN = "008456";
   print nl2br($PIN.' '.hash('md5',$PIN)."\n");

   if (isset($_GET['md5'])) {
	   $start = microtime(true);
	   $md5 = $_GET['md5'];
	   
   
	   $digits = "0123456789abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ_-.,:?!;()[]{}'\"";
	   //print nl2br($digits."\n");
	   //$show = 15;
	   $totalchecks = 0;
	   $time = round(microtime(true));
	   for ($i = 0; $i < strlen($digits); $i++) {
	   	    $dg1 = $digits[$i];
	   	  for ($j = 0; $j < strlen($digits); $j++) {
	   	      $dg2 = $digits[$j];
	   	      for ($k = 0; $k < strlen($digits); $k++) {
	   	          $dg3 = $digits[$k];
	   	          for ($m=0; $m <strlen($digits) ; $m++) { 
	   	              $dg4 = $digits[$m];
	   	              for ($n=0; $n <strlen($digits) ; $n++) { 
	   	                  $dg5 = $digits[$n];
	   	                  for ($l=0; $l <strlen($digits) ; $l++) { 
	   	             	      $dg6 = $digits[$l];

			   	              $try =  $dg1.$dg2.$dg3.$dg4.$dg5.$dg6;
			   	              $check = hash('md5', $try);

			   	              if ($check == $md5) {
			   	              	print nl2br('Found it! md5:'.' '.$md5."\n");
			   	              	$goodtext = $try;
			   	              	break 6;
			   	              }

			   	              if ($time + 3 == round(microtime(true))) { //$totalchecks % 100000 == 0){//(round(microtime(true)/ 1000000) % 3 == 0) {
			   	              	$time = round(microtime(true));
			   	              	print nl2br("$check $try\n");
			   	              	print nl2br("time now: ".round(microtime(true)));
			   	              	print "\n";

			   	              	//$show--;			   	              	
			   	              }
	   	              		  $totalchecks++;
	   	              		}
	   	                }
	   	            }
	   	        }
	   	    }   	  
	    }
   print "Total checks: ".$totalchecks;
   print "\n";
   $end = microtime(true);
   print "Elapsed time: ";
   print $end - $start;
   print "\n";
}
?>
</pre>
<p>PIN: <?= htmlentities($goodtext); ?></p>
<form>
<input type="text" name="md5" size="60" />
<input type="submit" value="Crack MD5"/>
</form>