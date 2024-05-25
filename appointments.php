<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicine";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is authenticated
if (!isset($_SESSION['isAuthenticated']) || !$_SESSION['isAuthenticated']) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == 'add_appointment') {
        $name = $_POST["name"];
        $category = $_POST["category"];
       

  
   
        $sql = "INSERT INTO appointments (name, category) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $category);

        if ($stmt->execute()) {
            echo "appointment added  ";
        } else {
            echo "Error adding appointment: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete_appointment') {
        $id = $_POST["id"];

        $sql = "DELETE FROM appointments WHERE appointment_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "appointment deleted  ";
        } else {
            echo "Error deleting appointment: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update_appointment') {
        $id = $_POST["id"];
        $status = $_POST["status"];
        $sql = "UPDATE appointments SET status=? WHERE appointment_id=?";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            echo "Appointment updated successfully";
        } else {
            echo "Error updating appointment: " . $stmt->error;
        }
        
        $stmt->close();
    }
}

// Retrieve appointments from the database
$sql = "SELECT * FROM appointments";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Поставщики</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav class="navbar navbar-expand-sm bg-success text-white navbar-light">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active text-white" href="/nazerke">NazMedicine</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link text-white" href="/nazerke/admin.php">Админ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/nazerke/appointments.php">Консультации</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/nazerke/requests.php">Запросы</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="container mt-5">
            <h2>Консультации</h2>
        </div>
        <div id="appointments" class="container mt-5 d-flex pb-5 flex-wrap gap-5">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="appointment bg-white p-4 rounded a-'. $row['status'] .'" id="appointment-' . $row['appointment_id'] . '">';
                    echo '<h3 class="mt-2">' . $row['patient_name'] . '</h3>';
                    echo '<h5>' . $row['appointment_date'] . '</h5>';
                    echo '<p>' . $row['reason'] . '</p>';
                    echo '<div>';
                    echo '<button class="btn btn-success mt-2 w-75" onclick="showUpdateAppointmentModal(' . $row['appointment_id'] . ')">Поменять статус</button>';
                    echo '<br>';
                    echo '<button class="btn btn-danger mt-2 w-75" onclick="showDeleteappointmentModal(' . $row['appointment_id'] . ')">Удалить</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No appointments found.";
            }
            ?>
        </div>
    </main>

    <div class="modal fade" id="update_appointment-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Обновить статус</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="update_appointment_form">
                    <div class="modal-body">
                    <label for="">Статус</label>
                    <br>
                        <select name="status" id="status" class="form-control">
                            <option value="0">Не проведен</option>
                            <option value="1">Проведен</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success px-md-4" onclick="updateappointment()">Обновить</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_appointment-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Удалить</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="delete_appointment_form">
                    <div class="modal-body">
                        <input type="hidden" id="delete_appointment_id" name="id"> 
                        <p>Вы хотите удалить?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger px-md-4" onclick="deleteappointment()">Удалить</button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var globalappointmentId;

        function showAddAppointmentModal() {
            $('#add_appointment-modal').modal('show');
        }

        function showUpdateAppointmentModal(appointmentId) {
            globalappointmentId = appointmentId;
            $('#update_appointment-modal').modal('show');
        }

        function showDeleteappointmentModal(appointmentId) {
            globalappointmentId = appointmentId;
            $('#delete_appointment_id').val(appointmentId);
            $('#delete_appointment-modal').modal('show');
        }

      
        function deleteappointment() {
            var form = document.getElementById('delete_appointment_form');
            var formData = new FormData(form);
            formData.append('action', 'delete_appointment');
            formData.append('id', globalappointmentId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'appointments.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Консультация удалена');
                    window.location.reload();
                } else {
                    console.error('Error deleting appointment:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error deleting appointment:', xhr.statusText);
            };
            xhr.send(formData);
        }

        function updateappointment() {
            var form = document.getElementById('update_appointment_form');
            var formData = new FormData(form);
            formData.append('action', 'update_appointment');
            formData.append('id', globalappointmentId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'appointments.php', true);
            xhr.onload = function() {
                console.log(xhr.responseText)
                if (xhr.status === 200) {
                    alert('Консультация обновлена');
                    window.location.reload();
                } 
            };
            xhr.onerror = function() {
            };
            xhr.send(formData);
        }


    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>