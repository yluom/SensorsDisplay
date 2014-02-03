<?php
	include "bdd.php";
?>

<script>
	function showPiece(str)
	{
		document.getElementById('formPiece').style.visibility="hidden";
		if (str==""){
			document.getElementById('optionpiece').innerHTML="";
			return;
		} 
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('optionpiece').innerHTML=xmlhttp.responseText;
				if(xmlhttp.responseText==""){
					if(str=="Choose :"){
						document.getElementById('alert').className="alert alert-info";
						document.getElementById('alert').innerHTML="<h4>Info!</h4><p>Veuillez choisir un batiment puis selectionner une piece.</p>";
					}else{
						document.getElementById('alert').className="alert alert-warning";
						document.getElementById('alert').innerHTML="<h4>Warning!</h4><p>Ce batiment ne contient aucune piece, merci d'en choisir un autre.</p>";
						document.getElementById('formPiece').style.visibility="hidden";
					}
				} else {
					document.getElementById('alert').className="alert alert-success";
					document.getElementById('alert').innerHTML="<h4>Success!</h4><p>Ce batiment contient des pieces, veuillez en selectionner une.</p>";
					document.getElementById('formPiece').style.visibility="visible";
				}
			}
		}
		
		xmlhttp.open("GET","./include/getPiece.php?batiment="+str,true);
		xmlhttp.send();
	}
	
	function valide()
	{
		if(document.getElementById('formPiece').style.visibility=="visible") {
			var e = document.getElementById("optionpiece");
			var strUser = e.options[e.selectedIndex].value;
			alert(strUser);
		} else {
			alert("Please choose a piece");
		}
	}
</script>

<div class="row">
  <div class="col-lg-12">
	<h1>Importation <small>Import Your Data</small></h1>
	<ol class="breadcrumb">
	  <li><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	  <li class="active"><i class="fa fa-edit"></i> Forms</li>
	</ol>
  </div>
</div><!-- /.row -->


<!--<form role="form" enctype="multipart/form-data" action="./include/parser.php" method="POST">-->
<form role="form" enctype="multipart/form-data" action="./index.php?p=parser" method="POST">
	<div class="row">
			<div class="col-lg-6">
					<div class="form-group">
						<label>File input</label>
						<input type="file" name="data">
					</div>
			</div>
		  
			<div class="col-lg-6">
					<div class="form-group">
						<label>Selects Batiment</label>
						<select class="form-control" onchange="showPiece(this.value)">
							<option>Choose :</option>
						<?php
							$resultats=$connection->query("SELECT nom FROM Batiment");
							$resultats->setFetchMode(PDO::FETCH_OBJ);
							while( $resultat = $resultats->fetch() )
							{
									echo '<option>'.$resultat->nom.'</option>';
							}
							$resultats->closeCursor();
						?>
						</select>
					</div>
					
					<div class="alert alert-info" id="alert">
						<h4>Info!</h4>
						<p>Veuillez choisir un batiment puis selectionner une piece.</p>
					</div>
					
			</div>
	</div>
	<div class="row">
		<div class="col-lg-6">
		</div>
		<div class="col-lg-6">
				<div class="form-group" id="formPiece" style="visibility: hidden;">
					<label>Selects Piece</label>
					<select class="form-control" id="optionpiece" name="idpiece">
						<option>Choisir</option>
					</select>
				</div>
				<button type="submit" class="btn btn-success" style="float: right;" onClick="valide()">Submit</button>
		</div>
	</div>
</form>

