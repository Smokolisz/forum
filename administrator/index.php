<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	
	<title>Dodaj obrazek</title>
	
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<meta name="description" content="Najśmieszniejsze memy o nosaczu sundajskim tylko tutaj!" />
	<meta name="keywords" content="nosacz, mem, memy, śmieszne, funny" />
	
	<link rel="stylesheet" href="../style.css" type="text/css" />
	<link async rel="icon" type="image/png" href="http://teampelikan.pl/adach/img/favicon-32x32.png" sizes="32x32" />
	<link href="https://fonts.googleapis.com/css?family=Lato|Oswald" rel="stylesheet">
	
	
		
</head>
<body>

	<!--<h1> Słuchaj jeden z drugim, żeby mi tu któryś kilka razy nie klikał wyślij jakby się coś zacięło bo się zrobi syf w bazie</h1> -->
	
	<div id="admin">
	
		<form enctype="multipart/form-data" method="post" action="upload.php">
			<?php  //echo $file=$_SERVER['DOCUMENT_ROOT'].'<br>';  ?>
			<div style="font-size:26px;">Podaj komentarz do obrazka:</div> <br>  
			<input type="text" name="title" class="input" style="float:left;" required />  <div style="font-size:50px;float:left;margin-left:195px;">&larr; 1</div>
			<div style="clear:both;"></div>
			<br>
			<br>
			<br>
			<br>
			<br>
			<br>
			
			Wybierz kategorie:<br>
			<select name="kategoria"  style="float:left;width:130px;height:30px;"> <!--disabled-->  
			
			  <option value="1">Nosacz</option>
			  <option value="2">games</option>
			  <option value="3">misc</option>
			</select> 
			<div style="font-size:50px;float:left;margin-left:326px;">&larr; 2</div>
			<div style="clear:both;"></div>
			<br>
			<br>
			<br>
			<br>
			<br>
			
			
			<input type="file" size="32" name="plik" value="wyślij" style="float:left;"><div style="font-size:50px;float:left;margin-left:205px;">&larr; 3</div>
			<div style="clear:both;"></div>
			<input type="submit" name="Wyślij"  style="width:200px;">       
			
		
		</form>


	<br><br><br>

		<h2>
		
		Instrukcja:
		
		</h2>
		<br>
		1. Nadaj tytuł obrazkowi<br>
		2. wybierz dział w którym ma się znaleźć<br>
		3. Wybierz obrazek i kliknij prześlij<br>
		4. Na następnej stronie kliknij Gotowe - przeniesie cie to na główną, a mem zostanie dodany<br>


	</div>


</body>
</html>