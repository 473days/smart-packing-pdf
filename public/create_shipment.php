<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "../config/db.php";

$items = $_POST['items'];

$totalWeight = 0;
$totalVolume = 0;

foreach ($items as $item) {
    $totalWeight += $item['weight'];
    $totalVolume += $item['length'] * $item['width'] * $item['height'];
}

// pick smallest suitable box
$stmt = $pdo->query("SELECT * FROM boxes ORDER BY (length*width*height) ASC");
$box = null;

while ($b = $stmt->fetch()) {
    $boxVolume = $b['length'] * $b['width'] * $b['height'];
    if ($boxVolume >= $totalVolume && $b['max_weight'] >= $totalWeight) {
        $box = $b;
        break;
    }
}

if (!$box) {
    die("No suitable box found for this shipment. Check dimensions or weight.");
}


// mock shipping cost
$shippingCost = 5 + ($totalWeight * 1.5);

// store shipment
$stmt = $pdo->prepare("
    INSERT INTO shipments (total_weight, total_volume, box_id, shipping_cost)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$totalWeight, $totalVolume, $box['id'], $shippingCost]);
$shipmentId = $pdo->lastInsertId();

// store items
$stmt = $pdo->prepare("
    INSERT INTO items (shipment_id, name, length, width, height, weight, fragile)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

foreach ($items as $item) {
    $stmt->execute([
        $shipmentId,
        $item['name'],
        $item['length'],
        $item['width'],
        $item['height'],
        $item['weight'],
        isset($item['fragile']) ? 1 : 0
    ]);
}

header("Location: generate_pdf.php?id=" . $shipmentId);
