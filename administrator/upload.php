<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	
	<title>Memy nosacz sundajski</title>
	
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="description" content="Najśmieszniejsze memy o nosaczu sundajskim tylko tutaj!" />
	<meta name="keywords" content="nosacz, mem, memy, śmieszne, funny" />
	
	<link rel="stylesheet" href="style.css" type="text/css" />
	<link async rel="icon" type="image/png" href="http://teampelikan.pl/adach/img/favicon-32x32.png" sizes="32x32" />

		
</head>
<body>

			<?php
			$title = $_POST['title'];
			$kategoria = $_POST['kategoria']; 
			
			
			/* SQL */
			ini_set("display_errors", 1);
			require_once 'dbconnect.php';
			$polaczenie = mysqli_connect($host, $user, $password);
			mysqli_query($polaczenie, "SET CHARSET utf8");
			mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
			mysqli_select_db($polaczenie, $database);
			
			if (mysqli_connect_errno())
			{
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
			
			
			$rezultat = mysqli_query($polaczenie,"SELECT id FROM meme ");
			
			/* ------------------------- */
			
			/* utworzenie zmiennych */
			$folder_upload="../img-meme/";
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
			echo "Plik został zapisany.";
			
			echo $plik_nazwa;
		
			$row;
			while ($row = mysqli_fetch_assoc($rezultat)) {
				$name_meme = $row["id"];
			}
			
			
			
			if($name_meme == 0)
			{
				$name_meme=1;
			}
			else
			{
				$name_meme++;
			}

			echo $name_meme;
			
			
			   
			
			/*     ---    SQL    ---      */
			
			

			$data=date("Y-m-d");				
			$add_row;
			//$zapytanie = "INSERT INTO meme  VALUES ('', '".$title."', '".$plik_rozszerzenie."', '".$data."', '".$kategoria."','')"; //dobre
			$zapytanie = "INSERT INTO meme  VALUES ('' ,'".$title."', '".$plik_rozszerzenie."', '".$data."', '".$kategoria."')";
			//$zapytanie = "INSERT INTO meme  VALUES ('', '".$name."' ,'".$title."', '".$plik_rozszerzenie."', '".$data."', '".$kategoria."','')";
			
			if($idzapytania = mysqli_query($polaczenie,$zapytanie))
			{
				$add_row = "Add row: Succes!";
				$rezultat = mysqli_query($polaczenie,"SELECT id FROM meme ");
				while ($row = mysqli_fetch_assoc($rezultat)) {
				$name_meme = $row["id"];
				}
				rename("../img-meme/".$plik_nazwa, "../img-meme/".$name_meme.".".$plik_rozszerzenie);
			
			}
			else																		//<-------
			{
				$add_row = "Add row: Error!";
				 
			}
			
			echo '<br>'.$add_row;
			
			mysqli_close($polaczenie);
			
			?>
			
			
			
			
			<a href="http://nosacz.pl/index.php">Gotowe</a>



</body>
</html>