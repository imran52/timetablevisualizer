$(document).ready(function(){
	//display form for inputs at start.
	showForm();	
	//variables
	var selectedYear = null;
	var selectedCourse = null;
	var selectedSemester = null;
	var allSpecs = [];	
	var canSubmit = true;
	//poulate years dropdown
	populateCalendarYears();
	
	$('button#backBtnTimeTable').on('click',function(e){
		e.preventDefault();
		//show form
		//console.log('Pressed');
		showForm();
	});
	
	$('#timeTableFormYear').change(function(){		
		selectedYear = $('#timeTableFormYear').val();
		//regenerate semesters
		populateSemesters();
	});
	$('#timeTableFormSemester').change(function(){
		selectedYear = $('#timeTableFormYear').val();
		selectedSemester = $('#timeTableFormSemester').val();
		//regenerate courses
		//populateCourses(selectedSemester);
	});	
	$('#timeTableFormCourse').change(function(){
		selectedYear = $('#timeTableFormYear').val();
		selectedSemester = $('#timeTableFormSemester').val();
		selectedCourse = $('#timeTableFormCourse').val();
		//good to submit
	});
	
	function populateCalendarYears(){
		//get years
		//start spinner
		$('#timetableLoadingYear').show();		
		//Ajax for calling php function
		$.post('api/getCalendarYears_timetable.php', { getCalYears: 'GetALLYears'}, function(data){		
		//alert(data);
		$('#timeTableFormYear').html(''); // clear
			var count =0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#timeTableFormYear').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			//stop spinner
			$('#timetableLoadingYear').hide();
			if (count === 0){
				canSubmit = false;
				//no data
				$('#timeTableFormYear').append($('<option>', { 
					value: 'no_data',
					text : 'no timetable data' 
				}));
				$('#timeTableFormYear').prop('disabled',true);
			}else{
				selectedYear = $('#timeTableFormYear').val(); //this will get the value of first item from list.
			}
			//Now populate courses
			populateSemesters();			
		});
	}
	function populateSemesters(){
		//Ajax for calling php function
		//start spinner
		$('#timetableLoadingSemester').show();				
		//console.log(courseYear);
		$.post('api/getSemestersByYear_timetable.php', { getSemestersByYearCourse: 'getSemestersByYearCourse'}, function(data){		
			//alert(data);
			//console.log(data);
			$('#timeTableFormSemester').html(''); // clear
			count = 0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#timeTableFormSemester').append($('<option>', { 
					value: v.Semester,
					text : v.Semester 
				}));
				count++;
			});
			$('#timetableLoadingSemester').hide();
			if (count === 0){
				//no data
				canSubmit = false;
				$('#timeTableFormSemester').append($('<option>', { 
					value: null,
					text : 'No Data' 
				}));
				$('#timeTableFormSemester').prop('disabled',true);			
			}else{
				selectedYear = $('#timeTableFormYear').val();
				selectedSemester = $('#timeTableFormSemester').val(); //this will get the value of first item from list.	
			}	
			populateCourses(selectedSemester);			
		});		
	}
	function populateCourses(semester){
		//get courses
		//start spinner
		$('#timetableLoadingCourse').show();				
		//Ajax for calling php function
		//console.log(semester);
		$.post('api/getCoursesBySemester_timetable.php', { getCoursesByYear: 'getCoursesByGivenYear', semester:semester}, function(data){		
			//alert(data);
			//console.log(data);
			$('#timeTableFormCourse').html(''); // clear
			count = 0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#timeTableFormCourse').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			$('#timetableLoadingCourse').hide();
			if (count === 0){
				//no data
				canSubmit = false;
				$('#timeTableFormCourse').append($('<option>', { 
					value: null,
					text : 'No Data' 
				}));
				$('#timeTableFormCourse').prop('disabled',true);			
			}
			selectedYear = $('#timeTableFormYear').val();
			selectedSemester = $('#timeTableFormSemester').val(); //this will get the value of first item from list.
			selectedCourse = $('#timeTableFormCourse').val(); //this will get the value of first item from list.
			//good to submit
		});
	}	
	
	function showForm(){
		//$('#timeTable div#timeTableForm').removeAttr('hidden');
		$('#timeTable div#timeTableForm').fadeIn(100);
		$('#timeTable div#timeTableView').attr('hidden','true');		
	}
	function showSpecialisations(){
		//$('#timeTable div#timeTableForm').attr('hidden','true');
		$('#timeTable div#timeTableForm').hide();
		$('#timeTable div#timeTableView').removeAttr('hidden');	
		$('#timeTable div#AllTimeTables').hide();
		$('#timeTable div#AllSpecialisations').fadeIn(100);
		$('#timeTable button#backBtnTimeTable').fadeIn();				
	}
	function showTimeTable(){
		$('div#AllSpecialisations').hide();
		$('#timeTable div#AllTimeTables').fadeIn(100);
		$('#timeTable button#backBtnTimeTable').hide();				
	}
/*
	function hideTimeTable(){
		$('#timeTable2 div#timeTableForm').attr('hidden','true');
	}
*/	

	$('#timeTableFormSubmit').on('click',function(e){
	//document.querySelector('#timeTableFormSubmit').addEventListener('click',function(e){
		//$(".btnSeccion").click(function(event) {
		e.preventDefault();

		if (!canSubmit){
			$('#timetableOutput').html('Incomplete data to generate timetables');
			return false;
		}
		allSpecs=[];
		$('div#AllSpecialisations').html('');
		$('div#AllSpecialisations').empty();
		//set variables to latest values
		resetVars();
		//sending ajax request
		$.post('api/getSpecs_timetable.php', { selectedYear:selectedYear, selectedCourse: selectedCourse, selectedSemester: selectedSemester}, function(data){		
			//alert(data);
			//console.log(data);
			$.each(JSON.parse(data), function(k, v) {
				//console.log(v);
				allSpecs.push({
					Specialisation: refineStr(v.Specialisation),
					Initials: v.Initials,
					Status: v.Status,
					color:generateRandomColor()
				});

			});
			//generate HTML
			var specs_div = $('#timeTable div#AllSpecialisations');
			$('#timeTable div#AllSpecialisations').html('');
			$('#timeTable div#AllSpecialisations').empty();
			document.getElementById('AllSpecialisations').innerHTML = "";
			var template = document.getElementById('specTemplate').innerHTML;
			var tline = selectedYear+' S'+selectedSemester+' Specialisations for '+selectedCourse; 
			var html = Mustache.to_html(template, { specialisations: allSpecs ,titleline:tline});
			specs_div.append(html);	
			//display specialisations 
			showSpecialisations();				
		});
	
		return false;
	});
	$(document).on("click","#displayTimetables",function(){
		//console.log($(this).attr('name'));
		var thisspecialisation = $(this).attr('name');
		var units = [];
		//now get the units data for this specialisation from server.
		var timetables = [];
		resetVars();//this will set the variables to latest values.
		$.post('api/getTimeTables_timetable.php',{selectedYear:selectedYear, selectedCourse: selectedCourse, selectedSemester: selectedSemester, selectedSpecialisation:unRefineStr(thisspecialisation)},function(data){
			//console.log(data);
		    try {
				timetables = [];
				timetables = JSON.parse(data);
				
				//console.log(timetables);
				var template = document.getElementById('timeTableTemplate').innerHTML;
				var tline = thisspecialisation;
				var html = Mustache.to_html(template,{SpecTitle:tline,timetables:timetables,Course:selectedCourse,Year:selectedYear,Semester:selectedSemester});
				var timetable = $('#timeTable div#AllTimeTables');
				timetable.html(html);				
				showTimeTable();
			} catch (e) {
				console.log("Unexpected response from server!");
				console.log(e);
			}

		});		
	});	
	$(document).on("click","#backToSpecs",function(){
		//console.log("Back pressed to ");
		showSpecialisations();
	});
	/*
	*Generate random colors
	*/
	function generateRandomColor(){
    	var light = 0.8;
		
		while (light>0.70) 
		{
  			var x = Math.floor(Math.random() * 256);
    		var y = Math.floor(Math.random() * 256);
    		var z = Math.floor(Math.random() * 256);
    		var xd = x/255;
    		var yd = y/255;
    		var zd = z/255;

    		var cMax = Math.max(xd, yd, zd);
    		var cMin = Math.min(xd, yd, zd);
    		light = (cMax + cMin)/2;
    		
    		var randomColor = "rgb(" + x + "," + y + "," + z + ")";
		}
		   			    			
    	return randomColor;
    			//random color will be freshly served
	}	
	/*
	*reseting the values
	*/
	function resetVars(){
		selectedYear = $('#timeTableFormYear').val();
		selectedCourse = $('#timeTableFormCourse').val(); 
		selectedSemester = $('#timeTableFormSemester').val(); 
	}
	/*
	*To replace and in a string with &
	*/
	function refineStr(str){
		var res = str.replace(" and ", " & ");
		return res;
	}
	/*
	*To replace & in a string with and
	*/
	function unRefineStr(str){
		var res = str.replace(" & ", " and ");
		return res;
	}	
});