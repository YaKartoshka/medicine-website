<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicine";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $phone_number = $_POST["phone_number"];

    // Check if the phone number already exists in the requests table
    $sql_check = "SELECT COUNT(*) AS count FROM requests WHERE phone_number = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $phone_number);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $row = $result->fetch_assoc();
    $existing_count = $row['count'];
    $stmt_check->close();

    if ($existing_count > 0) {
        echo "Отзыв добавлен";
    } else {
        $sql_insert = "INSERT INTO requests (firstname, lastname, phone_number) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $firstname, $lastname, $phone_number);

        if ($stmt_insert->execute()) {
            echo "Запрос отправлен";
        } else {
            echo "Error adding request: " . $stmt_insert->error;
        }
        $stmt_insert->close(); 
    }
}
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
    <div class="container mt-5 w-75">
        <h2>Оставить запрос</h2>
        <form class="mt-3" id="add_request_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="mb-3">
                <label for="firstname" class="form-label">Ваше Имя</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
            <div class="mb-3">
                <label for="lastname" class="form-label">Фамилия</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Номер телефона</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <button type="submit" class="btn btn-success">Отправить</button>
        </form>
    </div>
</main>
<footer>
            <h3>Made by Nazerke</h3>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    if (sessionStorage.getItem('form')) {
        alert('Ваш запрос еще в обработке');
        window.location.href = '/nazerke';
    }

    $(document).ready(function() {
        $('#add_request_form').submit(function(event) {
            event.preventDefault(); 

            $.ajax({
                type: 'POST',
                url: '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>',
                data: $(this).serialize(),
                success: function(response) {
                    console.log('Request sent successfully:', response);
                    alert('Ваш запрос отправлен');
                    window.location.href = '/nazerke';
                    sessionStorage.setItem('form', 'send');
                },
                error: function(xhr, status, error) {
                    console.error('Error sending request:', error);
                }
            });
        });
    });
</script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>