<?php
require "../config/db.php";
require "../vendor/tcpdf/tcpdf.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT s.*, b.name AS box_name
    FROM shipments s
    JOIN boxes b ON s.box_id = b.id
    WHERE s.id = ?
");
$stmt->execute([$id]);
$shipment = $stmt->fetch();

if (!$shipment) {
    die("Shipment not found. Create a shipment first.");
}

$itemsStmt = $pdo->prepare("SELECT * FROM items WHERE shipment_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont("helvetica", "", 12);

$pdf->Write(0, "Packing List\n\n");
$pdf->Write(0, "Box: {$shipment['box_name']}\n");
$pdf->Write(0, "Total Weight: {$shipment['total_weight']} kg\n");
$pdf->Write(0, "Shipping Cost: {$shipment['shipping_cost']} €\n\n");

foreach ($items as $item) {
    $fragile = $item['fragile'] ? " (Fragile)" : "";
    $pdf->Write(0, "- {$item['name']} ({$item['weight']} kg){$fragile}\n");
}

$pdf->Output("packing_list.pdf", "I");
