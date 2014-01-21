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

<div class="row">
	<form role="form" enctype="multipart/form-data" action="parser.php" method="POST">
		<div class="col-lg-6">
				<div class="form-group">
					<label>File input</label>
					<input type="file" name="data">
				</div>
		</div>
	  
		<div class="col-lg-6">
				<div class="form-group">
					<label>Selects</label>
					<select class="form-control">
					<?php
						$resultats=$connection->query("SELECT nom FROM piece");
						$resultats->setFetchMode(PDO::FETCH_OBJ);
						while( $resultat = $resultats->fetch() )
						{
								echo '<option>'.$resultat->nom.'</option>';
						}
						$resultats->closeCursor();
					?>
					</select>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
		</div>
	</form>
</div><!-- /.row -->