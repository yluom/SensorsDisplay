<?php
	include "bdd.php";
?>

<script type="text/javascript" src="./js/jscolor.js"></script>
<script src="js/jquery-ui-1.10.4.custom.js"></script>
<script src="js/jQDateRangeSlider-withRuler-min.js"></script>
<script src="js/moment.min.js"></script>



<link rel="stylesheet" href="./css/iThing.css" type="text/css" >

<!-- Init le bubblechart -->
<script type="text/javascript">
	var chart;
		
	var chartData = [{
								"y" : 0,
								"x" : 0,
								"value" : 0
				
							}
	   /*<?php
			include "./include/reqChart.php";
		?>*/
	];
	
	exportConfig = {
		menuTop: 'auto',
		menuLeft: 'auto',
		menuRight: '30px',
		menuBottom: '30px',
		menuItems: [{
			textAlign: 'center',
			onclick: function () {},
			icon: '../amcharts/images/export.png',
			iconTitle: 'Save chart as an image',
			items: [{
				title: 'JPG',
				format: 'jpg'
			}, {
				title: 'PNG',
				format: 'png'
			}, {
				title: 'SVG',
				format: 'svg'
			}]
		}],
		menuItemStyle: {
			backgroundColor: 'transparent',
			rollOverBackgroundColor: '#EFEFEF',
			color: '#000000',
			rollOverColor: '#CC0000',
			paddingTop: '6px',
			paddingRight: '6px',
			paddingBottom: '6px',
			paddingLeft: '6px',
			marginTop: '0px',
			marginRight: '0px',
			marginBottom: '0px',
			marginLeft: '0px',
			textAlign: 'left',
			textDecoration: 'none'
		}
	}
	
	
	var graph = new AmCharts.AmGraph();
	AmCharts.ready(function () {
		// XY Chart
		chart = new AmCharts.AmXYChart();
		chart.pathToImages = "amcharts/images/";
		chart.dataProvider = chartData;
		chart.startDuration = 1.5;
	
		// AXES
		// X
		var xAxis = new AmCharts.ValueAxis();
		xAxis.position = "bottom";
		xAxis.axisAlpha = 0;
		xAxis.minMaxMultiplayer = 1.2;
		chart.addValueAxis(xAxis);
	
		// Y
		var yAxis = new AmCharts.ValueAxis();
		yAxis.position = "left";
		yAxis.minMaxMultiplier = 1.2;
		yAxis.axisAlpha = 0;
		chart.addValueAxis(yAxis);
	
		// GRAPHS
		// first graph
		
		graph.valueField = "value";
		graph.lineColor = "#00FFFF";
		graph.xField = "x";
		graph.yField = "y";
		graph.lineAlpha = 0;
		graph.bullet = "bubble";
		graph.bulletBorderThickness = 0.5;
		graph.bulletAlpha = 0.75;	//Opacity
		graph.bulletBorderAlpha = 0.8;	//Opacity des bords
		graph.balloonText = "x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[value]]</b>";
		chart.addGraph(graph);
	
		// CURSOR
		var chartCursor = new AmCharts.ChartCursor();
		chart.addChartCursor(chartCursor);
	
		// SCROLLBAR
		var chartScrollbar = new AmCharts.ChartScrollbar();
		chart.addChartScrollbar(chartScrollbar);
	
		chart.exportConfig = {}; 
	
		// WRITE                                                
		chart.write("chartdiv");
	});
</script>

<!-- Recharge les data du chart en fonction du temps -->
<script>
function updaValues(dateDeb, dateFin)
	{
		opt1capt = document.getElementById('optioncapteur1').value;
		opt1lib = document.getElementById('optionlib1').value;
		
		opt2capt = document.getElementById('optioncapteur2').value;
		opt2lib = document.getElementById('optionlib2').value;
		
		opt3capt = document.getElementById('optioncapteur3').value;
		opt3lib = document.getElementById('optionlib3').value;
		
		document.getElementById('chartdiv').innerHTML='<h2><img src="./img/loading.gif" style="margin-right:25px;"/>Please wait...</h2>';
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText==""){
				
				} else {
					var chartData = JSON.parse("[" + xmlhttp.responseText + "]");
					chart.dataProvider = chartData;
					chart.validateData();
					chart.write("chartdiv");
				}
			}
		}
		xmlhttp.open("GET","./include/reqChart.php?dateDeb="+dateDeb+"&dateFin="+dateFin+"&idCapteur1="+opt1capt+"&idLibVal1="+opt1lib+"&idCapteur2="+opt2capt+"&idLibVal2="+opt2lib+"&idCapteur3="+opt3capt+"&idLibVal3="+opt3lib,true);
		xmlhttp.send();
	}
</script>

<!-- Charge les pieces -->
<script>
	function showPiece(str, valeur)
	{
		document.getElementById('formPiece'+valeur).style.visibility="hidden";
		document.getElementById('formCapteur'+valeur).style.visibility="hidden";
		document.getElementById('formLib'+valeur).style.visibility="hidden";
		if (str==""){
			document.getElementById('optionpiece'+valeur).innerHTML="";
			return;
		} 
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('optionpiece'+valeur).innerHTML=xmlhttp.responseText;
				if(xmlhttp.responseText.indexOf("option")==-1){
					document.getElementById('formPiece'+valeur).style.visibility="hidden";
					document.getElementById('formCapteur'+valeur).style.visibility="hidden";
					document.getElementById('formLib'+valeur).style.visibility="hidden";
				} else {
					piece = document.getElementById('optionpiece' + valeur).value;
					showCapteur(piece, valeur);
					document.getElementById('formPiece'+valeur).style.visibility="visible";
				}
			}
		}
		
		xmlhttp.open("GET","./include/getPiece.php?batiment="+str,true);
		xmlhttp.send();
	}
</script>

<!-- Charge les capteurs -->
<script>
	function showCapteur(str, valeur)
	{
		document.getElementById('formCapteur'+valeur).style.visibility="hidden";
		document.getElementById('formLib'+valeur).style.visibility="hidden";
		if (str==""){
			document.getElementById('optioncapteur'+valeur).innerHTML="";
			return;
		} 
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('optioncapteur'+valeur).innerHTML=xmlhttp.responseText;
				if(xmlhttp.responseText.indexOf("option")==-1){
					document.getElementById('formCapteur'+valeur).style.visibility="hidden";
					document.getElementById('formLib'+valeur).style.visibility="hidden";
				} else {
					capteur = document.getElementById('optioncapteur' + valeur).value;
					showLib(capteur, valeur);
					document.getElementById('formCapteur'+valeur).style.visibility="visible";
				}
			}
		}
		
		xmlhttp.open("GET","./include/getCapteur.php?idPiece="+str,true);
		xmlhttp.send();
	}
</script>

<!-- Charge les libelle des variables -->
<script>
	function showLib(str, valeur)
	{
		document.getElementById('formLib'+valeur).style.visibility="hidden";
		if (str==""){
			document.getElementById('optionlib'+valeur).innerHTML="";
			return;
		} 
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				document.getElementById('optionlib'+valeur).innerHTML=xmlhttp.responseText;
				if(xmlhttp.responseText.indexOf("option")==-1){
					document.getElementById('formLib'+valeur).style.visibility="hidden";
				} else {
					document.getElementById('formLib'+valeur).style.visibility="visible";
				}
			}
		}
		
		xmlhttp.open("GET","./include/getLib.php?idCapteur="+str,true);
		xmlhttp.send();
	}
</script>

<!-- Change la couleur du graph -->
<script>
	function updaColor(color){
		graph.lineColor = "#"+color;
		chart.validateNow();
	}
</script>

<!-- Affiche le nouveau graph après validation -->
<script>
	function valide(){
	
		date = $( "#optionDate option:selected" ).text();
	
		opt1capt = document.getElementById('optioncapteur1').value;
		opt1lib = document.getElementById('optionlib1').value;
		
		opt2capt = document.getElementById('optioncapteur2').value;
		opt2lib = document.getElementById('optionlib2').value;
		
		opt3capt = document.getElementById('optioncapteur3').value;
		opt3lib = document.getElementById('optionlib3').value;
		
		document.getElementById('chartdiv').innerHTML='<h2><img src="./img/loading.gif" style="margin-right:25px;"/>Please wait...</h2>';
		if (window.XMLHttpRequest){	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		} else {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText==""){
				
				} else {
					var chartData = JSON.parse("[" + xmlhttp.responseText + "]");
					chart.dataProvider = chartData;
					chart.validateData();
					chart.write("chartdiv");
				}
			}
		}
		xmlhttp.open("GET","./include/reqChart.php?date="+date+"&idCapteur1="+opt1capt+"&idLibVal1="+opt1lib+"&idCapteur2="+opt2capt+"&idLibVal2="+opt2lib+"&idCapteur3="+opt3capt+"&idLibVal3="+opt3lib,true);
		xmlhttp.send();
	/*
		if(document.getElementById('formPiece').style.visibility=="visible") {
			var e = document.getElementById("optionpiece");
			var strUser = e.options[e.selectedIndex].value;
			alert(strUser);
		} else {
			alert("Please choose a piece");
		}*/
	}
</script>

<script>
	function changeBullet(str){
		alert(str);
		graph.bullet = str;
		chart.validateNow();
	}
</script>

<!-- En tête du wrapper -->
<div class="row">
  <div class="col-lg-12">
	<h1>Charts <small>Display Your Data</small></h1>
	<ol class="breadcrumb">
	  <li><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	  <li class="active"><i class="fa fa-bar-chart-o"></i> Charts</li>
	</ol>
  </div>
</div><!-- /.row -->

<!-- BubbleChart -->
<div class="row">
  <div class="col-lg-12">
	<h2>Flot Charts</h2>
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Line Graph Example with Tooltips</h3>
	  </div>
	  <div class="panel-body">
		<div id="chartdiv" style="width: auto; height: 200px;"></div>
		<div id="slider"></div>
	  </div>
	</div>
	
  </div>
 </div>
 

<!-- Paramétrage des trois variables -->
<div class="row">
	<!-- Première donnée-->
	<div class="col-lg-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> First data (x)</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label>Selects Batiment</label>
					<select class="form-control" onchange="showPiece(this.value, 1)">
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
				<div class="form-group" id="formPiece1" style="visibility: hidden;">
					<label>Selects Piece</label>
					<select class="form-control" id="optionpiece1" onchange="showCapteur(this.value, 1)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formCapteur1" style="visibility: hidden;" >
					<label>Selects Capteur</label>
					<select class="form-control" id="optioncapteur1" onchange="showLib(this.value, 1)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formLib1" style="visibility: hidden;" >
					<label>Selects Variable</label>
					<select class="form-control" id="optionlib1">
						<option>Choisir</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<!-- Seconde donnée-->
	<div class="col-lg-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Second data (y)</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label>Selects Batiment</label>
					<select class="form-control" onchange="showPiece(this.value, 2)">
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
				<div class="form-group" id="formPiece2" style="visibility: hidden;">
					<label>Selects Piece</label>
					<select class="form-control" id="optionpiece2" onchange="showCapteur(this.value, 2)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formCapteur2" style="visibility: hidden;" >
					<label>Selects Capteur</label>
					<select class="form-control" id="optioncapteur2" onchange="showLib(this.value, 2)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formLib2" style="visibility: hidden;" >
					<label>Selects Variable</label>
					<select class="form-control" id="optionlib2">
						<option>Choisir</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<!-- Troisième donnée-->
	<div class="col-lg-4">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Third data (value)</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label>Selects Batiment</label>
					<select class="form-control" onchange="showPiece(this.value, 3)">
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
				<div class="form-group" id="formPiece3" style="visibility: hidden;">
					<label>Selects Piece</label>
					<select class="form-control" id="optionpiece3" onchange="showCapteur(this.value, 3)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formCapteur3" style="visibility: hidden;" >
					<label>Selects Capteur</label>
					<select class="form-control" id="optioncapteur3" onchange="showLib(this.value, 3)">
						<option>Choisir</option>
					</select>
				</div>
				<div class="form-group" id="formLib3" style="visibility: hidden;" >
					<label>Selects Variable</label>
					<select class="form-control" id="optionlib3">
						<option>Choisir</option>
					</select>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Autres paramètre (couleur, forme, ...) et BtnSubmit -->
<div class="row">
	  <div class="col-lg-12">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Parameters</h3>
			</div>
			 <div class="panel-body">
				
				
				<div class="form-group">
					<label>Selects Color</label>
					<input class="form-control color" onchange="updaColor(this.value)">
				</div>
				<div class="form-group">
					<label>Selects Bullet</label>
					<select class="form-control" onchange="changeBullet(this.value)">
						<option>none</option>
						<option>round</option>
						<option>square</option>
						<option>triangleUp</option>
						<option>triangleDown</option>
						<option>triangleLeft</option>
						<option>triangleRight</option>
						<option>bubble</option>
						<option>diamond</option>
						<option>xError</option>
						<option>yError</option>
					</select>
				</div>
				
				<button class="btn btn-success" style="float: right;" onClick="valide()">Submit</button>
			</div>
		</div>
	  </div>
	</div><!-- /.row -->

<!-- Lance le dateSlider -->	
<script>
	//$("#slider").dateRangeSlider();
	
	
	var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
  $("#slider").dateRangeSlider({
    bounds: {min: new Date(2010, 11, 1), max: new Date(2010, 12, 31)},
    defaultValues: {min: new Date(2010, 11, 13), max: new Date(2010, 11, 20)},
    scales: [{
      first: function(value){ return value; },
      end: function(value) {return value; },
      next: function(value){
        var next = new Date(value);
        return new Date(next.setMonth(value.getMonth() + 1));
      },
      label: function(value){
        return months[value.getMonth()];
      },
      format: function(tickContainer, tickStart, tickEnd){
        tickContainer.addClass("myCustomClass");
      }
    }]
  });
</script>
	
	
<script>
	$("#slider").bind("valuesChanged", function(e, data){
		var dateDeb = moment(new Date(data.values.min)).format("YYYY-MM-DD");
		var dateFin = moment(new Date(data.values.max)).format("YYYY-MM-DD");
		updaValues(dateDeb, dateFin);
	});
</script>
	

