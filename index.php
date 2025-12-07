<?php
session_start();
include 'conn.php'; // Includiamo la connessione

// Variabile per mostrare errori/messaggi
$alertMessage = "";

// --- LOGICA DI REGISTRAZIONE ---
if (isset($_POST['register'])) {
    // Pulizia input
    $username = mysqli_real_escape_string($conn, strtolower($_POST['username']));
    $nome = mysqli_real_escape_string($conn, strtolower($_POST['nome']));
    $cognome = mysqli_real_escape_string($conn, strtolower($_POST['cognome']));
    $email = mysqli_real_escape_string($conn, strtolower($_POST['email']));
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // 1. CONTROLLO SICUREZZA PASSWORD (LATO SERVER)
    if (strlen($password) < 12) {
        $alertMessage = "La password deve essere lunga almeno 12 caratteri!";
    } 
    elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $alertMessage = "La password deve contenere almeno una Maiuscola e un Numero!";
    }
    elseif ($password !== $cpassword) {
        $alertMessage = "Le password non corrispondono!";
    } 
    else {
        // Se la password è sicura, procediamo con i controlli DB
        $checkUser = mysqli_query($conn, "SELECT * FROM utenti WHERE Username = '$username'");
        if (mysqli_num_rows($checkUser) > 0) {
            $alertMessage = "Username già utilizzato";
        } else {
            $checkEmail = mysqli_query($conn, "SELECT * FROM utenti WHERE Email = '$email'");
            if (mysqli_num_rows($checkEmail) > 0) {
                $alertMessage = "Email già utilizzata";
            } else {
                // TUTTO OK: Creiamo l'utente
                
                // Impostiamo la sessione subito (opzionale, ma comodo)
                $_SESSION['username'] = $username;
                $_SESSION['nome'] = $nome;
                $_SESSION['cognome'] = $cognome;

                // Inserimento nel DB (SHA2 per sicurezza)
                $sql = "INSERT INTO utenti (Username, Nome, Cognome, Email, Password) VALUES ('$username', '$nome', '$cognome', '$email', SHA2('$password', 512))";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    // Successo! Usiamo JS per alert e redirect
                    echo "<script>
                        alert('Registrazione avvenuta con successo!');
                        window.location.href = '../Image-Gallary/index.php';
                    </script>";
                    exit();
                } else {
                    $alertMessage = "Errore Database: " . mysqli_error($conn);
                }
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
            // Login Corretto
            $_SESSION['username'] = $info['Username'];
            $_SESSION['nome'] = $info['Nome'];
            $_SESSION['cognome'] = $info['Cognome'];
            
            header("Location: ../Image-Gallary/index.php");
            exit();
        } else {
            $alertMessage = "La password non è corretta";
        }
    } else {
        $alertMessage = "Email non trovata";
    }
}

// Redirect se già loggato (Opzionale)
if (isset($_SESSION['username'])) {
   // header("Location: ../Image-Gallary/index.php");
   // exit();
}
?>

<!doctype HTML>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Login & Registrazione</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="stile.css">
    
    <style>
        /* Feedback visivo input */
        input.valid {
            border: 2px solid #2ecc71 !important; /* Verde */
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="%232ecc71" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        input.invalid {
            border: 2px solid #e74c3c !important; /* Rosso */
        }
        /* Testo aiuto */
        #pwd-hint {
            font-size: 0.8em; color: #666; margin-top: -10px; margin-bottom: 10px; display: none; text-align: left; padding-left: 5px; font-weight: bold;
        }
    </style>
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
                    
                    <input type="password" placeholder="Password" name="password" id="regPassword" required>
                    <div id="pwd-hint">Minimo 12 caratteri, 1 Maiuscola, 1 Numero</div>
                    
                    <input type="password" placeholder="Confirm Password" name="cpassword" id="regConfirmPassword" required>
                    
                    <input type="submit" value="Register" id="register" name="register">
                </form>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>

    <script>
        const passwordInput = document.getElementById('regPassword');
        const confirmInput = document.getElementById('regConfirmPassword');
        const hint = document.getElementById('pwd-hint');

        // Regex: Almeno 1 Maiuscola, 1 Numero, Minimo 12 caratteri
        const strongPasswordRegex = /^(?=.*[A-Z])(?=.*[0-9]).{12,}$/;

        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            hint.style.display = 'block'; // Mostra aiuto

            if (strongPasswordRegex.test(val)) {
                passwordInput.classList.remove('invalid');
                passwordInput.classList.add('valid');
                hint.style.color = '#2ecc71';
                hint.innerText = "Password sicura! ✅";
            } else {
                passwordInput.classList.remove('valid');
                passwordInput.classList.add('invalid');
                hint.style.color = '#e74c3c';
                hint.innerText = "Minimo 12 car, 1 Maiuscola, 1 Numero";
            }
        });

        confirmInput.addEventListener('input', function() {
            if (confirmInput.value === passwordInput.value && confirmInput.value !== "") {
                confirmInput.classList.add('valid');
                confirmInput.classList.remove('invalid');
            } else {
                confirmInput.classList.add('invalid');
                confirmInput.classList.remove('valid');
            }
        });
    </script>
</body>
</html>