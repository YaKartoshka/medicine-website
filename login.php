<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "medicine";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['isAuthenticated'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $logErr = "Неверная почта или пароль";
    }

    $stmt->close();
}


if (!isset($_SESSION['isAuthenticated']) || !$_SESSION['isAuthenticated']) {
   
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
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
        <div class="login container">
            <h3  style="text-align: center">Учет и управление медицинскими услугами в поликлинике</h3>
            <div id="ambiance" class=" p-0"></div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <?php if (isset($logErr)) { ?>
                    <div class="alert alert-danger"><?php echo $logErr; ?></div>
                <?php } ?>
                <div class="mb-3 mt-3 ">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" placeholder="Введите вашу почту" name="email">
                </div>
                <div class="mb-1">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" placeholder="Введите пароль" name="password">
                </div>
                <button type="submit" id="login_btn" class="btn btn-success px-5 mt-4 w-100" name="login">Вход</button>
            </form>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Handle form submissions for products
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (code for handling product submissions remains the same)
}

// Retrieve products from the database

?>

<!-- HTML code for the admin panel remains the same -->

<?php
// Close the database connection
$conn->close();
?>