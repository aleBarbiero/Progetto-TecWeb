<?php
	require_once "php/header.php";
	require_once "php/footer.php";
	require_once "php/dbhandler.php";
	
	if (!isset($_SESSION)) {
		session_start();
	}
	if (!isset($_SESSION["username"])) {
		header("Location: login.php");
		exit();
	}
	require_once "php/concludi-acquisto.php";
	require_once "php/rimuovi_carrello.php";
	
	
	
	function getImages(){
		$connessione=connessione();

		$username=$_SESSION["username"];
		$carrello=$connessione->query("SELECT * FROM carrello WHERE utente='$username' AND stato='in corso';");
		$img="<div id=\"gridContainer\"><div class=\"fotoCarrello\"><ul>";
		$tot=0;
		if(!$carrello || mysqli_num_rows($carrello)==0){
			$img.="<li class=\"center\">Il tuo carrello è vuoto!</li></ul></div></div>";
		}else{
			$prices=array();
			while($foto=$carrello->fetch_assoc()){
				$idImg=$foto["foto"];
				$immagine=$connessione->query("SELECT * FROM foto WHERE id='$idImg';");
				$getImg=$immagine->fetch_assoc();
				if(file_exists("upload/".$idImg.'.png')){
					$url="upload/".$idImg.'.png';
				}else if(file_exists("upload/".$idImg.'.jpg')){
						$url="upload/".$idImg.'.jpg';
				}else if(file_exists("upload/".$idImg.'.jpeg')){
						$url="upload/".$idImg.'.jpeg';
				}
				$prezzo=$getImg["prezzo"];
				$titolo=$getImg["titolo"];
				$venditore=$getImg["venditore"];
				$img.="<li><form method=\"post\" ><button class=\"removeButton\" type=\"submit\" name=\"rimuovi-immagine\" aria-label=\"Rimuovi oggetto dal carrello\"><i class=\"fa fa-times\"></i></button></form>";
				$img.="<img class=\"imgElement\" src=\"".$url."\" alt=\"".$titolo."\"/>
					<div id=\"parag\">
							<p> <strong>Titolo: </strong>".$titolo."</p>
							<p> <strong>Venditore: </strong>".$venditore."</p>
					</div>	
				</li>";
				$prices[$titolo]=$prezzo;
				$tot=$tot+$prezzo;
				$_SESSION['img']=$idImg;
			}
			$img.="</ul></div><div id=\"prezziContainer\"><div class=\"prezzi\">";
			foreach($prices as $title => $price)
				$img.="<p><strong>".$title."</strong>&nbsp;".$price."&euro;</p>";
			$img.="<div id=\"carrelloFinale\"> <p><strong>Totale</strong>:".$tot."€</p><form method=\"post\" ><button class=\"submitButton\" type=\"submit\" name=\"concludi-acquisto\">Concludi acquisto</button><button class=\"submitButton\" type=\"submit\" name=\"svuota-carrello\">Svuota carrello</button></form></div></div></div></div>";
		}
		return $img;
	}

	
	$output=file_get_contents("html/carrello.html");
	$output=str_replace("<div id=\"header\"></div>", Header::build(), $output);
	$output=str_replace("<div id=\"footer\"></div>", Footer::build(), $output);
	$output=str_replace("<div class=\"foto\"/>",getImages(),$output);
	$output=str_replace("<meta/>",file_get_contents("html/meta.html"),$output);
	
	
	echo $output;