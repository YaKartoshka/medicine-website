<?php
// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicine";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve products from the database
$sql = "SELECT * FROM medical_services";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Учет и управление медицинскими услугами в поликлинике</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/1533/1533792.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-sm bg-success text-white navbar-light">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="/nazerke/">NazMedicine</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link text-white" href="/nazerke/">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/nazerke/appointment.php">Записаться</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/nazerke/request.php">Оставить запрос</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="container intro w-75 pb-5" data-aos="zoom-in">
            <div class="content">
                <h1>Учет и управление медицинскими услугами в поликлинике</h1>
                <p>Система "Учет и управление медицинскими услугами в поликлинике" разработана для эффективного
                     управления медицинскими процессами, обеспечения качественного предоставления
                     медицинских услуг пациентам и сбора необходимой информации для внутреннего анализа и отчетности.</p>
                <a href="/nazerke/appointment.php"><button type="button" class="btn btn-success">Записаться</button></a>
            </div>
            <img src="https://cdn-icons-png.flaticon.com/512/1533/1533792.png" width="350">
        </div>
        <div id="services" class="container mt-5 d-flex pb-5 flex-wrap justify-content-center" data-aos="zoom-in">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="service rounded">';
                    echo '<img src="https://cdn-icons-png.flaticon.com/512/2858/2858022.png" class="rounded" alt="" height="200">';
                    echo '<h3 class="mt-2">' . $row['service_name'] . '</h3>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<button class="btn btn-light mt-2 mb-2"><h5 class="m-0">Цена: от ' . $row['cost'] . ' ₸</h5></button>';
                    echo '<a href="/nazerke/appointment.php"><button type="button" class="btn btn-success">Записаться</button></a>';
                    echo '</div>';
                }
            } else {
                echo "No services found.";
            }
            ?>
        </div>
    </main>
    <footer>
            <h3>Made by Nazerke</h3>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>