<html>
<head>
</head>
<body>
<?php
 /*   error_reporting(1);
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   error_reporting(E_ALL);  */	
   
   include("PHPMailer.php");
	
function getcontent($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8',));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	$response = curl_exec($ch);
	if(curl_errno($ch)) { curl_close($ch); return ;}
	curl_close($ch);
	$responseData=json_decode($response,true);
	return $responseData;
}

function sendMail($to,$subject,$message,$attach){
	try{
	$email = new PHPMailer();
	$email->SetFrom('supriyapandey013@gmail.com'); 
	$email->Subject   = $subject;
	$email->Body      = $message;
	$email->AddAddress($to);
	if(!empty($attach)){$email->AddAttachment( $attach , 'comic.png' );}
	$email->Send();
	 //echo "Message Sent OK\n";
	} catch (phpmailerException $e) {
	  //echo $e->errorMessage(); 
	} catch (Exception $e) {
	  //echo $e->getMessage(); 
	}
}

if($_POST['submit']==1){
	
	$to = $_POST['email']; 
	$subject = "OTP";
	$message = "
	<html>
	<head>
	<title>OTP</title>
	</head>
	<body>
	<p>".$_POST['otp']." is your OTP</p>
	</body>
	</html>
	";
	sendMail($to,$subject,$message,null);	
	echo "Check your email for OTP ".$_POST['otp']."</br>";
}

if($_POST['submit']==2){
	if($_POST['otp']==$_POST['eotp']){
		$random=random_int(1,2400);
		ob_start();
		$top_rated=getcontent('https://xkcd.com/'.$random.'/info.0.json');
		ob_end_clean();
		
		echo $message = "
		<html>
		<head>
		<title>".$top_rated['safe_title']."</title>
		</head>
		<body>
		<p>".$top_rated['alt']."</p>
		<p>".$top_rated['transcript']."</p>
		<img src='".$top_rated['img']."' >
		</body>
		</html>
		";
	
		sendMail($_POST['email'],$top_rated['safe_title'],$message,$top_rated['img']);	
		echo "<hr><br>Check your email for XKCD comic </br>";
	}else{
		echo "Invalid OTP </br>";
		$_POST['submit']=1;
	}
}



?>

<hr>
<br>

<div id="div1" <?php if(!empty($_POST['submit'])){echo'hidden';} ?>>
<form method="post">
Enter Email Id
<input type="email" id="email" name="email" >
<input type="hidden" name="otp" value="<?php echo random_int(100000,999999);?>">
<button type="submit" name="submit" value="1">Request</button>
</form>
</div>


<div id="div2" <?php if($_POST['submit']!=1){echo'hidden';} ?>>
<form method="post">
Enter OTP
<input type="text" id="eotp" name="eotp" >
<input type="hidden" name="email" value="<?php echo $_POST['email'];?>">
<input type="hidden" name="otp" value="<?php echo $_POST['otp'];?>">
<button type="submit" name="submit" value="2">Verify</button>
</form>
</div>




</body>
</html>