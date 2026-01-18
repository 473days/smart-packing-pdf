<?php
require "../config/db.php";

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $items = $_POST['item_name'] ?? [];
    $weights = $_POST['item_weight'] ?? [];
    $lengths = $_POST['item_length'] ?? [];
    $widths = $_POST['item_width'] ?? [];
    $heights = $_POST['item_height'] ?? [];
    $fragiles = $_POST['item_fragile'] ?? [];

    if (count($items) === 0 || empty($items[0])) {
        $errorMessage = "Please add at least one item.";
    } else {
        // Calculate total weight (simplified)
        $totalWeight = 0;
        foreach ($weights as $w) {
            $totalWeight += floatval($w);
        }

        // TODO: Select a suitable box based on dimensions & weight
        // For now, pick first box
        $boxStmt = $pdo->query("SELECT * FROM boxes LIMIT 1");
        $box = $boxStmt->fetch(PDO::FETCH_ASSOC);

        if (!$box) {
            $errorMessage = "No boxes available. Add boxes first.";
        } else {
            // Insert shipment
            $stmt = $pdo->prepare("INSERT INTO shipments (box_id, total_weight, shipping_cost) VALUES (?, ?, ?)");
            $shippingCost = $totalWeight * 1.5; // mock calculation
            $stmt->execute([$box['id'], $totalWeight, $shippingCost]);
            $shipmentId = $pdo->lastInsertId();

            // Insert items
            $itemStmt = $pdo->prepare("INSERT INTO items (shipment_id, name, weight, length, width, height, fragile) VALUES (?, ?, ?, ?, ?, ?, ?)");
            foreach ($items as $i => $name) {
                $itemStmt->execute([
                    $shipmentId,
                    $name,
                    floatval($weights[$i]),
                    floatval($lengths[$i]),
                    floatval($widths[$i]),
                    floatval($heights[$i]),
                    isset($fragiles[$i]) ? 1 : 0
                ]);
            }

            $successMessage = "Shipment created successfully! <a href='generate_pdf.php?id=$shipmentId' target='_blank'>View PDF</a>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Shipment</title>
<style>
body { font-family: Arial, sans-serif; padding: 20px; }
.card { background: #f9f9f9; padding: 20px; border-radius: 8px; max-width: 900px; margin: auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
.item-row { display: grid; grid-template-columns: repeat(6, 1fr) auto; gap: 5px; margin-bottom: 5px; }
button { cursor: pointer; }
#add-item { margin-top: 10px; padding: 5px 15px; background-color: #4CAF50; color: white; border: none; }
#add-item:hover { background-color: #45a049; }
.alert { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
.alert.success { background-color: #d4edda; color: #155724; }
.alert.error { background-color: #f8d7da; color: #721c24; }
</style>
</head>
<body>

<div class="card">
<h2>Create New Shipment</h2>

<?php if($successMessage): ?>
<div class="alert success"><?= $successMessage ?></div>
<?php endif; ?>
<?php if($errorMessage): ?>
<div class="alert error"><?= $errorMessage ?></div>
<?php endif; ?>

<form method="post" id="shipment-form">
  <div id="items-container">
    <div class="item-row">
      <input type="text" name="item_name[]" placeholder="Item name" required>
      <input type="number" name="item_weight[]" placeholder="Weight (kg)" step="0.01" required>
      <input type="number" name="item_length[]" placeholder="Length (cm)" required>
      <input type="number" name="item_width[]" placeholder="Width (cm)" required>
      <input type="number" name="item_height[]" placeholder="Height (cm)" required>
      <input type="checkbox" name="item_fragile[]"> Fragile
      <button type="button" class="remove-item">Remove</button>
    </div>
  </div>

  <button type="button" id="add-item">Add Item</button>

  <div style="margin-top:10px;">
    <p>Total Weight: <span id="total-weight">0 kg</span></p>
    <p>Total Volume: <span id="total-volume">0 cm³</span></p>
  </div>

  <button type="submit" style="margin-top:10px; padding:7px 20px; background-color:#007BFF; color:white; border:none; border-radius:5px;">Create Shipment</button>
</form>
</div>

<script>
function updateTotals() {
    let totalWeight = 0, totalVolume = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const w = parseFloat(row.querySelector('input[name="item_weight[]"]').value) || 0;
        const l = parseFloat(row.querySelector('input[name="item_length[]"]').value) || 0;
        const wi = parseFloat(row.querySelector('input[name="item_width[]"]').value) || 0;
        const h = parseFloat(row.querySelector('input[name="item_height[]"]').value) || 0;
        totalWeight += w;
        totalVolume += l*wi*h;
    });
    document.getElementById('total-weight').textContent = totalWeight.toFixed(2)+' kg';
    document.getElementById('total-volume').textContent = totalVolume.toFixed(2)+' cm³';
}

document.getElementById('items-container').addEventListener('input', updateTotals);
updateTotals();

document.getElementById('add-item').addEventListener('click', () => {
    const container = document.getElementById('items-container');
    const row = container.firstElementChild.cloneNode(true);
    row.querySelectorAll('input').forEach(input => {
        if(input.type === 'text' || input.type === 'number') input.value = '';
        if(input.type === 'checkbox') input.checked = false;
    });
    container.appendChild(row);
});

document.getElementById('items-container').addEventListener('click', e => {
    if(e.target.classList.contains('remove-item')) {
        const rows = document.querySelectorAll('.item-row');
        if(rows.length > 1) e.target.closest('.item-row').remove();
    }
});
</script>

</body>
</html>
