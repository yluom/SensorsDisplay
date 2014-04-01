<?php
	include 'bdd.php';
	
	$result=$connection->query("SELECT count(*) as nb FROM batiment"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbBatiments = $res->nb;
	
	$result=$connection->query("SELECT count(*) as nb FROM piece"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbPieces = $res->nb;
	
	$result=$connection->query("SELECT count(*) as nbCapteurs FROM capteur"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbCapteurs = $res->nbCapteurs;
	
	$result=$connection->query("SELECT count(*) as nbMesures FROM mesure"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbMesures = $res->nbMesures;
	
	$result=$connection->query("SELECT count(*) as nbTypes FROM typecapteur"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbTypes = $res->nbTypes;
	
	$result=$connection->query("SELECT count(*) as nbVal FROM valeurmesure"); 
	$result->setFetchMode(PDO::FETCH_OBJ);
	$res = $result->fetch();
	$nbVals = $res->nbVal;
	
	$result->closeCursor();
?>
<div class="row">
  <div class="col-lg-12">
	<h1>Dashboard <small>Statistics </small></h1>
	<ol class="breadcrumb">
	  <li class="active"><i class="fa fa-dashboard"></i> Dashboard</li>
	</ol>
  </div>
</div><!-- /.row -->

<div class="row">
  <div class="col-lg-4">
	<div class="panel panel-info">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-comments fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbBatiments; ?></p>
			<p class="announcement-text">Buildings</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=editB">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Edit buildings
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
  <div class="col-lg-4">
	<div class="panel panel-warning">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-check fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbPieces; ?></p>
			<p class="announcement-text">Rooms</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=editB">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Edit Rooms
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
 <!-- "warning"
 <div class="col-lg-3">
	<div class="panel panel-danger">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-tasks fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading">$nbTypes</p>
			<p class="announcement-text"> Types of sensors</p>
		  </div>
		</div>
	  </div>
	  <a href="#">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Fix Issues
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div> -->
  <div class="col-lg-4">
	<div class="panel panel-success">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-comments fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbCapteurs; ?></p>
			<p class="announcement-text"> Sensors</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=editC">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			Edit Sensors
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
</div><!-- /.row -->

<!-- Second row -->
<div class="row">
  <div class="col-lg-4">
	<div class="panel panel-info">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-comments fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbTypes; ?></p>
			<p class="announcement-text">Types of sensors</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=editC">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Edit Sensors
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
  <div class="col-lg-4">
	<div class="panel panel-warning">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-check fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbMesures; ?></p>
			<p class="announcement-text">Mesures</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=charts">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Display charts
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
  <div class="col-lg-4">
	<div class="panel panel-success">
	  <div class="panel-heading">
		<div class="row">
		  <div class="col-xs-6">
			<i class="fa fa-comments fa-5x"></i>
		  </div>
		  <div class="col-xs-6 text-right">
			<p class="announcement-heading"><?php echo $nbVals; ?></p>
			<p class="announcement-text"> Values</p>
		  </div>
		</div>
	  </div>
	  <a href="index.php?p=charts">
		<div class="panel-footer announcement-bottom">
		  <div class="row">
			<div class="col-xs-6">
			  Display charts
			</div>
			<div class="col-xs-6 text-right">
			  <i class="fa fa-arrow-circle-right"></i>
			</div>
		  </div>
		</div>
	  </a>
	</div>
  </div>
</div><!-- /.row -->
<!-- End Second row -->





<div class="row">
  <div class="col-lg-12">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Traffic Statistics: October 1, 2013 - October 31, 2013</h3>
	  </div>
	  <div class="panel-body">
		<div id="morris-chart-area"></div>
	  </div>
	</div>
  </div>
</div><!-- /.row -->

<div class="row">
  <div class="col-lg-4">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Traffic Sources: October 1, 2013 - October 31, 2013</h3>
	  </div>
	  <div class="panel-body">
		<div id="morris-chart-donut"></div>
		<div class="text-right">
		  <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	  </div>
	</div>
  </div>
  <div class="col-lg-4">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-clock-o"></i> Recent Activity</h3>
	  </div>
	  <div class="panel-body">
		<div class="list-group">
		  <a href="#" class="list-group-item">
			<span class="badge">just now</span>
			<i class="fa fa-calendar"></i> Calendar updated
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">4 minutes ago</span>
			<i class="fa fa-comment"></i> Commented on a post
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">23 minutes ago</span>
			<i class="fa fa-truck"></i> Order 392 shipped
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">46 minutes ago</span>
			<i class="fa fa-money"></i> Invoice 653 has been paid
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">1 hour ago</span>
			<i class="fa fa-user"></i> A new user has been added
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">2 hours ago</span>
			<i class="fa fa-check"></i> Completed task: "pick up dry cleaning"
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">yesterday</span>
			<i class="fa fa-globe"></i> Saved the world
		  </a>
		  <a href="#" class="list-group-item">
			<span class="badge">two days ago</span>
			<i class="fa fa-check"></i> Completed task: "fix error on sales page"
		  </a>
		</div>
		<div class="text-right">
		  <a href="#">View All Activity <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	  </div>
	</div>
  </div>
  <div class="col-lg-4">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-money"></i> Recent Imports</h3>
	  </div>
	  <div class="panel-body">
		<div class="table-responsive">
		  <table class="table table-bordered table-hover table-striped tablesorter">
			<thead>
			  <tr>
				<th>Order # <i class="fa fa-sort"></i></th>
				<th>Order Date <i class="fa fa-sort"></i></th>
				<th>Order Time <i class="fa fa-sort"></i></th>
				<th>Amount (USD) <i class="fa fa-sort"></i></th>
			  </tr>
			</thead>
			<tbody>
			  <tr>
				<td>3326</td>
				<td>10/21/2013</td>
				<td>3:29 PM</td>
				<td>$321.33</td>
			  </tr>
			  <tr>
				<td>3325</td>
				<td>10/21/2013</td>
				<td>3:20 PM</td>
				<td>$234.34</td>
			  </tr>
			  <tr>
				<td>3324</td>
				<td>10/21/2013</td>
				<td>3:03 PM</td>
				<td>$724.17</td>
			  </tr>
			  <tr>
				<td>3323</td>
				<td>10/21/2013</td>
				<td>3:00 PM</td>
				<td>$23.71</td>
			  </tr>
			  <tr>
				<td>3322</td>
				<td>10/21/2013</td>
				<td>2:49 PM</td>
				<td>$8345.23</td>
			  </tr>
			  <tr>
				<td>3321</td>
				<td>10/21/2013</td>
				<td>2:23 PM</td>
				<td>$245.12</td>
			  </tr>
			  <tr>
				<td>3320</td>
				<td>10/21/2013</td>
				<td>2:15 PM</td>
				<td>$5663.54</td>
			  </tr>
			  <tr>
				<td>3319</td>
				<td>10/21/2013</td>
				<td>2:13 PM</td>
				<td>$943.45</td>
			  </tr>
			</tbody>
		  </table>
		</div>
		<div class="text-right">
		  <a href="#">View All Transactions <i class="fa fa-arrow-circle-right"></i></a>
		</div>
	  </div>
	</div>
  </div>
</div><!-- /.row -->