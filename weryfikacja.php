<?php

	session_start();
	
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
	
	
	$id = htmlspecialchars($_GET["id"]);
	//echo $id;
	if(!$id)
	{
		header('Location: index.php');
		exit();
	}
	
	if(isset($_SESSION['upowaznienie'])&&($_SESSION['upowaznienie']!=1))
	{
		$polaczenie->query("DELETE FROM email WHERE kod='$id'");
		header('Location: index.php');
		exit();
	}
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
		if ($id2 = mysqli_query($polaczenie,"SELECT * FROM email WHERE kod='$id'"))
		{
			$ile = mysqli_num_rows($id2);
			//echo '<br><br>'.$ile.'<br><br>';
			
			$data=date("Y-m-d");
			$czas=date("H:i");
			$row = mysqli_fetch_assoc($id2);
			$roznica = (strtotime($data.$czas) - strtotime($row["dataWyslania"]));
			$min = floor($roznica / 60);
			$sec = $roznica-($min*60);
			$h = floor($min/60);
			$min = $min-($h*60);
			$dni = floor($h/24);
			
			//echo $min;
			
			$idSesji = $_SESSION["id"];
			$idSQL = $row["idUsera"];
			
			if($idSesji==$idSQL)
			{
				//echo "<br><br>".$idSesji."     a    ".$idSQL;
				
				if(($ile==1)&&(abs($min)<=100))
				{
					if (($polaczenie->query("UPDATE uzytkownicy SET punkty=punkty+25 WHERE id=".$_SESSION["id"].""))&&($polaczenie->query("DELETE FROM email WHERE kod='$id'"))&&($polaczenie->query('UPDATE uzytkownicy SET permissions=2 WHERE id='.$_SESSION["id"].' AND id='.$row["idUsera"])))
					{
						$_SESSION['werdykt'] = "Email potwierdzony";
						$id2 = mysqli_query($polaczenie,"SELECT permissions FROM uzytkownicy WHERE id=".$_SESSION["id"]);
						$row = mysqli_fetch_assoc($id2);
						$_SESSION['upowaznienie'] = $row['permissions'];
					}
					else
					{
						echo "error";
						printf("Errormessage: %s\n", $polaczenie->error);
					}
				}
				else
				{
					$polaczenie->query("DELETE FROM email WHERE kod='$id'");
					$_SESSION['werdykt'] = "Ten link wygasł, na twój email został wysłany nowy link!";
					require_once 'email.php';
				}
			} 
			else
			{
				$polaczenie->query("DELETE FROM email WHERE kod='$id'");
				$_SESSION['werdykt'] = "Ten link wygasł, na twój email został wysłany nowy link!";
				require_once 'email.php';
			}
		}
	}
	else
	{
		if(isset($_POST['login']))
		{
			$zalogowany=false;
		
			//Bot or not? Oto jest pytanie!
			$sekret = "6LdruGEUAAAAAMNsq466jHmY6OQVfEkdBmQ87R5O";
			$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
			$odpowiedz = json_decode($sprawdz);
			if ($odpowiedz->success==false)
			{
				$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
				header('refresh: 0;');
				exit();
			}	

			try 
			{
				$polaczenie = new mysqli($host, $user, $password,$database);
				
				mysqli_query($polaczenie, "SET CHARSET utf8");
				mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
				
				if ($polaczenie->connect_errno!=0)
				{
					throw new Exception(mysqli_connect_errno());
				}
				else
				{
					$login = $_POST['login'];
					$haslo = $_POST['haslo'];
					$_SESSION['fr_nick'] = $login;
					
					$login = htmlentities($login, ENT_QUOTES, "UTF-8");
				
					if ($rezultat = @$polaczenie->query(
					sprintf("SELECT * FROM uzytkownicy WHERE user='%s'",
					mysqli_real_escape_string($polaczenie,$login))))
					{
						$ilu_userow = $rezultat->num_rows;
						
						if($ilu_userow>0)
						{
							$wiersz = $rezultat->fetch_assoc();
							
							if (password_verify($haslo, $wiersz['pass']))
							{
								$_SESSION['zalogowany'] = true;
								
								
								$_SESSION['id'] = $wiersz['id'];
								$_SESSION['user'] = $wiersz['user'];
								$_SESSION['upowaznienie'] = $wiersz['permissions'];
								
								$idUsera=$_SESSION['id'];
																
								if (!$polaczenie->query("UPDATE uzytkownicy SET ostatnioWidziany=CURRENT_TIMESTAMP WHERE id='$idUsera'"))
								{
									throw new Exception($polaczenie->error);
								}
								
								unset($_SESSION['blad']);
								$rezultat->free_result();
								header('refresh: 0;');
								exit();
							} else {
								$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
								$_SESSION['liczba-bledow']++;
								header('refresh: 0;');
								exit();
							}
							
						} else {
							
							$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
							$_SESSION['liczba-bledow']++;
							header('refresh: 0;');
							exit();
						}
						
					}
					else
					{
						throw new Exception($polaczenie->error);
					}
					
					$polaczenie->close();
				}
			}
			catch(Exception $e)
			{
				echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o wizytę w innym terminie!</span>';
				echo '<br />Informacja developerska: '.$e;
			}
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

	<script type="text/javascript" async="" src="https://www.google-analytics.com/analytics.js"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
</head>
<body>

	<main id="container">
		
		<div id="logo">
			<img src="img/logo.png" width="100%"/>
		</div>
		
		<?php
			if(isset($_SESSION['werdykt']))
			{
				echo '<h1 class="text-align">'.$_SESSION['werdykt'].'</h1>';
			}
			
			if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true)) {}
			else
			{
		
				echo 	'<div id="content" style="min-height:600px !important;">
							<div style="width:100%;height:100px;"></div>
							<header id="section">
								<h2 style="margin:0px 0px 0px 5px;line-height:20px;">Zaloguj sie</h2>
							</header>
							
							<div id="panel">
								<form method="post">
									<label for="username">Nazwa użytkownika:</label>
									<input type="text" id="username" name="login" value="';
									if (isset($_SESSION['fr_nick']))
									{
										echo $_SESSION['fr_nick'];
										unset($_SESSION['fr_nick']);
									}
				echo				'"required>
									<label for="password">Hasło:</label>
									<input type="password" id="password" name="haslo" required>';	
										if(isset($_SESSION['blad']))	echo '<div class="small-rectangle">'.$_SESSION['blad'].'</div>';
										if($captcha=true)
										{
											echo '<div class="g-recaptcha" data-sitekey="6LdruGEUAAAAAIfFO4YXwadZudM_euD-FgevtgHc" id="captcha" style="margin-top:20px;"></div>';
											if (isset($_SESSION['e_bot']))
											{
												echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
												unset($_SESSION['e_bot']);
											}
										}		
				echo				'<div id="lower">
										<input type="checkbox" ><label class="check" for="checkbox" >Zapamiętaj mnie!</label>
										<input type="submit" value="Login">
									</div>
								</form>
							</div>
						</div>';
			}
			?>

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