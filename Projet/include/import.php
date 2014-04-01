<?php
	include "bdd.php";
?>

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
			<div class="col-lg-12">
					<div class="form-group">
						<label>File input</label>
						<input type="file" name="data">
					</div>
			</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<button type="submit" class="btn btn-success" style="float: right;">Submit</button>
		</div>
	</div>
</form>

