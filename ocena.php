<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		//SQL 
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
		
		$idUsera = $_SESSION["id"]; //id użytkownika który ocenił
		$idPosta = $_POST["id"];  //id posta
		$ocena = $_POST["ocena"]; //POZYTYWNA czy NEGATYWNA?
		
		$id = mysqli_query($polaczenie, "SELECT id FROM oceny WHERE idUsera='$idUsera' AND idPosta='$idPosta'");
		$ile = mysqli_num_rows($id);
		
		if($ile < 1)
		{
			$id = mysqli_query($polaczenie, "SELECT idUsera FROM posty WHERE id='$idPosta' LIMIT 1");
			$row = mysqli_fetch_assoc($id);
			
			$idUseraDoOceny = $row["idUsera"];

			if ($polaczenie->query("INSERT INTO oceny VALUES (NULL, '$idUsera', '$idPosta', '$ocena')"))
			{
				if($ocena==1)
				{
					$polaczenie->query("UPDATE uzytkownicy set punkty=punkty+13 WHERE id='$idUseraDoOceny'");
				}
				else
				{
					$polaczenie->query("UPDATE uzytkownicy set punkty=punkty-2 WHERE id='$idUseraDoOceny'");
				}
				
				echo "OK";
				
			} else {
			
			echo "Nie udało się ocenić (może jesteś niezalogowany?)! Skontaktuj się z webmasterem!";
			printf("Errormessage: %s\n", $polaczenie->error);
			
			}
			
			mysqli_close($polaczenie);
		}
		else
		{
			echo "Już oceniłeś ten post!";
		}
		
	}
	else
	{
		echo "Musisz się zalgować!";
	}

?>