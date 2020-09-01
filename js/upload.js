 $('div#alertsuccess').addClass('collapse');
 $('div#alertfail').addClass('collapse');
 //localStorage.clear();
 document.getElementById('uploadForm').reset();
$('button#submitFile').click(function(e){
	e.preventDefault();		
	//var fileType = $("input[name='fileTypeRadio']:checked").val(); //10-May-20 client asked to remove radio box
	var myform = document.getElementById('uploadForm');
	if (myform.checkValidity() === false) {
	  //event.preventDefault();
	  e.stopPropagation();
	  myform.classList.add('was-validated');
	}else{
		myform.classList.remove('was-validated');
		//var myFile = $('#fileinput').prop('files');
		var ajaxurl = 'api/uploadFile.php';
		var myFile = $('#userFile').prop('files')[0];
		var form_data = new FormData();
		form_data.append('file',myFile);
		//form_data.append('fileType',fileType);
		form_data.append('fileAction','upload');
		$('button#submitFile').attr('disabled',true);
		$('#waitingSpin').removeClass('d-none');
		$.ajax({
			url: ajaxurl, // point to server-side PHP script 
			dataType: 'text',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(response){
				//alert(response); // display response from the PHP script, if any
				if (response.indexOf('Success!') >= 0){
					$('div#alertsuccess').show(500);
					$('div#alertsuccess').removeClass('collapse');
					$('div#alertfail').addClass('collapse');
					$('div#alertfail').hide();
					$('div#alertsuccess').delay(15000).hide(1000);
					populateFiles();
					populateTimeTableFiles();
				}else{
					$('div#alertfail').show(400);
					$('div#alertsuccess').addClass('collapse');
					$('div#alertfail').removeClass('collapse');
					$('div#alertfail').html('<button type="button" class="close" data-dismiss="alert">&times;</button><strong>Error!</strong>'+response);
				}
				$('button#submitFile').removeAttr('disabled');
				$('#waitingSpin').addClass('d-none');
			},
			error: function(XMLHttpRequest, textStatus, errorThrown){
				//alert('Error in ajax server request!');
				$('button#submitFile').removeAttr('disabled');
				$('#waitingSpin').addClass('d-none');
				$('div#alertsuccess').addClass('collapse');
				$('div#alertfail').removeClass('collapse');
				$('div#alertfail').html('<button type="button" class="close" data-dismiss="alert">&times;</button><strong>Warning!</strong>'+textStatus+errorThrown);
			}
		});	
	}		
	
});
$('#waitingSpin').addClass('d-none');
$('#userFile').on('change',function(e){
	//get the file name
	var fileName = e.target.files[0].name;
	//replace the "Choose a file" label
	$(this).next('.custom-file-label').text(fileName);
});

populateFiles();
populateTimeTableFiles();
function populateFiles(){
	$('#filesListTable > tbody').empty();
	$.ajax({
	url: 'api/getFilesInfo.php', // point to server-side PHP script 
	dataType: 'text',  // what to expect back from the PHP script, if anything
	data: {getAllCourses: 'getThis'},                         
	type: 'post',
	success: function(response){		
		var cc =1;
		$.each(JSON.parse(response), function(k,v){
			var t = v.DateUploaded.split(/[- :]/);
			// Apply each element to the Date function
			var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
			d=new Date(d.toLocaleString("en-US", {timeZone: "Australia/Brisbane"}));
			var dt_str = d.toLocaleString();
			var downloadbtn = "<button type='submit' onclick='downloadFile(\""+v.FileName+"\")' class='btn btn-primary'>Download</button>";
			var deletebtn = "<button type='submit' class='btn btn-secondary' onclick='deleteFile(\""+v.FileName+"\",\""+v.FileYear+"\",\""+v.DateUploaded+"\",\"studyplan\")'  data-toggle='modal' data-target='#deleteModal'>Delete</button>";
			$('#filesListTable > tbody:last-child').append('<tr><th scope="row">'+cc+'</th><td>'+v.Course+'</td><td>'+v.FileYear+'</td><td>'+v.FileName+'</td><td>'+dt_str+'</td><td>'+downloadbtn+' '+deletebtn+'</td></tr>');
			cc++;
		});
	},
	error: function(XMLHttpRequest, textStatus, errorThrown){
		//alert('Error in ajax server request!');
		console.log('Error in ajax server request!');
	}
	});	
}

function populateTimeTableFiles(){
	$('#listFilesTimeTable > tbody').empty();
	$.ajax({
	url: 'api/getTimeTableFilesInfo.php', // point to server-side PHP script 
	dataType: 'text',  // what to expect back from the PHP script, if anything
	data: {getUnitFiles: 'getThis'},                         
	type: 'post',
	success: function(response){
		var cc =1;
		$.each(JSON.parse(response), function(k,v){
			var t = v.DateUploaded.split(/[- :]/);
			// Apply each element to the Date function
			var d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
			d=new Date(d.toLocaleString("en-US", {timeZone: "Australia/Brisbane"}));
			var dt_str = d.toLocaleString();
			var downloadbtn = "<button type='submit' onclick='downloadFile(\""+v.FileName+"\")' class='btn btn-primary'>Download</button>";
			var deletebtn = "<button type='submit' class='btn btn-secondary' onclick='deleteFile(\""+v.FileName+"\",\""+v.FileYear+"\",\""+v.DateUploaded+"\", \"timetable\")'  data-toggle='modal' data-target='#deleteModal'>Delete</button>";
			$('#listFilesTimeTable > tbody:last-child').append('<tr><th scope="row">'+cc+'</th><td>'+v.FileYear+'</td><td>'+v.FileName+'</td><td>'+dt_str+'</td><td>'+downloadbtn+' '+deletebtn+'</td></tr>');
			cc++;
		});
	},
	error: function(XMLHttpRequest, textStatus, errorThrown){
		//alert('Error in ajax server request!');
		console.log('Error in ajax server request!');
	}
	});	
}
function downloadFile(filename){
	//alert('Pressed '+filename);
    window.location.href = 'uploads/'+filename;
}
var fileToDelete = null;
var fileToDeleteYear = null;
var fileToDeleteDate = null;
var fileToDeleteType = null;
function deleteFile(filename,year,date,filetype){
	$('button#confirmDeleteBtn').removeAttr('disabled'); //enable button if already disabled
	$('button#confirmDeleteBtn').show(); //display button if already hidden
	$('#deleteModalBody').html('This action can\'t be undone!');
	//alert(filename);
	//bootbox.confirm(message, callback)
	fileToDelete = filename;
	fileToDeleteYear = year;
	fileToDeleteDate = date;
	fileToDeleteType = filetype;
}
function confirmDelete(){
	$('button#confirmDeleteBtn').attr('disabled',true); // disable button until ajax is completed
	//initiate delete
	$('#deleteModalBody').html('Deleting...');
	$.ajax({
	url: 'api/deleteFile.php', // point to server-side PHP script 
	dataType: 'text',  // what to expect back from the PHP script, if anything
	data: {deleteFile: 'getThis', file: fileToDelete, year: fileToDeleteYear, date: fileToDeleteDate,type:fileToDeleteType},                         
	type: 'post',
	success: function(response){
		console.log(response);
		$('#deleteModalBody').html(response);
		$('button#confirmDeleteBtn').hide(); //Hide button
		populateFiles();
		populateTimeTableFiles();
	},
	error: function(XMLHttpRequest, textStatus, errorThrown){
		//alert('Error in ajax server request!');
		$('button#confirmDeleteBtn').removeAttr('disabled'); // enable button again.
		console.log('Error in ajax server request!');
		$('#deleteModalBody').html('Error in ajax server request!');
	}
	});	
}
function closeDelete(changed){
	if (changed === true){
		populateFiles();
	}else{
		
	}
}