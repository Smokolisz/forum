<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: index.php');
		exit();
	}
	
	if(isset($_SESSION['liczba-bledow'])&&($_SESSION['liczba-bledow']>=5))
	{
		$captcha=true;
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
		
			<a href="rejestracja.php"><nav class="navigation-item">
				zarejestruj
			</nav></a>
			<a href="login.php"><nav class="navigation-item">
				zaloguj
			</nav></a>
			<a href="index.php"><nav class="navigation-item">
				Strona Główna
			</nav></a>
			<div style="clear:both;"></div>
		
		</section>
		<div id="content">
			
			<section id="left-category">
			
				<header id="section">
					<h2 class="popularne-nowe-h2">Zaloguj sie</h2>
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
						<input type="text" id="username" name="login" value="<?php
							if (isset($_SESSION['fr_nick']))
							{
								echo $_SESSION['fr_nick'];
								unset($_SESSION['fr_nick']);
							}
						?>"required>
						<label for="password">Hasło:</label>
						<input type="password" id="password" name="haslo" required>
						
						<?php
						
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
							
						?>
						
						<div id="lower">
							<input type="checkbox" ><label class="check" for="checkbox" >Zapamiętaj mnie!</label>
							<input type="submit" value="Login">
						</div>
					</form>
				</div>
				<br>
				
				
				<div class="utworz-konto">
					<br><br>
					<a href="rejestracja.php">
						Nie masz konta? <strong>Zarejestruj sie!</strong>
					</a>
				</div>
		
			</section>
			<section id="right-panel">
			
				<section id="trending">
				
					<header id="popular">
						<h2 class="popularne-nowe-h2">Popularne</h2>
					</header>
					<div class="clear-both"></div>
					
					<?php
					
						$top5=1;
						
						$id2 = mysqli_query($polaczenie,"SELECT * FROM kategorie");
						
						if($id = mysqli_query($polaczenie,"SELECT * FROM tematy WHERE widoczny = true ORDER BY wyswietlenia DESC"))
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
					?>
				
				
				</section>
				<section id="fresh">
				
					<header id="popular">
						<h2 class="popularne-nowe-h2">Nowe</h2>
					</header>
					<div class="clear-both"></div>
					
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
													<div class="clear-both"></div>
												
												</div>
												<div class="stats-odpowiedzi">
												
													<div class="info-left"><img src="img/odpowiedzi.png" class="info-images"></div>
													<div class="info-elements-odpowiedzi">'.$row_cnt.'</div>
													<div class="clear-both"></div>
												
												</div>
												<div class="stats-kategoria">
												
													<div class="info-left"><img src="img/kategoria.png" class="info-images"></div>
													<div class="info-elements-kategoria">';
													
													
													$id2 = mysqli_query($polaczenie,"SELECT nazwa FROM kategorie WHERE id = ".$row["kategoria"]);
													$row2 = mysqli_fetch_assoc($id2);
													
												echo	$row2["nazwa"].'</div>
													<div class="clear-both"></div>
												
												</div>';
												if($row["przypiety"]==true)
												{
													echo '<img src="img/pin.png" width="65%" class="pinned">';
												}
								echo			'<div class="clear-both"></div>
												
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
			<div class="clear-both"></div>
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
						
							<dt style="float:left;">Kontakt:</dt>
							<dd style="float:left;margin:0px 0px 0px 10px;"> <a href="mailto:smokoliszone@gmail.com">smokoliszone@gmail.com</a></dd>
						
						</dl>
						
					</div>
					
				    <div style="clear:both;"></div>
					
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