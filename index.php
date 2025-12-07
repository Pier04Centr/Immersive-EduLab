<?php
session_start();
include 'conn.php'; // Includiamo subito la connessione

// Variabile per mostrare eventuali errori/messaggi nel corpo HTML
$alertMessage = "";

// --- LOGICA DI REGISTRAZIONE ---
if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, strtolower($_POST['username']));
    $nome = mysqli_real_escape_string($conn, strtolower($_POST['nome']));
    $cognome = mysqli_real_escape_string($conn, strtolower($_POST['cognome']));
    $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Controllo Username
    $checkUser = mysqli_query($conn, "SELECT * FROM utenti WHERE Username = '$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        $alertMessage = "Username già utilizzato";
    } else {
        if ($password == $cpassword) {
            // Controllo Email
            $checkEmail = mysqli_query($conn, "SELECT * FROM utenti WHERE Email = '$email'");
            if (mysqli_num_rows($checkEmail) == 0) {
                
                // FIX IMPORTANTE: Qui usiamo le variabili dirette, non $info (che non esiste ancora)
                // Nota: Di solito si fa il login DOPO la registrazione, ma se vuoi settare la sessione subito:
                $_SESSION['username'] = $username;
                $_SESSION['nome'] = $nome;
                $_SESSION['cognome'] = $cognome;

                // Inserimento nel DB
                // Nota: SHA2 è una funzione di MySQL
                $sql = "INSERT INTO utenti (Username, Nome, Cognome, Email, Password) VALUES ('$username', '$nome', '$cognome', '$email', SHA2('$password', 512))";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    // Usiamo JavaScript per fare sia l'alert che il redirect
                    echo "<script>
                        alert('Registrazione avvenuta con successo');
                        window.location.href = '../Image-Gallary/index.php';
                    </script>";
                    exit();
                } else {
                    $alertMessage = "Registrazione non avvenuta: " . mysqli_error($conn);
                }
            } else {
                $alertMessage = "Email già utilizzata";
            }
        } else {
            $alertMessage = "Password non corrispondenti";
        }
    }
}

// --- LOGICA DI LOGIN ---
if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    // Hashing della password per confrontarla con quella nel DB
    $passwordHashed = hash('sha512', $password);

    $sql = "SELECT * FROM utenti WHERE Email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $info = $result->fetch_assoc();
        // Confrontiamo le password
        if ($passwordHashed == $info['Password']) {
            $_SESSION['username'] = $info['Username'];
            $_SESSION['nome'] = $info['Nome'];
            $_SESSION['cognome'] = $info['Cognome'];
            
            // Redirect
            header("Location: ../Image-Gallary/index.php");
            exit();
        } else {
            $alertMessage = "La password non è corretta";
        }
    } else {
        $alertMessage = "Email non trovata";
    }
}

// Redirect se già loggato
if (isset($_SESSION['username'])) {
    // Nota: path corretto per tornare alla home o galleria
   // header("Location: ../Image-Gallary/index.php");
   // exit();
}
?>

<!doctype HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" href="stile.css">
</head>

<body>
    <?php if ($alertMessage != ""): ?>
        <script>alert("<?php echo $alertMessage; ?>");</script>
    <?php endif; ?>

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
                    <input type="submit" value="Register" id="register" name="register">
                </form>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>