<?php
	require_once "dbhandler.php";
	if (!isset($_SESSION)) {
		session_start();
	}
	if (!isset($_SESSION["username"])) {
		header("Location: login.php");
		exit();
	}
//CARICAMENTO IMMAGINE
	if(isset($_POST['carica_img'])){
		$titolo=mysqli_real_escape_string($connessione,sanitizeString($_POST['titolo'])); 
		$categoria=mysqli_real_escape_string($connessione,sanitizeString($_POST['selectCategoria']));
		$prezzo=mysqli_real_escape_string($connessione,sanitizeString($_POST['prezzo']));
		$tag1=mysqli_real_escape_string($connessione,sanitizeString($_POST['tag1']));
		$tag2=mysqli_real_escape_string($connessione,sanitizeString($_POST['tag2']));
		$tag3=mysqli_real_escape_string($connessione,sanitizeString($_POST['tag3']));
		$temp_name=$_FILES["file"]["tmp_name"];
		$data=date('Y-m-d');
		if(empty($tag1)){
			$tag1='';
		}
		if(empty($tag2)){
			$tag2='';
		}
		if(empty($tag3)){
			$tag3='';
		}
		if(empty($titolo)){
			$errors['titolo']="Titolo richiesto";
		}else if(empty($prezzo)){
			$errors['prezzo']="Prezzo richiesto";
		}else if($categoria=='Seleziona'){
			$errors['categoria']="Categoria richiesta";
		}else{
			$idcategoria=$_POST['selectCategoria'];
			$user=$_SESSION['username'];
			$connessione->query("INSERT INTO foto (titolo, venditore, prezzo, stato, categoria, data, tag1, tag2, tag3) 
				  VALUES('$titolo', '$user', '$prezzo', 'in attesa', '$idcategoria', '$data', '$tag1', '$tag2', '$tag3');");
			$result=$connessione->query("SELECT id FROM foto WHERE venditore='$user' ORDER BY id DESC LIMIT 1;");
			$ris=mysqli_fetch_assoc($result);
			$name=$ris['id'].'.'.pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION);
			if(isset($name)){
				if(!empty($name)){      
					$location='upload/';      
					if(!move_uploaded_file($temp_name, $location.$name)){
						$idfoto=$ris['id'];
						$connessione->query("DELETE FROM foto WHERE id='$idfoto';");
					}
				}       
			}
			header("Location: fotoOk.php");
		}
	}
?>