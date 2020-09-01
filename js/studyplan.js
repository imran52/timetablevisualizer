$(document).ready(function(){
	//hide table at start
	$('div#studyPlanTable').hide();
	$('div#studyPlanTable').addClass('d-none');
	//generate year, course, specialisation and intake data.
	var selectedYear = null;
	var selectedCourse = null;
	var selectedSpecialisation = null;
	var selectedIntake = null;
	var canSubmit = true;
	//Populate years
	populateCalendarYears();
	//When submit is pressed
	$('button#studyPlanFormSubmit').on('click',function(e){
		e.preventDefault();
		if (!canSubmit){
			
			$('p#studyPlanOutput').html('Incomplete data to generate study plan.');
			return false;
		}

		//Ajax for calling php function
		$.post('api/getUnitsForStudyPlan.php', { course: selectedCourse, year: selectedYear,specialisation:selectedSpecialisation,intake:selectedIntake}, function(data){		
		//alert(data);
		//console.log(data);

		
		$('#tables').html(''); // clear
		
		var rowcount = 0;
		$.each(JSON.parse(data), function(k, v){			
			if ($('div#semester_'+v.Semester+'_'+v.YearNo).length){
				//div and table for this year,semester exists
				addTableRow(v,rowcount);				
			}else{
				rowcount = 0;
				createTableDiv(v,rowcount); //creating table and div				
			}
			rowcount++;
		});
		});
		//Visibility changing
		$('#studyPlanCourseInfo').show();
		$('#studyPlanCourseInfo').html(selectedCourse+' ('+selectedSpecialisation+')');
		$('#getPdfBtn').show();
		$('#backBtn').show();
		$('form#studyPlanForm').hide();
		$('div#studyPlanTable').fadeIn(150);
		$('div#studyPlanTable').removeClass('d-none');		
	});
	function createTableDiv(value,rowcount_){
		//create div for this year, semester.
		var tablesdiv = $('div#tables'); // get studyplan div
		var tablediv = document.createElement('div');
		tablediv.id = 'semester_'+value.Semester+'_'+value.YearNo;
		var titleh4 = document.createElement('h5');
		var yeartodisplay = selectedYear;
		if (value.YearNo ==2){
			yeartodisplay++;
		}
		if (value.YearNo == 3){
			yeartodisplay++;
			yeartodisplay++;
		}
		titleh4.innerHTML = yeartodisplay+" Semester "+value.Semester;
		// build the table
		var content = '<table id="table_semester'+value.Semester+'_'+value.YearNo+'" class="table table-sm">';
			content += '<thead><tr class="d-flex text-white">';
			content += '<th class="col-6 border-0 bg-primary">Unit Code</th>';
			content += '<th  class="col-6 border-0 bg-primary">Unit Title</th>';
			content += '</tr></thead><tbody>';

		content += '</tbody></table>';		
		
		
		tablediv.append(titleh4);
		//tablediv.append(content);
		tablesdiv.append(tablediv);	
		//console.log('div#semester_'+value.Semester+'_'+value.YearNo);
		$('div#semester_'+value.Semester+'_'+value.YearNo).append(content);
		addTableRow(value,rowcount_);//now add row
	}
	function addTableRow(value,rowcount_){
		var tableelement = $('table#table_semester'+value.Semester+'_'+value.YearNo);
		var trelement = document.createElement('tr');
		trelement.classList.add('d-flex');
		//trelement.classList.add('d-flex table-primary');
		var rowNum = parseInt(rowcount_);
		//console.log(rowNum%2);
		if ((rowNum-1)%2 === 0){
			trelement.classList.add('table-primary');
		}
		var tdelement1 = document.createElement('td');
		tdelement1.classList.add('col-6');
		tdelement1.innerHTML = value.UnitCode;

		var tdelement2 = document.createElement('td');		
		tdelement2.classList.add('col-6');
		tdelement2.innerHTML = value.UnitTitle;
		
		trelement.append(tdelement1);
		trelement.append(tdelement2);
		tableelement.append(trelement);
	}
	//when back is pressed
	$('button#backBtn').on('click',function(e){
		e.preventDefault();
		$('#studyPlanCourseInfo').hide();
		$('form#studyPlanForm').fadeIn(150);
		$('div#studyPlanTable').hide();
		$('div#studyPlanTable').addClass('d-none');
		$('#getPdfBtn').hide();
		$('#backBtn').hide();		
		//generate tables.
	});

	//when download pdf is pressed
	$('button#getPdfBtn').on('click',function(e){
		e.preventDefault();
		var style = '<link type="text/css" href="../css/pdf.css" rel="stylesheet" />';
		var tString = '<h4>'+selectedCourse+' '+selectedSpecialisation+' '+selectedYear+' Intake '+selectedIntake+' Study Plan </h4>';
		var pdfhtml = style+tString+document.getElementById('tables').innerHTML;
		$.redirect('api/getPDF.php', {getPDF: 'GetPDF',htmlcode:pdfhtml,selectedCourse:selectedCourse,selectedYear:selectedYear});		

	});	
	
	$('#studyPlanFormYear').change(function(){		
		selectedYear = $('#studyPlanFormYear').val();
		//regenerate courses
		populateCourses(selectedYear);
	});
	$('#studyPlanFormCourse').change(function(){
		selectedYear = $('#studyPlanFormYear').val();
		selectedCourse = $('#studyPlanFormCourse').val();
		//regenerate Specialisations
		populateSpecialisations(selectedYear,selectedCourse);
	});
	$('#studyPlanFormSpec').change(function(){
		selectedYear = $('#studyPlanFormYear').val();
		selectedCourse = $('#studyPlanFormCourse').val();
		selectedSpecialisation = $('#studyPlanFormSpec').val();
		//regenerate intakes
		populateIntakes(selectedYear,selectedCourse,selectedSpecialisation);
	});
	$('#studyPlanFormIntake').change(function(){
		selectedIntake = $('#studyPlanFormIntake').val();
	});
	
	function populateCalendarYears(){
		//get years
		//Ajax for calling php function
		$('#studyPlanLoadingYear').show();
		$.post('api/getCalendarYears.php', { getCalYears: 'GetALLYears'}, function(data){		
			//alert(data);
			$('#studyPlanFormYear').html(''); // clear
			count=0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#studyPlanFormYear').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			if (count === 0){
				//no data
				canSubmit = false;
				$('#studyPlanFormYear').append($('<option>', { 
					value: 'no_data',
					text : 'No Data' 
				}));
				$('#studyPlanFormYear').prop('disabled',true);
			}				
			$('#studyPlanLoadingYear').hide();
			selectedYear = $('#studyPlanFormYear').val(); //this will get the value of first item from list.
			populateCourses(selectedYear);
			//Now populate courses
		});
	}
	function populateCourses(courseYear){
		//Ajax for calling php function
		//console.log(courseYear);
		$('#studyPlanLoadingCourse').show();
		$.post('api/getCoursesByYear.php', { getCoursesByYear: 'getCoursesByGivenYear', givenYear: courseYear}, function(data){		
			//alert(data);
			//console.log(data);
			$('#studyPlanFormCourse').html(''); // clear
			count =0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#studyPlanFormCourse').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			if (count === 0){
				//no data
				canSubmit = false;
				$('#studyPlanFormCourse').append($('<option>', { 
					value: 'no_data',
					text : 'No Data' 
				}));
				$('#studyPlanFormCourse').prop('disabled',true);
			}			
			$('#studyPlanLoadingCourse').hide();
			selectedYear = $('#studyPlanFormYear').val();
			selectedCourse = $('#studyPlanFormCourse').val(); //this will get the value of first item from list.
			//Now populate Specialisations
			populateSpecialisations(selectedYear,selectedCourse);
		});
	}	
	function populateSpecialisations(courseYear,course){
		//get years
		//Ajax for calling php function
		//console.log(courseYear);
		$('#studyPlanLoadingSpec').show();
		$.post('api/getSpecialisations.php', { course: course, year: courseYear}, function(data){		
			//alert(data);
			//console.log(data);
			$('#studyPlanFormSpec').html(''); // clear
			count = 0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#studyPlanFormSpec').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			if (count === 0){
				//no data
				canSubmit = false;
				$('#studyPlanFormSpec').append($('<option>', { 
					value: 'no_data',
					text : 'No Data' 
				}));
				$('#studyPlanFormSpec').prop('disabled',true);
			}				
			$('#studyPlanLoadingSpec').hide();
			selectedYear = $('#studyPlanFormYear').val();
			selectedCourse = $('#studyPlanFormCourse').val(); 
			selectedSpecialisation = $('#studyPlanFormSpec').val(); //this will get the value of first item from dropdown (which is selected when it is populated).
			//Now populate Intakes
			populateIntakes(selectedYear,selectedCourse,selectedSpecialisation);
		});		
	}
	function populateIntakes(courseYear,course,spec){
		//get years
		//Ajax for calling php function
		//console.log(courseYear);
		$('#studyPlanLoadingIntake').show();
		$.post('api/getIntakes.php', { course: course, year: courseYear,specialisation:spec}, function(data){		
			//alert(data);
			//console.log(data);
			$('#studyPlanFormIntake').html(''); // clear
			count = 0;
			$.each(JSON.parse(data), function(k, v) {
				/// do stuff
				//alert(v);
				$('#studyPlanFormIntake').append($('<option>', { 
					value: v,
					text : v 
				}));
				count++;
			});
			if (count === 0){
				//no data
				canSubmit = false;
				$('#studyPlanFormIntake').append($('<option>', { 
					value: 'no_data',
					text : 'No Data' 
				}));
				$('#studyPlanFormIntake').prop('disabled',true);
			}					
			$('#studyPlanLoadingIntake').hide();
			selectedYear = $('#studyPlanFormYear').val();
			selectedCourse = $('#studyPlanFormCourse').val(); 
			selectedSpecialisation = $('#studyPlanFormSpec').val(); //this will get the value of first item from dropdown (which is selected when it is populated).
			selectedIntake = $('#studyPlanFormIntake').val();
			//Have all the values now. Good for submit.
		});		
	}
});