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
	
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
		
		$id2 = mysqli_query($polaczenie,"SELECT permissions FROM uzytkownicy WHERE id=".$_SESSION["id"]." LIMIT 1");
		$row=mysqli_fetch_assoc($id2);
		$_SESSION['upowaznienie'] = $row['permissions'];
	}
	else $zalogowany=false;
	
	unset($_SESSION['blad']);

	if (isset($_POST['nazwa-dzialu'])&&($zalogowany==true))
	{
		if(isset($_POST['przypiety']))
		{
			$przypiety=1;
		}
		else $przypiety=0;
		
		$nazwaDzialu = $_POST['nazwa-dzialu'];
		echo $nazwaDzialu;
		
		if ($polaczenie->query("INSERT INTO kategorie VALUES (NULL, '$nazwaDzialu', NULL, 0, '$przypiety', 1)"))
		{
			header('Location: index.php');
			
		} else {
			
			echo "Nie udało się dodać nowego działu! Skontaktuj się z webmasterem!";
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
	<script type="text/javascript" async src="wodotryski.js"></script>
	
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script>
	
		$(document).ready(function() {
			$("#send").click(function() {
				$("#clicked").load("email.php");
			});
		});
	</script>
	
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
		<?php 
		if (($zalogowany)&&($_SESSION["upowaznienie"]==1))
		{
			echo	'<div id="confirmation">
						<h4 class="no-margin-padding">Aby móc w pełni korzystać z forum potwierdź adres email!</h4><strong><button id="send">Wyślij</button></strong><div id="clicked"></div>
					<div>';
			
			//require_once 'email.php';
		}
		?>
		
		<div id="content">
			
			<section id="left-category">
			 <!--onmouseover="paint()"-->
				<header id="section">
					<h2 style="margin:0px 0px 0px 5px;line-height:20px;">Kategorie</h2>
				</header>
				
		
				<?php
					//PRZYPIETE TEMATY
					if($id = mysqli_query($polaczenie,"SELECT * FROM kategorie WHERE widoczny = true AND przypiety = true ORDER BY id DESC"))
					{
						while ($row = mysqli_fetch_assoc($id)) {
							
							$id3 = mysqli_query($polaczenie,"SELECT id FROM tematy WHERE kategoria = ".$row["id"]." ORDER BY id DESC");	
							 /* zwraca ilość rekordów w zapytaniu */
							$row_cnt = $id3->num_rows;
							
							echo  '<div class="rectangle-margin"><a href="kategorie.php?id='.$row["id"].'"><article class="rectangle">';
							echo  '<div class="przypiety">';

							echo '<img src="img/pin.png" width="65%" style="margin-top:25px;">';
							
							echo  '</div><h2 class="h2-font-size float-left">'.$row["nazwa"].'  |  '.$row_cnt.'</h2>';
							echo  '<div class="clear-both"></div>';
							
							echo  '</article></a></div>';
								
						}
					}
					//ZWYKŁE TEMATY
					if($id = mysqli_query($polaczenie,"SELECT * FROM kategorie WHERE widoczny = true AND przypiety = false ORDER BY id DESC"))
					{
						while ($row = mysqli_fetch_assoc($id)) {
							
							$id3 = mysqli_query($polaczenie,"SELECT id FROM tematy WHERE kategoria = ".$row["id"]." ORDER BY id DESC");		
							 /* zwraca ilość rekordów w zapytaniu */
							$row_cnt = $id3->num_rows;
							
							echo  '<div class="rectangle-margin"><a href="kategorie.php?id='.$row["id"].'"><article class="rectangle">';
							echo  '<div class="przypiety">';
							
							echo  '</div><h2 class="h2-font-size float-left">'.$row["nazwa"].'  |  '.$row_cnt.'</h2>';
							echo  '<div class="clear-both"></div>';
							
							echo  '</article></a></div>';
								
						}
					}
					
					//For admins only
					if (($zalogowany)&&($_SESSION['upowaznienie']>2))
					{
						echo	'<div id="nowa-kat">
									<div class="nowa-kategoria">
										<div class="nowa-kategoria-nazwa">
										<div style="margin-left:10px;">
											<h2>Dodaj kategorie</h2>
										</div>
											<form method="post">

												<input type="text" id="username" name="nazwa-dzialu" required>
												
												<div class="bottom-send">
												<input type="checkbox" name="przypiety"><label class="check" for="checkbox" >Przypięty</label>
												<input type="submit" value="Dodaj">
												</div>
												
											</form>
										</div>
									</div>
								</div>';
								//<label for="username">Nazwa działu</label>
					}

				?>
				
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
						
							<dt class="float-left">Kontakt:</dt>
							<dd style="float:left;margin:0px 0px 0px 10px;"> <a href="mailto:smokoliszone@gmail.com">smokoliszone@gmail.com</a></dd>
						
						</dl>
						
					</div>
					
				    <div class="clear-both"></div>
					
				</div>
			
			</footer>
			
			<?php	
			

		
			mysqli_free_result($id);
			mysqli_free_result($id2);

			mysqli_close($polaczenie);

//Wejścia
/*$plik = fopen('odslony.txt','r');

$zawartosc = fread($plik, 8192);

$zawartosc++;
fclose($plik); 

$file = "odslony.txt"; 

$fp = fopen($file, "w"); 

flock($fp, 2); 

fwrite($fp, $zawartosc);

flock($fp, 3); 

fclose($fp); */

//adresy ip i data
/*$data=date("Y-m-d");
$czas=date("H:i");

$ip = $_SERVER['REMOTE_ADDR'];
$file = "adresy.txt"; 
$fp = fopen($file, "a");
flock($fp, 2);

fwrite($fp, $ip);
fwrite($fp, '    '.$data.'    '.$czas);
fwrite($fp, "\r\n\r\n");

flock($fp, 3); 

fclose($fp); */
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
			var html_code = '<div id="cookies-message" class="cookies">Ta strona używa ciasteczek (cookies), dzięki którym nasz serwis może działać lepiej. <a href="http://wszystkoociasteczkach.pl" target="_blank">Dowiedz się więcej</a><a href="javascript:WHCloseCookiesWindow();" id="accept-cookies-checkbox" name="accept-cookies" class="cookies-button">Rozumiem</a></div>';
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