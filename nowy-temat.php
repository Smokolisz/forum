<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
	}
	else
	{
		$zalogowany=false;
		header('Location: login.php');
		exit();
		
	}
	
	$_SESSION["pin"]=1;
	$kategoria = htmlspecialchars($_GET["id"]);
	
	if(!$kategoria)
	{
		header('Location: index.php');
		exit();
	}
	
	/* SQL */
	ini_set("display_errors", 1);
	require_once 'administrator/dbconnect.php';
	$polaczenie = mysqli_connect($host, $user, $password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $database);
		
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		echo "\n Error: ".$polaczenie->connect_errno." Opis: ".$polaczenie->connect_error;
	}
	
	
	
	if (isset($_POST['nazwa-tematu'])&&($zalogowany==true))
	{
		//Bot or not? Oto jest pytanie!
		$sekret = "6LdruGEUAAAAAMNsq466jHmY6OQVfEkdBmQ87R5O";	
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if($odpowiedz->success==true)
		{
			if(isset($_POST['przypiety']))
			{
				$przypiety=1;
			}
			else $przypiety=0;
			
			$tytul = $_POST['nazwa-tematu'];
			$tresc = $_POST['tresc'];
			$user = $_SESSION["user"];
			$idUsera = $_SESSION['id'];
			
			if ($polaczenie->query("INSERT INTO tematy VALUES (NULL, '$idUsera', '$tytul', 0, '$przypiety', 1, NULL, '$kategoria')"))
			{
				$id = mysqli_query($polaczenie,"SELECT id FROM tematy ORDER BY id DESC LIMIT 1"); // jakby nie działało uzuń limit 1
				$row = mysqli_fetch_assoc($id);
								
				$idTematu = $row["id"];
					
				if ($polaczenie->query("INSERT INTO posty VALUES (NULL, '$idTematu', '$idUsera', 1, '$tytul', '$tresc', '', NULL, '$user', 1)"))
				{
					$polaczenie->query("UPDATE uzytkownicy SET punkty=punkty+5 WHERE id='$idUsera'");
					header('Location: temat.php?id='.$idTematu); //PRZEKIEROWANIE DO UTOWORZONEGO TEMATU
				}
				
			} else {
			
			echo "Nie udało się dodać nowego tematu! Skontaktuj się z webmasterem!";
			printf("Errormessage: %s\n", $polaczenie->error);
			}
		}	
		else if($odpowiedz->success==true)
		{
			
		$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}	
	}
		
		

?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	
	<title>Wolne Forum</title>
	
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="description" content="Opis w Google" />
	<meta name="keywords" content="słowa, kluczowe, wypisane, po, porzecinku" />

	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/brands.css" integrity="sha384-7xAnn7Zm3QC1jFjVc1A6v/toepoG3JXboQYzbM0jrPzou9OFXm/fY6Z/XiIebl/k" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">
	
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
</head>
<body>

	<main id="container">
		
		<div id="logo">
			<img src="img/logo.png" width="100%"/>
		</div>
		
		<section id="nav">
			<?php
			if (!$zalogowany)
			{
			echo   '<a href="rejestracja.php"><nav class="navigation-item">
						zarejestruj
					</nav></a>
					<a href="login.php"><nav class="navigation-item">
						zaloguj
					</nav></a>
					<a href="index.php"><nav class="navigation-item">
						Strona Główna
					</nav></a>
					<div class="clear-both""></div>';
			} else {	
			echo   '<a href="wyloguj.php"><nav class="navigation-item">
						wyloguj
					</nav></a>
					<a href="moje-konto.php"><nav class="navigation-item">
						moje konto '.$_SESSION["user"].'
					</nav></a>
					<a href="index.php"><nav class="navigation-item">
						Strona Główna
					</nav></a>
					<div class="clear-both"></div>';
			}
			
			?>
		</section>
		<div id="content">
			
				<section id="panel-nowy-temat">
				
					<header style="width:100%;text-align:center;font-size:25px;">
						
						<h1>Napisz nowy temat w dziale <?php  

							$id2 = mysqli_query($polaczenie,"SELECT nazwa FROM kategorie WHERE id = ".$kategoria);
							$row2 = mysqli_fetch_assoc($id2);
							
							echo $row2["nazwa"];

						?></h1>
						
					</header>
					
					<?php
					
					if ($zalogowany)
					{
						echo	'<div class="nowy-temat">
					
									<div class="nowa-kategoria-nazwa">
									
										<form method="post">
										
											<label for="username" style="font-size:17px;padding-left:0px;">Temat - o czym będzie dyskusja?</label>
											<input type="text" style="width:500px;" id="username" name="nazwa-tematu" required>
											<textarea type="text" style="width:500px;min-height:300px;margin-top:15px;" id="username" name="tresc" required></textarea>
											
											<div class="g-recaptcha" style="margin: 15px 0px 5px 0px;" data-sitekey="6LdruGEUAAAAAIfFO4YXwadZudM_euD-FgevtgHc" id="captcha"></div>';
											
											if (isset($_SESSION['e_bot']))
											{
												echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
												unset($_SESSION['e_bot']);
											}
							
						echo				'<div class="bottom-send">
											
												<div class="bottom-send-left">';
											
						if($_SESSION["pin"])
						{
							echo			'<input type="checkbox" name="przypiety"><label class="check" for="checkbox" >Przypięty</label>'; 
						}
						
						echo				'</div>
											<div class="bottom-send-right"><input type="submit" value="Dodaj"></div>
											<div class="clear-both"></div>
											</div>
											
										</form>
										
									</div>
									
								</div>';
					}
					
					?>
					
					
					

				</section>
				
			<div style="width:100%;height:100px;"></div>
			
		</div>
		<footer id="footer">
			
				<div id="bottom-footer">
				
					
					
					<div style="margin-left:auto;margin-right:auto;width:258px;">
						
						<a href="http://www.teampelikan.pl/" target="_BLANK"><div class="social-media">
							<img src="img/teampelikan.png" id="teampelikan">
						</div></a>
						
						<a href="https://www.facebook.com/TeamPelikan-1178817782151548/" target="_BLANK"><div class="social-media">
							<i class="fab fa-facebook-square" style="font-size:60px;"></i>
						</div></a>
						
						<a href="https://www.youtube.com/channel/UCeD5R1V6mOj32zBy6LIwGCg" target="_BLANK"><div class="social-media" >
							<i class="fab fa-youtube" style="font-size:70px;line-height:58px;"></i>
						</div></a>
						<div style="clear:both;padding-bottom:30px;" ></div>
						
						<address class="author">By <a rel="author" href="http://www.teampelikan.pl/adach/index.php">Adam Czwordon</a></address>
					
						<dl class="dateline" >
						
							<dt class="float-left">Kontakt:</dt>
							<dd style="float:left;margin:0px 0px 0px 10px;"> <a href="mailto:smokoliszone@gmail.com">smokoliszone@gmail.com</a></dd>
						
						</dl>
						
					</div>
					
				    <div class="clear-both"></div>
					
				</div>
			
			</footer>
			
			<?php	

			mysqli_free_result($id2);

			mysqli_close($polaczenie);

			?>

	</main>
	
	<script type="text/javascript" src="whcookies.js"></script>
	<script>
	function WHCreateCookie(name, value, days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    var expires = "; expires=" + date.toGMTString();
	document.cookie = name+"="+value+expires+"; path=/";
	}
	function WHReadCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
		}
		return null;
	}

	window.onload = WHCheckCookies;

	function WHCheckCookies() {
		if(WHReadCookie('cookies_accepted') != 'T') {
			var message_container = document.createElement('div');
			message_container.id = 'cookies-message-container';
			var html_code = '<div id="cookies-message" style="color:white;padding: 10px 0px; font-size: 14px; line-height: 22px; border-bottom: 1px solid #e9b361; text-align: center; position: fixed; top: 0px; background-color: #e9c185; width: 100%; z-index: 999;">Ta strona używa ciasteczek (cookies), dzięki którym nasz serwis może działać lepiej. <a href="http://wszystkoociasteczkach.pl" target="_blank">Dowiedz się więcej</a><a href="javascript:WHCloseCookiesWindow();" id="accept-cookies-checkbox" name="accept-cookies" style="background-color: #00AFBF; padding: 5px 10px; color: #FFF; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; display: inline-block; margin-left: 10px; text-decoration: none; cursor: pointer;">Rozumiem</a></div>';
			message_container.innerHTML = html_code;
			document.body.appendChild(message_container);
		}
	}

	function WHCloseCookiesWindow() {
		WHCreateCookie('cookies_accepted', 'T', 365);
		document.getElementById('cookies-message-container').removeChild(document.getElementById('cookies-message'));
	}	
	</script>
	
</body>
</html>