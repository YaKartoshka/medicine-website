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
    if (isset($_POST['action']) && $_POST['action'] == 'add_doctor') {
        $full_name = $_POST["full_name"];
        $specialization = $_POST["specialization"];

        $sql = "INSERT INTO doctors (doctor_id, full_name, specialization) VALUES (NULL, ?, ?)";
        
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ss", $full_name, $specialization);

        if ($stmt->execute()) {
            echo "Доктор успешно добавлен";
        } else {
            echo "Ошибка при добавлении доктора: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'update_doctor') {
        $doctor_id = $_POST["doctor_id"];
        $full_name = $_POST["full_name"];
        $specialization = $_POST["specialization"];

        $sql = "UPDATE doctors SET full_name=?, specialization=? WHERE doctor_id=?";
        
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("ssi", $full_name, $specialization, $doctor_id);

        if ($stmt->execute()) {
            echo "Данные доктора успешно обновлены";
        } else {
            echo "Ошибка при обновлении данных доктора: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'delete_doctor') {
        $id = $_POST["doctor_id"];

        $sql = "DELETE FROM doctors WHERE doctor_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "Доктор удален";
        } else {
            echo "Error deleting doctor: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST['action']) && $_POST['action'] == 'get_doctor') {
        $doctor_id = $_POST["doctor_id"];

        $sql = "SELECT * FROM doctors WHERE doctor_id=?";
        
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("i", $doctor_id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                
                $doctor = $result->fetch_assoc();
                echo json_encode($doctor);  
            } else {
                echo json_encode(array('error' => 'Doctor not found'));
            }
        } else {
            echo "Error fetching doctor: " . $stmt->error;
        }

        $stmt->close();
    }
}

$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
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
        <div class="container mt-5"><h1>Докторы  <button class="btn btn-primary" onclick="showAddDoctorModal()">+ Добавить</button></h1> </div>
        <div id="requests" class="container mt-5 d-flex pb-5 flex-wrap gap-5">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="doctor rounded" id="doctor-' . $row['doctor_id'] . '">';
                    echo '<h3 class="mt-2">' . $row['full_name'] . '</h3>';
                    echo '<p class="mt-2">' . $row['specialization'] . '</p>';
                    echo '<div>';
                    echo '<button class="btn btn-primary mt-2 w-75" onclick="showEditDoctorModal(' . $row['doctor_id'] . ')">Редактировать</button>';
                    echo '<br>';
                    echo '<button class="btn btn-danger mt-2 w-75" onclick="showDeleteDoctorModal(' . $row['doctor_id'] . ')">Удалить</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No requests found.";
            }
            ?>
        </div>
    </main>

    <div class="modal fade" id="add_doctor-modal" tabindex="-1" aria-labelledby="add_doctor-modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="add_doctor-modalLabel">Добавить доктор</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add_doctor_form" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">ФИО</label>
                            <input type="text" class="form-control" id="new_full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Специальность</label>
                            <input type="text" class="form-control" id="new_specialization" name="specialization" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success px-md-4" form="add_doctor_form" name="add_doctor" onclick="addDoctor()">Добавить</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_doctor-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Редактировать доктора</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_doctor_form">
                        <div class="mb-3">
                            <label for="name" class="form-label">ФИО</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Специальность</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" required>
                        </div>  
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success px-md-4" onclick="editDoctor()">Обновить</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="delete_doctor-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Удалить доктор</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="delete_doctor_form">
                <div class="modal-body">
                    <input type="hidden" id="delete_doctor_id" name="id"> 
                    <p>Вы действительно хотите удалить доктор?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger px-md-4" onclick="deleteDoctor()">Удалить</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var globalDoctorId;
        function showAddDoctorModal() {
            $('#add_doctor-modal').modal('show');
        }

        function showEditDoctorModal(doctorId) {
            globalDoctorId = doctorId;
            var formData = new FormData();
            formData.append('action', 'get_doctor');
            formData.append('doctor_id', globalDoctorId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var data = JSON.parse(xhr.responseText.split('}')[0]+"}");
                    console.log(data)
                    document.getElementById('full_name').value = data.full_name;
                    document.getElementById('specialization').value = data.specialization;
           
                    $('#edit_doctor-modal').modal('show');
                } else {
                 
                }
            };
            xhr.onerror = function() {
                
            };
            xhr.send(formData);
               
                   
        }

        function showDeleteDoctorModal(doctorId) {
            globalDoctorId = doctorId;
            $('#delete_doctor_id').val(doctorId);
            $('#delete_doctor-modal').modal('show');
        }

        function addDoctor() {
            var form = document.getElementById('add_doctor_form');
          
            if (!validateForm(form)) {
                alert('Заполните все поля.');
                return; 
            }
            
            var formData = new FormData(form);
            formData.append('action', 'add_doctor');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Доктор добавлен');
                    window.location.reload();
                } else {
                    console.error('Error adding doctor:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error adding doctor:', xhr.statusText);
            };
            xhr.send(formData);
        }

        function validateForm(form) {
            var inputs = form.querySelectorAll('input');
            for (var i = 0; i < inputs.length; i++) {
                console.log(isValidInput(inputs[i]))
                if (inputs[i].required && !isValidInput(inputs[i])) {
                    return false; 
                }
            }
            return true;
        }

        function isValidInput(input) {
         
            if (input.type === 'file') {
              
                return input.files.length > 0;
            } else {
             
                return input.value.trim() !== '';
            }
        }

        function editDoctor() {
            var form = document.getElementById('edit_doctor_form');

            if (!validateForm(form)) {
                alert('Заполните все поля и файл.');
                return; 
            }

            var formData = new FormData(form);
            formData.append('action', 'update_doctor');
            formData.append('doctor_id', globalDoctorId);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Доктор редактирован');
                    window.location.reload();
                } else {
                    console.error('Error updating doctor:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error updating doctor:', xhr.statusText);
            };
            xhr.send(formData);
        }

        function deleteDoctor() {
            var form = document.getElementById('delete_doctor_form');
            var formData = new FormData(form);
            formData.append('action', 'delete_doctor');
            formData.append('doctor_id', globalDoctorId);

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'admin.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('Доктор удален');
                    console.log(xhr.responseText)
                    window.location.reload();
                } else {
                    console.error('Error deleting doctor:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error deleting doctor:', xhr.statusText);
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