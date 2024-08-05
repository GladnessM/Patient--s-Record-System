<?php
// Include the database connection file
require_once 'db_connect.php';

// Check if the "id" parameter is set in the URL
if (!isset($_GET['id'])) {
    echo 'Error: No record ID provided.';
    exit;
}
// Get the record ID from the URL parameter
$record_id = mysqli_real_escape_string($mysqli, $_GET['id']);

// Get the record data from the database
$query = "SELECT * FROM patient_records WHERE id = '$record_id'";
$result = mysqli_query($mysqli, $query);
$record_data = mysqli_fetch_assoc($result);

// Create a PDF file using TCPDF library
//============================================================+
// File name   : example_011.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 011 for TCPDF class
//               Colored Table (very simple table)
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Colored Table
 * @author Nicola Asuni
 * @since 2008-03-04
 */

// Include the main TCPDF library (search for installation path).
require_once('TCPDF-main/tcpdf.php');

// extend TCPF with custom functions
class MYPDF extends TCPDF {

    // Load table data from file
    public function LoadData($record_id) {
        // Read file lines
        include 'db_connect.php';
        $query = "SELECT * FROM patient_records WHERE id = '$record_id'";
        $result = mysqli_query($mysqli, $query);
        $record_data = mysqli_fetch_assoc($result);
        return $record_data;

    
    // // Prepare the data for the PDF
    //  $data = array(
    //   array(
    //     "id" => $record_data['id'],
    //     "date" => $record_data['date'],
    //     "record_type" => $record_data['record_type'],
    //     "description" => $record_data['description']
    //   )
    // );
    // return $data;

    }
    // Colored table
    public function ColoredTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(40, 35, 40, 45);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row["id"], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row["date"], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row["record_type"], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row["description"], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }

}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 011');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

// column titles
$header = array('ID', 'Date', 'Record Type', 'Description)');

// data loading
$data = $pdf->LoadData('');

// print colored table
$pdf->ColoredTable($header, $data);

// ---------------------------------------------------------

// close and output PDF document
$pdf->Output('PDF.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>