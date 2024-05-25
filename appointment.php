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
    // Retrieve form data
    $patient_name = $_POST["patient_name"];
    $doctor_id = $_POST["doctor_id"];
    $appointment_date = $_POST["appointment_date"];
    $reason = $_POST["reason"];

    // SQL query to insert data into the appointments table
    $sql_insert = "INSERT INTO appointments (appointment_id, patient_name, doctor_id, appointment_date, reason) 
                   VALUES (NULL, ?, ?, ?, ?)";

    // Prepare the SQL statement
    $stmt_insert = $conn->prepare($sql_insert);

    // Bind parameters
    $stmt_insert->bind_param("ssss", $patient_name, $doctor_id, $appointment_date, $reason);

    // Execute the statement
    if ($stmt_insert->execute()) {
        echo "Appointment successfully added.";
    } else {
        echo "Error adding appointment: " . $stmt_insert->error;
    }

    // Close the statement
    $stmt_insert->close();
}

$sql1 = "SELECT * FROM medical_services";
$medical_services = $conn->query($sql1);
$sql2 = "SELECT * FROM doctors";
$doctors = $conn->query($sql2);
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
    <div class="container mt-5">
        <h2>Записаться на консультацию</h2>
        <form id="add_appointment_form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="mb-3">
                <label for="patient_name" class="form-label">Ваше Имя</label>
                <input type="text" class="form-control" id="patient_name" name="patient_name" required>
            </div>
            <div class="mb-3">
                <label for="doctor_id" class="form-label">Врач</label>
                <select class="form-control" id="doctor_id" name="doctor_id" required>
                
                <?php
                    if ($doctors->num_rows > 0) {
                        while ($row = $doctors->fetch_assoc()) {
                            echo '<option value="' . $row['doctor_id'] . '">';
                            echo '' . $row['specialization'] . ' | ' . $row['full_name'] . '';
                            echo '</option>';
                            
                        }
                    } else {
                        echo "No services found.";
                    }
                    ?>
                </select>
            </div>
           
            <div class="mb-3 mt-3 w-50">
                <label for="appointment_date" class="form-label">Дата записи</label>
                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
            </div>
            <div class="mb-3">
                <label for="reason" class="form-label">Причина</label>
                <input type="text" class="form-control" id="reason" name="reason" required>
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
   
    if (sessionStorage.getItem('form2')) {
        alert('Ваш запрос еще в обработке');
        window.location.href = '/nazerke';
    }

    $(document).ready(function() {
    $('#add_appointment_form').submit(function(event) {
        event.preventDefault();
        
        var isEmpty = false;
        $(this).find('input[type=text]').each(function() {
            if ($(this).val().trim() === '') {
                isEmpty = true;
                return false; 
            }
        });

        if (isEmpty) {
            alert('Пожалуйста, заполните все поля перед отправкой формы.');
            return; 
        }

 
        $.ajax({
            type: 'POST',
            url: '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>',
            data: $(this).serialize(),
            success: function(response) {
                alert('Вы записаны на консультацию');
                window.location.href = '/nazerke';
                sessionStorage.setItem('form2', 'send');
            },
            error: function(xhr, status, error) {
                console.error('Ошибка при отправке запроса:', error);
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