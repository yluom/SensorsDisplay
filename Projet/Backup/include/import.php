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
					if(str == "Choose :"){
						document.getElementById('alert').className="alert alert-info";
						document.getElementById('alert').innerHTML="<h4>Info!</h4><p>Please choose a building and then select a room.</p>";
					}else{
						document.getElementById('alert').className="alert alert-warning";
						document.getElementById('alert').innerHTML="<h4>Warning!</h4><p> This building does not contains any rooms, please select another building.</p>";
						document.getElementById('formPiece').style.visibility="hidden";
					}
				} else {
					document.getElementById('alert').className="alert alert-success";
					document.getElementById('alert').innerHTML="<h4>Success!</h4><p>This building has rooms, please select one.</p>";
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
		} else {
			alert("Please choose a piece");
		}
	}
</script>

<div class="row">
  <div class="col-lg-12">
	<h1>Import File <small>Import Your Data Into SensorsDataDisplay Database</small></h1>
	<ol class="breadcrumb">
	  <li><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	  <li class="active"><i class="fa fa-edit"></i> Import File</li>
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
						<label>Select Building</label>
						<select class="form-control" onchange="showPiece(this.value)">
							<option>Choose :</option>
						<?php
							$resultats=$connection->query("SELECT nom FROM batiment");
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
						<p>Please choose a building and then select a room.</p>
					</div>
					
			</div>
	</div>
	<div class="row">
		<div class="col-lg-6">
		</div>
		<div class="col-lg-6">
				<div class="form-group" id="formPiece" style="visibility: hidden;">
					<label>Select Piece</label>
					<select class="form-control" id="optionpiece" name="idpiece">
						<option>Choisir</option>
					</select>
				</div>
				<button type="submit" class="btn btn-success" style="float: right;" onClick="valide()">Submit</button>
		</div>
	</div>
</form>

