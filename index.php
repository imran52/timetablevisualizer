<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Timetable Visualizer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>	
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/mustache.js/3.1.0/mustache.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/style.css">
	<script language="JavaScript" type="text/javascript" src="js/index.js"></script> 
	<script src="js/jquery.redirect.js"></script>
	<link rel="icon" type="image/png" href="./favicon.png"> 
  </head>
  <body>

	<div id="headerTitle" class="navbar shadow">
      <a class="navbar-brand" href="#">Timetable Visualizer</a>	
	</div>
	
    <div class="container">	
	<br><br><br>
      <ul id="my-tab" class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link active border-bottom-0" id="uploadpagetab" data-toggle="tab" href="#home">Upload</a>
        </li>
        <li class="nav-item">
          <a class="nav-link  border-bottom-0" id="studyplanpagetab" data-toggle="tab" href="#studyPlan">Study Plan</a>
        </li>
        <li class="nav-item">
          <a class="nav-link  border-bottom-0" id="timetablepagetab" data-toggle="tab" href="#timeTable">Timetable</a>
        </li>
      </ul>
      <div id="my-tab-content" class="tab-content">
        <div id="home" class="container tab-pane active pb-5">
		<!--To be loaded dynamically-->
        </div>
        <div id="studyPlan" class="container tab-pane fade mb-3 pb-3">
		<!--
			Content to be loaded dynamically.
		-->
		</div>
        <div id="timeTable" class="container tab-pane fade pb-2">
		<!--To be loaded dynamically-->
        </div>
      </div>
    </div>
  </body>
</html>