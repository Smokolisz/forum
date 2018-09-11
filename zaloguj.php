<?php

	session_start();
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true)||(!isset($_POST['login'])) || (!isset($_POST['haslo'])))
	{
		header('Location: login.php');
		exit();
	}
	
	ini_set("display_errors", 0);
	require_once 'administrator/dbconnect.php';
	mysqli_report(MYSQLI_REPORT_STRICT);
	
	/*$polaczenie = mysqli_connect($host, $user, $password);
	mysqli_query($polaczenie, "SET CHARSET utf8");
	mysqli_query($polaczenie, "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'");
	mysqli_select_db($polaczenie, $database);*/
	
	
	//Bot or not? Oto jest pytanie!
	$sekret = "6LdruGEUAAAAAMNsq466jHmY6OQVfEkdBmQ87R5O";
		
	$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);
		
	$odpowiedz = json_decode($sprawdz);
		
	if ($odpowiedz->success==false)
	{
		$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		header('Location: login.php');
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
						header('Location: index.php');
					} else {
						$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
						$_SESSION['liczba-bledow']++;
						header('Location: login.php');
					}
					
				} else {
					
					$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
					$_SESSION['liczba-bledow']++;
					header('Location: login.php');
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
	
?>