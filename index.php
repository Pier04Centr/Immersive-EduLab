<!doctype HTML>
<?php
session_start();
if (isset($_SESSION['username'])) {
    echo "<script> alert('Sei gia loggato') </script>";
    header("refresh:0;url=./personal.php");
}
?>
<html>

<head>
    <meta name="viewport" content="width=Ddevice-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="stile.css">
</head>

<body>
    <div class="container">
        <div class="blueBg">
            <div class="box signin">
                <h2>Hai gia un Account ?</h2>
                <button class="signinBtn">Sign in <i class="fa-solid fa-right-to-bracket"></i></button>
            </div>
            <div class="box signup">
                <h2>Non hai un Account ?</h2>
                <button class="signupBtn">Sign up <i class="fa-solid fa-user"></i></button>
            </div>
        </div>
        <div class="formBx">
            <div class="form signinForm">
                <form method="POST">
                    <h3>Sign In</h3>
                    <input type="email" placeholder="Email" name="email" required>
                    <input type="password" placeholder="Password" name="password" required>
                    <input type="submit" value="Login" name="login">
                    <a href="#" class="forgot">Password Dimenticata</a>
                </form>
            </div>

            <div class="form signupForm">
                <form method="POST">
                    <h3>Sign Up</h3>
                    <input type="text" placeholder="Username" name="username" required>
                    <input type="text" placeholder="Nome" name="nome" required>
                    <input type="text" placeholder="Cognome" name="cognome" required>
                    <input type="email" placeholder="Email Address" name="email" required>
                    <input type="password" placeholder="Password" name="password" id="password" required>
                    <input type="password" placeholder="Confirm Password" name="cpassword" id="confirmPassword" required>
                    <Input type="submit" value="Register" id="register" name="register">
                </form>
            </div>
        </div>
    </div>

    <?php
    include 'conn.php';
    
    if (!$conn) {
        die("<script> alert('Connessione non riusita') </script>");
    }

    if (isset($_POST['register'])) {
        $username = strtolower($_POST['username']);
        if (mysqli_num_rows(mysqli_query($conn, "SELECT * FROM utenti WHERE username = '$username'")) > 0) {
            echo "<script> alert('Username già utilizzato') </script>";
        } else {
            $nome = strtolower($_POST['nome']);
            $cognome = strtolower($_POST['cognome']);
            $email = strtolower($_POST['email']);
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];

            if ($password == $cpassword) {
                $sql = "SELECT * FROM utenti WHERE email = '$email'";
                $result = mysqli_query($conn, $sql);
                if (!$result->num_rows > 0) {
                    $_SESSION['username'] = $info['Username'];
                    $_SESSION['nome'] = $info['Nome'];
                    $_SESSION['cognome'] = $info['Cognome'];
                    $sql = "INSERT INTO utenti (Username, Nome, Cognome, Email, Password,CODRuolo) VALUES ('$username', '$nome', '$cognome', '$email', SHA2('$password', 512) , 2)";
                    $result = mysqli_query($conn, $sql);
                    if ($result) {
                        echo "<script> alert('Registrazione avvenuta con successo') </script>";
                        header("Location: ../Image-Gallary/index.php");
                    } else {
                        echo "<script> alert('Registrazione non avvenuta') </script>";
                    }
                } else {
                    echo "<script> alert('Email già utilizzata') </script>";
                }
            } else {
                echo "<script> alert('Password non corrispondenti') </script>";
            }
        }
    }


    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password = hash('sha512', $password);

        $sql = "SELECT * FROM utenti WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        if ($result->num_rows > 0) {
            $info = $result->fetch_assoc();
            if ($password == $info['Password']) {
                echo "<script> alert('Login avvenuto con successo') </script>";
                $_SESSION['username'] = $info['Username'];
                $_SESSION['nome'] = $info['Nome'];
                $_SESSION['cognome'] = $info['Cognome'];
                header("Location: ../Image-Gallary/index.php");
            } else {
                echo "<script> alert('La password non è corretta') </script>";
            }
        } else {
            echo "<script> alert('Email non trovato') </script>";
        }
    }
    mysqli_close($conn);

    ?>
</body>
<script src="script.js"></script>

</html>