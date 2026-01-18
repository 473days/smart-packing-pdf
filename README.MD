# Smart Packing List Web App

<img width="940" height="591" alt="image" src="https://github.com/user-attachments/assets/5c010c14-84d3-496c-a542-400ed6d318e0" />
<img width="426" height="321" alt="image" src="https://github.com/user-attachments/assets/ce1f874c-6e20-45a8-82b9-7083af584ac5" />
<img width="930" height="688" alt="image" src="https://github.com/user-attachments/assets/454f439c-5457-4fe0-af1d-a5d5a8088d1b" />


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
