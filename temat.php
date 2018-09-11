<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		$zalogowany=true;
	}
	else { $zalogowany=false; }
	
	$idTematu = htmlspecialchars($_GET["id"]);
	
	if(!$idTematu)
	{
		header('Location: kategorie.php');
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
	
	if (!$polaczenie->query("UPDATE tematy SET wyswietlenia=wyswietlenia+1 WHERE id='$idTematu'"))
	{
		throw new Exception($polaczenie->error);
		//header('Location: kategorie.php');
		exit();
	}
	
	if (isset($_POST['tresc'])&&($zalogowany==true))
	{
		//Bot or not? Oto jest pytanie!
		$sekret = "6LdruGEUAAAAAMNsq466jHmY6OQVfEkdBmQ87R5O";
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
			
		$odpowiedz = json_decode($sprawdz);
			
		if($odpowiedz->success==true)
		{
			$tresc = $_POST['tresc'];
			$user = $_SESSION["user"];
			$idUsera = $_SESSION['id'];
			
			if ($polaczenie->query("INSERT INTO posty VALUES (NULL, '$idTematu', '$idUsera', 0, '','$tresc', '', 0, 0, NULL, '$user', 1)"))
			{	
				if (!$polaczenie->query("UPDATE uzytkownicy SET punkty=punkty+25 WHERE id='$idUsera'"))
				{
					echo "error";
				}
				if (isset($_FILES['plik']))
				{
					if(is_uploaded_file($_FILES['plik']['tmp_name']))
					{
						$id = mysqli_query($polaczenie,"SELECT id FROM posty ORDER BY id DESC LIMIT 1");
					
						/* utworzenie zmiennych */
						$folder_upload="img/zalaczniki";
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
						echo "Plik został zapisany. ";
							
						echo $plik_nazwa;

						$row = mysqli_fetch_assoc($id);

						echo $row["id"];

						/*     ---    SQL    ---      */
						$nazwaPlikuDoZapisania=$row["id"].'.'.$plik_rozszerzenie;
							
						if($idzapytania = mysqli_query($polaczenie,"UPDATE posty SET zalacznik='$nazwaPlikuDoZapisania' WHERE id=".$row["id"]))
						{
							rename("img/zalaczniki/".$plik_nazwa, "img/zalaczniki/".$nazwaPlikuDoZapisania);
							header('refresh: 0;');
							exit();
						}
					}
				}	
		
			} else {
				echo "Nie udało się dodać twojej odpowiedzi! Skontaktuj się z webmasterem!";
				printf("Errormessage: %s\n", $polaczenie->error);
			}
		}
		else if($odpowiedz->success==true)
		{
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}
	}


	if($id = mysqli_query($polaczenie,"SELECT * FROM posty WHERE idTematu='$idTematu' AND widoczny=1 AND rozpoczynajacy=1 LIMIT 1"))
	{
		while ($row = mysqli_fetch_assoc($id)) {

			$dataDodania = $row["dataDodaniaPostu"];
			$temat = $row["temat"];
			$tresc = $row["tresc"];
			$idUsera = $row["idUsera"];
			$idPosta = $row["id"];
			
			$id3 = mysqli_query($polaczenie,"SELECT id FROM oceny WHERE idPosta=".$row["id"]." AND ocena=1");
			$plusy = mysqli_num_rows($id3);
							
			$id3 = mysqli_query($polaczenie,"SELECT id FROM oceny WHERE idPosta=".$row["id"]." AND ocena=0");
			$minusy = mysqli_num_rows($id3);
		}
						
		if($id2 = mysqli_query($polaczenie,"SELECT id,user,punkty,dataDolaczenia,ostatnioWidziany,quote,avatar FROM uzytkownicy WHERE permissions>0 AND id='$idUsera' LIMIT 1"))
		{
			while ($row = mysqli_fetch_assoc($id2)) {
								
				$id = $row["id"];
				$autor = $row["user"];
				$punkty = $row["punkty"];
				$dataDolaczenia = $row["dataDolaczenia"];
				$ostatnioWidziany = $row["ostatnioWidziany"];
				$quote = $row["quote"];
				$avatar = $row["avatar"];
			}
		}
		else
		{
			echo "Wystąpił błąd spróbuj ponownie";
			printf("Errormessage: %s\n", $polaczenie->error);
		}
	} else {
		
	echo "Wystąpił błąd spróbuj ponownie";
	printf("Errormessage: %s\n", $polaczenie->error);
	}
	
	if(empty ($autor))
	{
		header('Location:index.php');
		exit();
	}


?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	
	<title><?php echo $temat  ?> - Wolne Forum</title>
	
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="description" content="<?php echo $tresc  ?>" />
	<meta name="keywords" content="słowa, kluczowe, wypisane, po, porzecinku" />

	<link rel="stylesheet" href="style.css" type="text/css" />
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/brands.css" integrity="sha384-7xAnn7Zm3QC1jFjVc1A6v/toepoG3JXboQYzbM0jrPzou9OFXm/fY6Z/XiIebl/k" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/fontawesome.css" integrity="sha384-ozJwkrqb90Oa3ZNb+yKFW2lToAWYdTiF1vt8JiH5ptTGHTGcN7qdoR1F95e0kYyG" crossorigin="anonymous">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
	<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
	<script>
	
		function ocena(id,ocena)
		{
			$("#nav").load("ocena.php", {
				id:     id,
				ocena:  ocena
			});
		}
			
		/*$(document).ready(function() {
			$(".thumbup").click(function() {
				
			});
			
			$(".thumbdown").click(function() {
				$("#nav").load("hello.php");
			});
		});*/
		
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
					<div class="clear-both"></div>';
			}

			$data=date("Y-m-d");
			$czas=date("H:i");

			$roznica = (strtotime($data.$czas) - strtotime($dataDodania));
			$min = floor($roznica / 60);
			$sec = $roznica-($min*60);
			$h = floor($min/60);
			$min = $min-($h*60);
			$dni = floor($h/24);
			//echo "Różnica między datami to: ".abs($dni)." dni ".abs($h)." godzin ".abs($min)." minut ".abs($sec)." sekund";
			
			?>
		</section>
		<div id="content">
			
			<section id="discussion-panel">
				
				
				<div class="bottom-line">
				
					<div class="comment-panel"> 
					
						<div class="top-line">
				
							<div class="right-top-date">
								Dodano 
								<?php
								
									if(abs($dni)>0) { echo abs($dni)." dni temu"; }
									else if(abs($h)>0) { echo abs($h)." godzin temu"; }
									else if(abs($min)>0) { echo abs($min)." minut temu"; }
									else if(abs($sec)>0) { echo abs($sec)." sekund temu"; }

								?>

							</div>	<!-- DODANO -->
							<div class="clear-both"></div>
				
						</div>
						<div class="left-info">
						
							<div class="low-res-left">
							
								<div class="avatar-user">
									<img src="img/avatar/<?php echo $avatar; ?>" class="avatar-size" async >			<!-- AVATAR -->
								</div>
								<div class="user-nick">
										<?php echo $autor;    ?><br>													<!-- NICK -->
										<p class="ranga">
										<?php 
										
										if($punkty<200) echo "Nowy";
										else if($punkty<400) echo "Pierwsze kroki";
										else if($punkty<800) echo "Członek";
										else if($punkty<1500) echo "Zasłużony";
										else if($punkty<3000) echo "Elita";
										?>
										</p>
								</div>
								
							</div>
							
							<div class="low-res-right">
							
								<div class="bottom-info">  

										<span> <b>Data rejsestracji:</b><br> <?php echo$dataDolaczenia;?> </span><br>  			<!-- DATA REJESTRACJI -->

										<span> <b>Ostatnio widziany:</b><br> <?php echo $ostatnioWidziany;?> </span><br>    	<!-- OSTATNIO WIDZIANY -->
									
										<span> <b>Punkty:</b><br> <?php echo $punkty;?> </span><br>								<!-- PUNKTY -->
										
										<span> <i class="fas fa-thumbs-up thumbup" id="a1" onclick="ocena(<?php echo $idPosta.","."1"; ?>)"></i> <?php echo $plusy;?> &nbsp;&nbsp;</span>
										<span><i class="fas fa-thumbs-down thumbdown" onclick="ocena(<?php echo $idPosta.","."0"; ?>)"></i>  <?php echo $minusy;?> </span>
									
									
										
								</div>
								
							</div>
							<div class="clear-both"></div>
			
						</div>
						<div class="right-info">
						
							<div class="top-rectangle">
				
								<article>

									<h1> <?php echo $temat;?> </h1>						<!-- TEMAT -->

									<p> <?php echo $tresc;?> </p>					<!-- TREŚĆ -->

								</article>

							</div>
							
							<div class="bottom-quote">
							
								<p> <?php echo $quote;?> </p> 					<!-- INFO-O-UŻYTKOWNIKU -->
								
							</div>
						</div>
						<div class="clear-both"></div>
			
					</div>
				
				</div>
				
				<?php
					
					if($id = mysqli_query($polaczenie,"SELECT * FROM posty WHERE idTematu=".$idTematu." AND widoczny=true AND rozpoczynajacy=false ORDER BY id"))
					{
						while ($row = mysqli_fetch_assoc($id)) {
							
							$id2 = mysqli_query($polaczenie,"SELECT id,user,punkty,dataDolaczenia,ostatnioWidziany,quote,avatar FROM uzytkownicy WHERE id=".$row["idUsera"]." AND permissions>0 LIMIT 1");
							$row2 = mysqli_fetch_assoc($id2);
							
							$id3 = mysqli_query($polaczenie,"SELECT id FROM oceny WHERE idPosta=".$row["id"]." AND ocena=1");
							$plusy = mysqli_num_rows($id3);
							
							$id3 = mysqli_query($polaczenie,"SELECT id FROM oceny WHERE idPosta=".$row["id"]." AND ocena=0");
							$minusy = mysqli_num_rows($id3);
							
							$roznica = (strtotime($data.$czas) - strtotime($row["dataDodaniaPostu"]));
							$min = floor($roznica / 60);
							$sec = $roznica-($min*60);
							$h = floor($min/60);
							$min = $min-($h*60);
							$dni = floor($h/24);
							
							echo   '<div class="comment-panel"> 
				
									<div class="top-line">
							
										<div class="right-top-date">
											Dodano '; 
											
												if(abs($dni)>0) { echo abs($dni)." dni temu"; }
												else if(abs($h)>0) { echo abs($h)." godzin temu"; }
												else if(abs($min)>0) { echo abs($min)." minut temu"; }
												else if(abs($sec)>0) { echo abs($sec)." sekund temu"; }


							echo		'</div>	
										<div class="clear-both"></div>
							
									</div>
									<div class="left-info">
									
										<div class="low-res-left">
											<div class="avatar-user">
												<img src="img/avatar/'.$row2["avatar"].'" class="avatar-size" async>
											</div>
											<div class="user-nick">
													 '.$row2["user"].'<br>	
												<p class="ranga">';

												if($punkty<200) echo "Nowy";
												else if($punkty<400) echo "Pierwsze kroki";
												else if($punkty<800) echo "Członek";
												else if($punkty<1500) echo "Zasłużony";
												else if($punkty<3000) echo "Elita";
												
							echo				'</p>	
												
											</div>
										</div>
										
										<div class="low-res-right">
											<div class="bottom-info">  

													<span> <b>Data rejsestracji:</b><br> '.$row2["dataDolaczenia"].' </span><br>  		

													<span> <b>Ostatnio widziany:</b><br> '.$row2["ostatnioWidziany"].' </span><br>    	
												
													<span> <b>Punkty:</b><br> '.$row2["punkty"].' </span><br>    

													<span> <i class="fas fa-thumbs-up thumbup" onclick="ocena('.$row["id"].","."1".')"></i> '.$plusy.' </span>
													<span> <i class="fas fa-thumbs-down thumbdown" onclick="ocena('.$row["id"].","."0".')"></i> '.$minusy.' </span>
																						
													
												
											</div>
											
										</div>
										<div class="clear-both"></div>
										
									</div>
									<div class="right-info">
									
										<div class="top-rectangle">
							
											<article>

												<p> '.$row["tresc"].' </p><br>';
												
												if(!empty($row["zalacznik"]))
												{
													echo '<img src="img/zalaczniki/'.$row["zalacznik"].'" class="attachment" >';
												}
													
							echo			'</article>

										</div>
										
										<div class="bottom-quote">
										
											<p> '.$row2["quote"].' </p> 					
											
										</div>
									</div>
									<div class="clear-both"></div>
						
								</div>';
								
								
						}
						
					}
					else
					{
						echo "Wystąpił błąd spróbuj ponownie<br>";
						printf("Errormessage: %s\n", $polaczenie->error);
					}

				?>

			</section>
			
				<?php 
				
					if(($zalogowany)&&($_SESSION['upowaznienie']>1))
					{
						echo   '<section id="nowa-odpowiedz">
						
								<div class="nowa-kategoria wysrodkowane text-align">
									
									<div class="odpowiedz">
												
										<form enctype="multipart/form-data" method="post">
														
											<textarea type="text" class="textarea" id="username" name="tresc" placeholder="Treść twojej odpowiedzi" required></textarea>
											
											<label for="plik">Załącznik</label>
											<input type="file" size="32" name="plik" id="plik" value="Przeglądaj">
																
											<div class="g-recaptcha" style="margin: 15px 0px 5px 0px;" data-sitekey="6LdruGEUAAAAAIfFO4YXwadZudM_euD-FgevtgHc" id="captcha"></div>';

											if (isset($_SESSION['e_bot']))
											{
												echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
												unset($_SESSION['e_bot']);
											}
												
						echo					'<div class="bottom-send-right"><input type="submit" value="Odpowiedz"></div>
												<div class="clear-both">
																	
											</div>
															
										</form>
														
									</div>
													
								</div>
												
							</section>';
					}
					else if (($zalogowany)&&($_SESSION['upowaznienie']==1))
					{
						echo '<br><br><div id="nowy-temat"><div id="napisz-nowy-temat"><a href="login.php"><h4 class="no-margin-padding">Aby móc w pełni korzystać z forum potwierdź adres email!</h4></a></div></div>';
					}
					else
					{
						echo '<br><br><div id="nowy-temat"><div id="napisz-nowy-temat"><a href="login.php"><p><strong>Zaloguj sie</strong> aby móc komentować</p></a><a href="rejestracja.php"><p><strong>Zarejestruj sie</strong> jeżeli nie masz konta</p></a></div></div>';
					}
				
				?>
				
			<div class="clear-both"></div>
			<section id="bottom-links">
			
				<article>
				<h2 class="text-align">Może cię zainteresować</h2>
			
				<div class="temat-propozycje">
					
					<div id="propozycje-margines-lewy">
					
						<h3 class="h-c">Popularne</h3>
						
						<div class="rec-mar">
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
									
									echo	'<a href="temat.php?id='.$row["id"].'"><article class="rectangle-more-left">	
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
						</div>
						
					</div>
					
				</div>
				<div class="temat-propozycje">
				
					<div id="propozycje-margines-prawy">
				
						<h3 class="h-c">Nowe</h3>
						<div class="rec-mar">
						
						<?php
						$top5=1;
						
						if($id = mysqli_query($polaczenie,"SELECT * FROM tematy WHERE widoczny = true ORDER BY id DESC"))
						{
							
							
							while ($row = mysqli_fetch_assoc($id)) {
								
								$id3 = mysqli_query($polaczenie,"SELECT id FROM posty WHERE idTematu = ".$row["id"]." ORDER BY id DESC");
								
								 /* zwraca ilość rekordów w zapytaniu */
								$row_cnt = $id3->num_rows;
								$row_cnt--;
								
								echo	'<a href="temat.php?id='.$row["id"].'"><article class="rectangle-more-right">	
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
						</div>
						
					</div>
					
				</div>
				<div class="clear-both;"></div>
				
				</article>
				
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