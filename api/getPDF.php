<?php
require '../vendor/autoload.php';
// reference the Dompdf namespace
use Dompdf\Dompdf;
if (isset($_POST['getPDF']) && isset($_POST['htmlcode'])){
	$selectedYear = $_POST["selectedYear"];
	$selectedCourse = $_POST["selectedCourse"];
	$htmlcode = $_POST['htmlcode'];

	// instantiate and use the dompdf class
	$dompdf = new Dompdf();
	$dompdf->loadHtml($htmlcode);
	
	//$dompdf->set_option('defaultFont', 'Arial');
	// Render the HTML as PDF
	$dompdf->render();
	// Output the generated PDF to Browser
	$dompdf->stream($selectedCourse."_".$selectedYear.".pdf", ["Attachment" => true]);
	
}else{
	echo "Invalid command";
}
?>