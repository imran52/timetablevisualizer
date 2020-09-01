$(document).ready(function(){
	//Check if db connected, else display error
	//Ajax for calling php function
	$.post('api/dbCheck.php', { requestType: 'DBCHECK'}, function(data){
		try{
			var dbConnResult = JSON.parse(data);
			//alert(String.parse(dbConnResult.outcome));
			if (!dbConnResult.outcome){
				console.log('DB Connection failed, redirecting...');
				location.href = 'error.html';
			}
		}catch (e) {
			console.log("index.js,Unexpected response from server!");
			console.log(data);
			location.href = 'error.html';
		}
	});
	//Load the default tab
	$('div#home').load('content/upload.html',function(){
		$.getScript('js/upload.js');
	});
	$('a#uploadpagetab').click(function(){
		$('div#home').load('content/upload.html',function(){
			$.getScript('js/upload.js');
		});
		
	});	
	$('a#studyplanpagetab').click(function(){
		$('div#studyPlan').load('content/studyplan.html');
		$.getScript('js/studyplan.js');
	});
	$('a#timetablepagetab').click(function(){
		$('div#timeTable').load('content/timetable.html');
		$.getScript('js/timetable.js');
	});
});