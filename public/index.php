<!DOCTYPE html>
<html>
<head>
    <title>Smart Packing List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Create Shipment</h1>

<form method="post" action="create_shipment.php" id="shipmentForm">
    <div id="items"></div>

    <button type="button" onclick="addItem()">Add Item</button>
    <br><br>
    <button type="submit">Create Shipment</button>
</form>

<script src="script.js"></script>
</body>
</html>
