<?php
session_start();
require_once('TCPDF-main/tcpdf.php');
require_once ('db_connect.php');

if (isset($_SESSION['patient_id'])) {
   $patient_id= $_SESSION['patient_id'];
    
   $sql = "SELECT * FROM patient_records WHERE patient_id=?";
   $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    class MYPDF extends TCPDF {

            // Load table data from file
            public function LoadData($record_id) {
                // Read file lines
                include 'db_connect.php';
                $query = "SELECT * FROM patient_records WHERE id = '$record_id'";
                $result = mysqli_query($mysqli, $query);
                $record_data = mysqli_fetch_assoc($result);
                return $record_data;
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
                $w = array(40, 35, 40, 45, 45);
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
                $this->Cell($w[0], 6, $row["record_id"], 'LR', 0, 'L', $fill);
                $this->Cell($w[0], 6, $row["patient_id"], 'LR', 0, 'L', $fill);
                $this->Cell($w[1], 6, $row["record_type"], 'LR', 0, 'L', $fill);
                $this->Cell($w[2], 6, $row["record_date"], 'LR', 0, 'L', $fill);
                $this->Cell($w[3], 6, $row["record_details"], 'LR', 0, 'L', $fill);
                $this->Ln();
                $fill=!$fill;
            }
            $this->Cell(array_sum($w), 0, '', 'T');
        }
    }


    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Your Application Name');
    $pdf->SetTitle('Patient Records');
    $pdf->SetSubject('patient Records');
    $pdf->SetKeywords('TCPDF, PDF, patient, records');


    // Add a page
    $pdf->AddPage();

    // Set content for the PDF
    $html = '<h1>Patient Records</h1>';
    $html .= '<h2>Your records:</h2>';
    $html .= '<table border="1" cellspacing="3" cellpadding="4">';
    $html .= '<tr><th>Record ID</th><th>Patient ID</th><th>Record Type</th><th>Record Details</th><th>Record Date</th></tr>';

    while ($record = $result->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($record['record_id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($record['patient_id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($record['record_type']) . '</td>';
        $html .= '<td>' . htmlspecialchars($record['record_details']) . '</td>';
        $html .= '<td>' . htmlspecialchars($record['record_date']) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</table>';

    // Print text using writeHTMLCell()
    $pdf->writeHTML($html, true, false, true, false, '');

    //set default header data
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
    //set font
    $pdf->SetFont('helvetica', '', 12);

    // add a page
    $pdf->AddPage();

    // column titles
    $header = array('Record ID','Patient ID', 'Record Date', 'Record Type', 'Record Details)');

    
    // Output the PDF
    $pdf->Output('PDF.pdf', 'I');
}  else {
        echo "No records found.";
    }
?>