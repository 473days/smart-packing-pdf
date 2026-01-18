<?php
// Avoid any output before TCPDF
ob_start();

require "../config/db.php";
require "../vendor/tcpdf/tcpdf.php";

// Get shipment ID safely
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid shipment ID.");
}

// Fetch shipment with its box
$stmt = $pdo->prepare("
    SELECT s.*, b.name AS box_name, b.length AS box_length, b.width AS box_width, b.height AS box_height
    FROM shipments s
    JOIN boxes b ON s.box_id = b.id
    WHERE s.id = ?
");
$stmt->execute([$id]);
$shipment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$shipment) {
    die("Shipment not found. Create a shipment first.");
}

// Fetch shipment items
$itemsStmt = $pdo->prepare("SELECT * FROM items WHERE shipment_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

// Create TCPDF object
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Document metadata
$pdf->SetCreator('SmartPack Logistics System');
$pdf->SetAuthor('SmartPack Logistics GmbH');
$pdf->SetTitle('Packing List');

// Margins
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// COMPANY HEADER
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 8, 'SmartPack Logistics GmbH', 0, 1);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Logistikstraße 12, 80335 München, Germany', 0, 1);
$pdf->Cell(0, 5, 'Tel: +49 89 1234567 | Fax: +49 89 1234568', 0, 1);
$pdf->Cell(0, 5, 'dispatch@smartpack-logistics.de | VAT ID: DE123456789', 0, 1);
$pdf->Ln(6);

// DOCUMENT TITLE
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'PACKING LIST', 0, 1, 'C');
$pdf->Ln(4);

// SHIPMENT INFO
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 6, 'Shipment ID:', 0, 0);
$pdf->Cell(50, 6, $shipment['id'], 0, 0);
$pdf->Cell(40, 6, 'Date:', 0, 0);
$pdf->Cell(0, 6, date('d.m.Y'), 0, 1);
$pdf->Ln(3);

// ITEMS TABLE HEADER
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(60, 7, 'Item', 1);
$pdf->Cell(30, 7, 'Dimensions (cm)', 1);
$pdf->Cell(25, 7, 'Weight (kg)', 1);
$pdf->Cell(25, 7, 'Fragile', 1);
$pdf->Ln();

// ITEMS TABLE ROWS
$pdf->SetFont('helvetica', '', 10);
if ($items) {
    foreach ($items as $item) {
        $pdf->Cell(60, 7, $item['name'] ?? '-', 1);
        $dims = "{$item['length']}x{$item['width']}x{$item['height']}";
        $pdf->Cell(30, 7, $dims, 1);
        $pdf->Cell(25, 7, number_format($item['weight'] ?? 0, 2), 1);
        $pdf->Cell(25, 7, !empty($item['fragile']) ? 'Yes' : 'No', 1);
        $pdf->Ln();
    }
} else {
    $pdf->Cell(140, 7, 'No items in this shipment.', 1, 1, 'C');
}

// BOX & TOTAL SUMMARY
$pdf->Ln(6);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 7, 'Packing Summary', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Recommended Box: ' . ($shipment['box_name'] ?? '-'), 0, 1);
$pdf->Cell(0, 6, 'Box Dimensions: ' . ($shipment['box_length'] ?? '-') . 'x' . ($shipment['box_width'] ?? '-') . 'x' . ($shipment['box_height'] ?? '-') . ' cm', 0, 1);
$pdf->Cell(0, 6, 'Total Weight: ' . number_format($shipment['total_weight'] ?? 0, 2) . ' kg', 0, 1);
$pdf->Cell(0, 6, 'Shipping Cost: ' . number_format($shipment['shipping_cost'] ?? 0, 2) . ' €', 0, 1);

// FOOTER
$pdf->SetY(-30);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 5, 'This document was generated automatically by the SmartPack Logistics System.', 0, 1, 'C');
$pdf->Cell(0, 5, 'No signature required.', 0, 1, 'C');

// OUTPUT PDF
$pdf->Output("packing_list_{$shipment['id']}.pdf", "I");
