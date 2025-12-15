<?php
session_start();
include 'conn.php'; 

$alertMessage = "";

// --- LOGICA DI REGISTRAZIONE ---
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, strtolower($_POST['username']));
    $nome = mysqli_real_escape_string($conn, strtolower($_POST['nome']));
    $cognome = mysqli_real_escape_string($conn, strtolower($_POST['cognome']));
    $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $checkUser = mysqli_query($conn, "SELECT * FROM utenti WHERE Username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        $alertMessage = "ACCESS DENIED: Username taken";
    } else {
        $checkEmail = mysqli_query($conn, "SELECT * FROM utenti WHERE Email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $alertMessage = "ACCESS DENIED: Email registered";
        } else {
            $_SESSION['username'] = $username;
            $_SESSION['nome'] = $nome;
            $_SESSION['cognome'] = $cognome;

            $sql = "INSERT INTO utenti (Username, Nome, Cognome, Email, Password) VALUES ('$username', '$nome', '$cognome', '$email', SHA2('$password', 512))";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                echo "<script>
                    alert('ACCESS GRANTED: Profile Created.');
                    window.location.href = '../Image-Gallary/index.php';
                </script>";
                exit();
            } else {
                $alertMessage = "DB ERROR: " . mysqli_error($conn);
            }
        }
    }
}

// --- LOGICA DI LOGIN ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $passwordHashed = hash('sha512', $password);

    $sql = "SELECT * FROM utenti WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && $result->num_rows > 0) {
        $info = $result->fetch_assoc();
        if ($passwordHashed == $info['Password']) {
            $_SESSION['username'] = $info['Username'];
            $_SESSION['nome'] = $info['Nome'];
            $_SESSION['cognome'] = $info['Cognome'];
            
            header("Location: ../Image-Gallary/index.php");
            exit();
        } else {
            $alertMessage = "ACCESS DENIED: Invalid credentials";
        }
    } else {
        $alertMessage = "ACCESS DENIED: User not found";
    }
}
?>

<!doctype HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>EduLab Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stile.css?v=4">
</head>

<body>
    <a href="../index.html" style="position: absolute; top: 20px; left: 20px; color: var(--primary); text-decoration: none; font-weight: bold; border: 1px solid var(--primary); padding: 5px 10px; z-index: 9999; border-radius:4px;">
        <i class="fa-solid fa-arrow-left"></i> HOME
    </a>

    <button id="theme-toggle" class="theme-toggle-btn" title="Toggle Light/Dark Mode">
        <i class="fa-solid fa-sun"></i>
    </button>

    <?php if ($alertMessage != ""): ?>
        <script>alert("<?php echo $alertMessage; ?>");</script>
    <?php endif; ?>

    <div class="container">
        <div class="blueBg">
            <div class="box signin">
                <h2>Have an account?</h2>
                <button class="signinBtn">Sign In <i class="fa-solid fa-key"></i></button>
            </div>
            <div class="box signup">
                <h2>New here?</h2>
                <button class="signupBtn">Sign Up <i class="fa-solid fa-user-plus"></i></button>
            </div>
        </div>
        
        <div class="formBx">
            
            <div class="form signinForm">
                <form method="POST">
                    <h3>Access</h3>
                    <input type="email" placeholder="USER EMAIL" name="email" required>
                    <input type="password" placeholder="PASSWORD" name="password" required>
                    <input type="submit" value="LOGIN" name="login">
                    <a href="#" class="forgot">Forgot credentials?</a>
                </form>
            </div>

            <div class="form signupForm">
                <form method="POST">
                    <h3>New Profile</h3>
                    <input type="text" placeholder="SET USERNAME" name="username" required>
                    <input type="text" placeholder="FIRST NAME" name="nome" required>
                    <input type="text" placeholder="LAST NAME" name="cognome" required>
                    <input type="email" placeholder="EMAIL ADDRESS" name="email" required>
                    
                    <input type="password" placeholder="SET PASSWORD" name="password" id="regPassword" required>
                    <div id="pwd-hint">SECURE REQ: 12+ Chars, 1 Upper, 1 Digit</div>
                    
                    <input type="password" placeholder="CONFIRM PASSWORD" name="cpassword" id="regConfirmPassword" required>
                    
                    <input type="submit" value="CREATE" id="register" name="register">
                </form>
            </div>
        </div>
    </div>
    
    <script src="./script.js"></script>
</body>
</html>