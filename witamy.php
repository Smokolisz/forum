<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
	}
	else $zalogowany=false;
	
	if (!isset($_SESSION['udanarejestracja']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanarejestracja']);
	}
	
	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['fr_nick'])) unset($_SESSION['fr_nick']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if (isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	if (isset($_SESSION['fr_regulamin'])) unset($_SESSION['fr_regulamin']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_nick'])) unset($_SESSION['e_nick']);
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if (isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
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
			
			<div style="width:100%">
				<header id="powitanie">
						<h1 class="greetings1">Dziękujemy za rejestracje!</h1>
				</header>
				<aside class="lighter-line">
					
						<h2 class="greetings2">Na twój adres email został wysłany link weryfikacyjny<br>Teraz możesz zalogować się do serwisu :)</h2>
					
				</aside>
			</div>
			
			<section id="left-category">
			
				<header id="section">
					<h1 style="margin:0px 0px 0px 5px;line-height:20px;">Zaloguj sie</h1>
				</header>

				<?php
						
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
				?>

				<div id="panel">
					<form action="zaloguj.php"  method="post">
						<label for="username">Nazwa użytkownika:</label>
						<input type="text" id="username" name="login" required>
						<label for="password">Hasło:</label>
						<input type="password" id="password" name="haslo" required>
						<div id="lower">
							<input type="checkbox" ><label class="check" for="checkbox" >Zapamiętaj mnie!</label>
							<input type="submit" value="Login">
						</div>
					</form>
				</div>
				<br>
				<?php
					if(isset($_SESSION['blad']))	echo $_SESSION['blad'];
				?>
		
			</section>
			<section id="right-panel">
			
				<section id="trending">
				
					<header id="popular">
						<h2 style="float:right;margin:0px 5px 0px 0px;line-height:20px;">Popularne</h2>
					</header>
					<div style="clear:both;"></div>
					
					<?php
					
						$top5=1;
						
						$id2 = mysqli_query($polaczenie,"SELECT * FROM kategorie");
						
						if($id = mysqli_query($polaczenie,"SELECT * FROM tematy WHERE widoczny = true ORDER BY wyswietlenia DESC"))
						{
							while ($row = mysqli_fetch_assoc($id)) {
								
								echo	'<a href="temat.php?id='.$row["id"].'"><article class="rectangle-popular">	
										<h2 class="index-right-small-font">'.$row["tytul"].'</h2>
										
										<div class="bottom-rectangle">
										
											<div class="info">
											
												<div class="stats">
												
													<div class="info-left"><img src="img/wyswietlenia.png" class="info-images"></div>
													<div class="info-elements">'.$row["wyswietlenia"].'</div>
													<div style="clear:both;"></div>
												
												</div>
												<div class="stats-odpowiedzi">
												
													<div class="info-left"><img src="img/odpowiedzi.png" class="info-images"></div>
													<div class="info-elements-odpowiedzi">'.$row["ilosc-odpowiedzi"].'</div>
													<div style="clear:both;"></div>
												
												</div>
												<div class="stats-kategoria">
												
													<div class="info-left"><img src="img/kategoria.png" class="info-images"></div>
													<div class="info-elements-kategoria">';
													
													
													$id2 = mysqli_query($polaczenie,"SELECT nazwa FROM kategorie WHERE id = ".$row["kategoria"]);
													$row2 = mysqli_fetch_assoc($id2);
													
												echo	$row2["nazwa"].'</div>
													<div style="clear:both;"></div>
												
												</div>';
												if($row["przypiety"]==true)
												{
													echo '<img src="img/pin.png" width="65%" class="pinned">';
												}
								echo			'<div style="clear:both;"></div>
												
											</div>
											
											
										</div>
										</article></a>';
								
								
								if($top5 >= 5)
								{
									break;
								}
								else
								{
									$top5++;
								}
							}
						}
					?>
				
				
				</section>
				<section id="fresh">
				
					<header id="popular">
						<h2 style="float:right;margin:0px 5px 0px 0px;line-height:20px;">Nowe</h2>
					</header>
					<div style="clear:both;"></div>
					
					<?php
					
						$top5=1;
						
						if($id = mysqli_query($polaczenie,"SELECT * FROM tematy WHERE widoczny = true ORDER BY id DESC"))
						{
							
							
							while ($row = mysqli_fetch_assoc($id)) {
								
								$id3 = mysqli_query($polaczenie,"SELECT id FROM posty WHERE idTematu = ".$row["id"]." ORDER BY id DESC");
								
								 /* zwraca ilość rekordów w zapytaniu */
								$row_cnt = $id3->num_rows;
								$row_cnt--;
								
								echo	'<a href="temat.php?id='.$row["id"].'"><article class="rectangle-popular">	
										<h2 class="index-right-small-font">'.$row["tytul"].'</h2>
										
										<div class="bottom-rectangle">
										
											<div class="info">
											
												<div class="stats">
												
													<div class="info-left"><img src="img/wyswietlenia.png" class="info-images"></div>
													<div class="info-elements">'.$row["wyswietlenia"].'</div>
													<div style="clear:both;"></div>
												
												</div>
												<div class="stats-odpowiedzi">
												
													<div class="info-left"><img src="img/odpowiedzi.png" class="info-images"></div>
													<div class="info-elements-odpowiedzi">'.$row_cnt.'</div>
													<div style="clear:both;"></div>
												
												</div>
												<div class="stats-kategoria">
												
													<div class="info-left"><img src="img/kategoria.png" class="info-images"></div>
													<div class="info-elements-kategoria">';
													
													
													$id2 = mysqli_query($polaczenie,"SELECT nazwa FROM kategorie WHERE id = ".$row["kategoria"]);
													$row2 = mysqli_fetch_assoc($id2);
													
												echo	$row2["nazwa"].'</div>
													<div style="clear:both;"></div>
												
												</div>';
												if($row["przypiety"]==true)
												{
													echo '<img src="img/pin.png" width="65%" class="pinned">';
												}
								echo			'<div style="clear:both;"></div>
												
											</div>
											
											
										</div>
										</article></a>';
								
								
								if($top5 >= 5)
								{
									break;
								}
								else
								{
									$top5++;
								}
							}
						}
						else
						{
							echo $polaczenie->connect_error;
						}
					?>
				
				
				</section>
				
			</section>
			<div style="clear:both;"></div>
			
			<div style="width:100%;height:200px;"></div>
			
			<section style="width:100%; text-align:center;">
				<div class="darker-line">
				
					<h2>Odkryj możliwości naszego forum</h2>
					
				</div>
				<div class="lighter-line">
				
					<div id="lista-odkryj">
					
						<ul>
						  <li><h4 class="h-b">Badź aktywnym użytkonikiem i zbieraj punkty</h4></li>
						  <li><h4 class="h-b">Zdobywaj osiągnięcia, które zobaczą inni!</h4></li>
						  <li><h4 class="h-b">Najlepsi otrzymają dodatkowe uprawnienia moderatora</h4></li>
						  <li><h4 class="h-b">Ale przede wszystkim: staraj się dbać o przyjazną atmosfere na forum!</h4></li>
						</ul>
						
					</div>
					
				</div>
			</section>
			
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
			
		</div>
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