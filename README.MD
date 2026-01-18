# Smart Packing List Web App

A PHP-based logistics web application that allows users to create shipments, calculate volume and weight, select suitable box sizes, and generate PDF packing lists.

## Tech Stack
- PHP (PDO)
- MySQL
- JavaScript (dynamic forms)
- HTML / CSS
- TCPDF (PDF generation)

## Features
- Create shipments with multiple items
- Automatic volume and weight calculation
- Box selection based on constraints
- PDF packing list generation
- Relational database design

## Setup (XAMPP)

1. Clone the repository
2. Place it in `htdocs`
3. Create a MySQL database named `smart_packing`
4. Import `db/schema.sql`
5. Download TCPDF from https://github.com/tecnickcom/TCPDF
6. Place TCPDF in `vendor/tcpdf`
7. Start Apache and MySQL
8. Open `http://localhost/smart-packing/public`

## Notes
This project is intended as an educational and portfolio project.
