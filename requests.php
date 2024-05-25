<?php
// Start the session
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['isAuthenticated']) || !$_SESSION['isAuthenticated']) {
    // Redirect to the login page
    header("Location: /nazerke/login.php");
    exit();
}
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
        <div id="requests" class="container mt-5 d-flex gap-5 flex-wrap justify-content-center">
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

            // Get requests
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $sql = "SELECT * FROM requests";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="request rounded w-75 bg-white p-4 ">';
                        echo '<h3 class="mt-2">' . $row['firstname'] . ' ' . $row['lastname'] . '</h3>';
                        echo '<h5>Почта: ' . $row['phone_number'] . '</h5>';
                        echo '<div>';
                        echo '<button class="btn btn-danger mt-2" onclick="showDeleteRequestModal(' . $row['request_id'] . ')">Удалить</button>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "Нет отзывов";
                }
            }

            // Delete request
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                $requestId = $_POST['id'];
                $sql = "DELETE FROM requests WHERE request_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $requestId);

                if ($stmt->execute()) {
                    echo "Request deleted";
                } else {
                    echo "Error deleting request: " . $stmt->error;
                }

                $stmt->close();
            }

            $conn->close();
            ?>
        </div>
    </main>

    <div class="modal fade" id="delete_request-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Удалить отзыв</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Вы действительно хотите удалить?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger px-md-4" onclick="deleteRequest()">Удалить</button>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Отменить</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var globalRequestId;

        function showDeleteRequestModal(requestId) {
            globalRequestId = requestId;
            $('#delete_request-modal').modal('show');
        }

        function deleteRequest() {
            $.ajax({
                url: '', // Set the correct URL for the PHP file
                type: 'POST',
                data: { 'id': globalRequestId },
                success: function(response) {
                    alert('запрос удален');
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting request:', error);
                }
            });
        }
    </script>
</body>

</html>