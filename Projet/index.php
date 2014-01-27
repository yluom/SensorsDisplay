<!-- Initialisation des variables PHP -->
<?php
	//Colore l'onglet actif
	$dashboard = "";
	$charts = "";
	$form = "";
	$import = "";
	if(!empty($_GET['p'])){
		//Selon l'onglet courant
		switch($_GET['p']){
			case 'dashboard' :	$dashboard = "active";
				break;
			case 'charts' : 	$charts = "active";
				break;
			case 'editB' : 		$form = "active";
				break;
			case 'editC' : 		$form = "active";
				break;
			case 'import' :  	$import = "active";
				break;
		}
	} else {
		$dashboard = "active";
	}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Clement Edouard - Léo Mouly">

    <title>Dashboard - SensorsDisplay</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="css/morris-0.4.3.min.css">
	
	<!-- Importation des fichiers JavaScript pour le Bubble Chart-->
	<script src="amcharts/amcharts.js" type="text/javascript"></script>
    <script src="amcharts/xy.js" type="text/javascript"></script>
	<script src="amcharts/exporting/amexport.js" type="text/javascript"></script>
	<script src="amcharts/exporting/rgbcolor.js" type="text/javascript"></script>
	<script src="amcharts/exporting/canvg.js" type="text/javascript"></script>        
	<script src="amcharts/exporting/filesaver.js" type="text/javascript"></script>
	
	<!-- Configuration du BubbleC Chart -->
	<script type="text/javascript">
            var chart;
			
            var chartData = [
                {
                    "y": 10,
                    "x": 14,
                    "value": 59,
                    "y2": -5,
                    "x2": -3,
                    "value2": 44
                },
                {
                    "y": 5,
                    "x": 3,
                    "value": 50,
                    "y2": -15,
                    "x2": -8,
                    "value2": 12
                },
                {
                    "y": -10,
                    "x": -3,
                    "value": 19,
                    "y2": -4,
                    "x2": 6,
                    "value2": 35
                },
                {
                    "y": -6,
                    "x": 5,
                    "value": 65,
                    "y2": -5,
                    "x2": -6,
                    "value2": 168
                },
                {
                    "y": 15,
                    "x": -4,
                    "value": 92,
                    "y2": -10,
                    "x2": -8,
                    "value2": 102
                },
                {
                    "y": 13,
                    "x": 1,
                    "value": 8,
                    "y2": -2,
                    "x2": -3,
                    "value2": 41
                },
                {
                    "y": 1,
                    "x": 6,
                    "value": 35,
                    "y2": 0,
                    "x2": -3,
                    "value2": 16
                }
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
                var graph = new AmCharts.AmGraph();
                graph.valueField = "value";
                graph.lineColor = "#b0de09";
                graph.xField = "x";
                graph.yField = "y";
                graph.lineAlpha = 0;
                graph.bullet = "bubble";
                graph.balloonText = "x:<b>[[x]]</b> y:<b>[[y]]</b><br>value:<b>[[value]]</b>";
                chart.addGraph(graph);
            
                // second graph
                graph = new AmCharts.AmGraph();
                graph.valueField = "value2";
                graph.lineColor = "#fcd202";
                graph.xField = "x2";
                graph.yField = "y2";
                graph.lineAlpha = 0;
                graph.bullet = "bubble";
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
	
	
</head>

<body>

	<div id="wrapper">

		<!-- Sidebar -->
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="index.html">SensorsDisplay</a>
			</div>

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse navbar-ex1-collapse">
				<ul class="nav navbar-nav side-nav">
					<li class="<?php echo $dashboard; ?>"><a href="index.php?p=dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
					<li class="<?php echo $charts; ?>"><a href="index.php?p=charts"><i class="fa fa-bar-chart-o"></i> Charts</a></li>
					<li class="dropdown <?php echo $form; ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-edit"></i> Forms <b class="caret"></b></a>
						<ul class="dropdown-menu">
						<li><a href="index.php?p=editB">Edit Batiments</a></li>
						<li><a href="index.php?p=editC">Edit Cursors</a></li>
						</ul>
					</li>
					<li class="<?php echo $import; ?>"><a href="index.php?p=import"><i class="fa fa-file"></i> Import File</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right navbar-user">
					<li class="dropdown user-dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> Clement Edouard <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#"><i class="fa fa-user"></i> Profile</a></li>
							<li><a href="#"><i class="fa fa-gear"></i> Settings</a></li>
							<li class="divider"></li>
							<li><a href="#"><i class="fa fa-power-off"></i> Log Out</a></li>
						</ul>
					</li>
				</ul>
			</div><!-- /.navbar-collapse -->
		</nav>
		
		<!-- CONTENU DE LA PAGE CENTRALE -->
		<div id="page-wrapper">
			<?php
				if(!empty($_GET['p'])){
					if(file_exists('include/' . $_GET['p'] . '.php')){
							include('include/' . $_GET['p'] . '.php');
					} else {
							include('include/dashboard.php');
					}
				} else {
					include('include/dashboard.php');
				}
			?>
		</div><!-- /#page-wrapper -->

	</div><!-- /#wrapper -->

	<!-- Importation JavaScript de JQuery et Bootstrap-->
	<script src="js/jquery-1.10.2.js"></script>
	<script src="js/bootstrap.js"></script>

	<!-- Page Specific Plugins -->
	<script src="js/raphael-min.js"></script>
	<script src="js/morris-0.4.3.min.js"></script>
	<script src="js/morris/chart-data-morris.js"></script>
	<script src="js/tablesorter/jquery.tablesorter.js"></script>
	<script src="js/tablesorter/tables.js"></script>
</body>
</html>
