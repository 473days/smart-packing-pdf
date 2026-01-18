let index = 0;

function addItem() {
    const div = document.createElement("div");
    div.className = "item";

    div.innerHTML = `
        <h3>Item ${index + 1}</h3>
        <input name="items[${index}][name]" placeholder="Name" required>
        <input name="items[${index}][length]" type="number" placeholder="Length (cm)" required>
        <input name="items[${index}][width]" type="number" placeholder="Width (cm)" required>
        <input name="items[${index}][height]" type="number" placeholder="Height (cm)" required>
        <input name="items[${index}][weight]" type="number" placeholder="Weight (kg)" required>
        Fragile <input type="checkbox" name="items[${index}][fragile]">
        <hr>
    `;

    document.getElementById("items").appendChild(div);
    index++;
}
