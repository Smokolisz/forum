<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
	}
	else
	{
		header('Location: login.php');
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
	
	$idUsera = $_SESSION['id'];
	
	if(isset($_POST['quote']))
	{
		$quote = $_POST['quote'];
		
		if (!$polaczenie->query("UPDATE uzytkownicy SET quote='$quote' WHERE id='$idUsera'"))
		{
			throw new Exception($polaczenie->error);
		}
	}
	
	if (isset($_FILES['plik']))
	{
		if(is_uploaded_file($_FILES['plik']['tmp_name']))
		{
			$id = mysqli_query($polaczenie,"SELECT id FROM uzytkownicy WHERE id = ".$_SESSION["id"]." LIMIT 1");
		
			/* utworzenie zmiennych */
			$folder_upload="img/avatar";
			$plik_nazwa=$_FILES['plik']['name'];
			$plik_lokalizacja=$_FILES['plik']['tmp_name']; //tymczasowa lokalizacja pliku
			$plik_mime=$_FILES['plik']['type']; //typ MIME pliku wysłany przez przeglądarkę
			$plik_rozmiar=$_FILES['plik']['size'];
			$plik_blad=$_FILES['plik']['error']; //kod błędu
			 
			/* sprawdzenie, czy plik został wysłany */
			if (!$plik_lokalizacja) {
				exit("Nie wysłano żadnego pliku");
			}
				 
			/* sprawdzenie błędów */
			switch ($plik_blad) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_NO_FILE:
					exit("Brak pliku.");
				break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					exit("Przekroczony maksymalny rozmiar pliku.");
					break;
				default:
					exit("Nieznany błąd.");
					break;
			}
				
			//rename($plik_lokalizacja."/".$plik_nazwa, $zawartosc);
			//$plik_nazwa=$zawartosc;
			
			/* sprawdzenie rozszerzenia pliku - dzięki temu mamy pewność, że ktoś nie zapisze na serwerze pliku .php */
			$dozwolone_rozszerzenia=array("jpeg", "jpg", "tiff", "tif", "png", "gif");
			$plik_rozszerzenie=pathinfo(strtolower($plik_nazwa), PATHINFO_EXTENSION);
			if (!in_array($plik_rozszerzenie, $dozwolone_rozszerzenia, true)) {
				exit("Niedozwolone rozszerzenie pliku.");
			}

			/* przeniesienie pliku z folderu tymczasowego do właściwej lokalizacji */
			if (!move_uploaded_file($plik_lokalizacja, $folder_upload."/".$plik_nazwa)) {
				exit("Nie udało się przenieść pliku.");
			}
			 
			/* nie było błędów */
			//echo "Plik został zapisany. ";
			//echo $plik_nazwa;
			$row = mysqli_fetch_assoc($id);

			//echo $row["id"];

			/*     ---    SQL    ---      */
			$data=date("Y-m-d");
			
			//$zapytanie = "INSERT INTO meme  VALUES ('', '".$title."', '".$plik_rozszerzenie."', '".$data."', '".$kategoria."','')"; //dobre
				
			$nazwaPlikuDoZapisania=$row["id"].'.'.$plik_rozszerzenie;
				
			if($idzapytania = mysqli_query($polaczenie,"UPDATE uzytkownicy SET avatar='$nazwaPlikuDoZapisania' WHERE id=".$_SESSION["id"]))
			{
				rename("img/avatar/".$plik_nazwa, "img/avatar/".$nazwaPlikuDoZapisania);
			}
			else																		//<-------
			{
				echo "Add row: Error!";		 
			}

		}
	}
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	
	<title>Wolne Forum <?php echo $_SESSION["user"] ?></title>
	
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="description" content="Opis w Google" />
	<meta name="keywords" content="słowa, kluczowe, wypisane, po, porzecinku" />
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/brands.css" integrity="sha384-7xAnn7Zm3QC1jFjVc1A6v/toepoG3JXboQYzbM0jrPzou9OFXm/fY6Z/XiIebl/k" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">

	
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
					<div style="clear:both;"></div>';
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
					<div style="clear:both;"></div>';
			}
			
			?>
		</section>
		<div id="content">
			
			<section id="moje-konto">

				<div id="rectangle-user">

					<div id="left1">
						<?php
						$id = mysqli_query($polaczenie,"SELECT id,avatar FROM uzytkownicy WHERE id = ".$_SESSION["id"]." LIMIT 1");
						$row = mysqli_fetch_assoc($id);?>
						<img src="img/avatar/<?php echo $row["avatar"];?>" class="avatar">
					</div>
					
					<div class="nowa-kategoria padding-left">
					
						<div class="nowa-kategoria-nazwa">
									
							<form enctype="multipart/form-data" method="post">
							
								<?php  //echo $file=$_SERVER['DOCUMENT_ROOT'].'<br>';  ?>
								<label for="plik">Ustaw swój avatar</label>
								<input type="file" size="32" name="plik" id="plik" value="wyślij" required>
								<input type="submit" name="Wyślij"  >       
							
							</form>
										
						</div>
									
					</div>
					<div class="clear-both"></div>
					
					<table id="user-table-data">
						
						<tr>
						<td color="#e9c185">Nick:</td>  <td><?php echo $_SESSION["user"];?></td>  <td><a href="zmien-nick.php" class="user-link">zmień</a></td>
						</tr>
						
					</table>
						
					<div class="nowa-kategoria">
					
						<div class="nowa-kategoria-nazwa">
									
							<form method="post">
											
								<label for="username">Zmień tekst wyświetlany pod postami</label>
								
								<input type="text" id="username" name="quote" required>
												
								<div class="wysrodkowane">
									<input type="submit" value="Zmień">
								</div>
												
							</form>
										
						</div>
									
					</div>
				
				</div>
		
			</section>
			
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

			mysqli_free_result($id);

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