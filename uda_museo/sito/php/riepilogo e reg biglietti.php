<?php
//sessione, connessione al db e recupero variabili dalla sessione
session_start();
include("conndb.php");
$email= $_SESSION['email'];
$nBiglietti= $_SESSION['nBiglietti'];
$evento= $_SESSION['eventi'];
$accessori=$_SESSION['accessori'];

//ramificazione del flusso
if(!isset($_POST['Procedere'])){
	//riepilogo del ordine
echo "riepilogo:<br>";
echo"Numero biglietti: $nBiglietti<br>";
echo"evento: $evento<br>";
echo "Accessori: ";
foreach($accessori as $accessorio){
echo $accessorio."<br>";


}
//form di conferma per procedere
echo "<form action=\"riepilogo e reg biglietti.php\" method=\"POST\">
		Procedere?
		<input type=\"submit\" name=\"Procedere\" value=\"si\" />
		<input type=\"reset\" onclick=\"clearForm()\"name=\"cancella\" value=\"No\" /> <br><br>
		</form>";
		
}else{
	//secondo ramo
	
	//recupero data odierna per il biglietto
	$timestamp=strtotime("+1 day");
	$date = date('Y-m-d',$timestamp);
	//recupero codice dell'evento
	$codEve=0;
	$sql1="select codEve from EVENTO where titolo='$evento'";
	$query1 = mysqli_query($mysqli,$sql1);
	if($query1){
		while($cicle1=mysqli_fetch_array($query1)){
		$codEve=$cicle1['codEve'];
	}
	}else{
		//esito egativo
		//errore 
		echo "Error: ". $sql1 . "<br>" .mysqli_error($mysqli);
	}
	
	
	
	
	
	//registrazione del biglietto/biglietti nel database 
	//ciclo per ripetere le azioni successive nel caso di acquisto simultaneo di piu biglietti
	for($i=0;$i<$nBiglietti;$i++){
	$sql2="insert into BIGLIETTO(dataVal,Eve,vis) values ('$date','$codEve','$email')";
		$query2 = mysqli_query($mysqli,$sql2);
		//controllo esito query
		if($query2){
			
	}else{
		//esito egativo
		//errore 
		echo "Error: ". $sql2 . "<br>" .mysqli_error($mysqli);
	}
	
	
	
	
	//recupero del codice del biglietto appena inserito
	$codBig=0;
	$sql3="select codBig from BIGLIETTO where dataVal='$date' AND Eve='$codEve' AND vis='$email'";
	$query3 = mysqli_query($mysqli,$sql3);
		//controllo esito query
		if($query3){
			//esito positivo
			//controllo per impedire un errato abbinamento nel caso l'utente abbia più di un biglietto acquistato nello stesso giorno
			while($cicle3=mysqli_fetch_array($query3)){
		$codBig=$cicle3['codBig'];
	}
	}else{
		//esito egativo 2
		//errore 
		echo "Error: ". $sql3 . "<br>" .mysqli_error($mysqli);
	}
	
	
	
	//ciclo per l'abbinamento biglietto -> 1 o piu accessori
	if(!empty($accessori)){
	foreach($accessori as $accessorio){
		//recupero del codice del accessorio in esame
	$codAcc=0;
	$sql4="select codAcc from ACCESSORIO where descAcc='$accessorio'";
	$query4 = mysqli_query($mysqli,$sql4);
		//controllo esito query
		if($query4){
			//esito positivo
			//sarà sempre un giro ma se non metto un while non funziona(bho)
			while($cicle4=mysqli_fetch_array($query4)){
			$codAcc=$cicle4['codAcc'];
	}
	
	
	
				// abbinamento biglietto accessorio in apposita tabella
					$sql5="insert into BIGLACC(cBigl,cAcc) values($codBig,$codAcc)";
					$query5 = mysqli_query($mysqli,$sql5);
					//controllo esito query
						if($query5){
							//esito positivo
							//termine inserimento dati senza errori, l'utente viene riportato a index(temporaneo)
							header("location:index.php");
			
	}else{
		//esito egativo 5
		//errore 
		echo "Error: ". $sql5 . "<br>" .mysqli_error($mysqli);
	}
	
	}else{
		//esito egativo 4
		//errore e ripetizione dell'inserimento dei dati
		echo "Error: ". $sql4 . "<br>" .mysqli_error($mysqli);
	}
	
	
	
	
		
}
}else{
	$accessori="no accessori";//modificare quando si farà il conteggio del prezzo
}
}	
}	
?>

<script>
function clearForm(){
	//script per l'annullamento della transazione e redirect al index.php in caso di risposta negativa al form di conferma dei dati
	  alert('transazione annullata');
      window.location='index.php';
  }
      </script>
      
      
      
      
      
      
      
      
      
      
