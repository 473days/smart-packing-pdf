CREATE TABLE boxes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    length DECIMAL(10,2),
    width DECIMAL(10,2),
    height DECIMAL(10,2),
    max_weight DECIMAL(10,2)
);

CREATE TABLE shipments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_weight DECIMAL(10,2),
    total_volume DECIMAL(10,2),
    box_id INT,
    shipping_cost DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shipment_id INT,
    name VARCHAR(100),
    length DECIMAL(10,2),
    width DECIMAL(10,2),
    height DECIMAL(10,2),
    weight DECIMAL(10,2),
    fragile BOOLEAN
);

INSERT INTO boxes (name, length, width, height, max_weight) VALUES
('Small Box', 30, 30, 30, 5),
('Medium Box', 60, 40, 40, 15),
('Large Box', 100, 60, 60, 30);
