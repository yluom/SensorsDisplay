<?php
	include "bdd.php";
?>



<div class="row">
  <div class="col-lg-12">
	<h1>Importation <small>Import Your Data</small></h1>
	<ol class="breadcrumb">
	  <li><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	  <li class="active"><i class="fa fa-edit"></i> Forms</li>
	</ol>
  </div>
</div><!-- /.row -->

<form role="form" enctype="multipart/form-data" action="parser.php" method="POST">
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
					
			</div>
	</div>
	<div class="row">
		<div class="col-lg-6">
		</div>
		<div class="col-lg-6">
				<div class="form-group">
					<label>Selects Piece</label>
					<select class="form-control">
						<div id='optionpiece'><option>Choisir</option></div>
					</select>
				</div>
		</div>
		<button type="submit" class="btn btn-default">Submit</button>
	</div>
</form>



<script>
	function showPiece(str)
	{
		alert(str);
		if (str==""){
			document.getElementById("optionpiece").innerHTML="";
			return;
		} 
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById("optionpiece").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.open("GET","./include/getPiece.php?batiment="+str,true);
		xmlhttp.send();
	}
</script>