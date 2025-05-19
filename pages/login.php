<?php
require_once '../config/db.php';

session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Por favor, complete todos los campos';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, nombre, email, contrasena FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['contrasena'])) {
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['nombre'];
                    
                    header("Location: ../index.php");
                    exit();
                } else {
                    $error_message = 'Credenciales incorrectas';
                }
            } else {
                $error_message = 'Credenciales incorrectas';
            }
        } catch(PDOException $e) {
            $error_message = 'Error al iniciar sesión: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Triplaning</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/common_styles.css">
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
    <link rel="stylesheet" href="../assets/css/whatsapp_style.css">
    <link rel="stylesheet" href="../assets/css/login_style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8">
        <div class="login-container">
            <h1 class="login-title">Iniciar Sesión</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
    
    <?php include '../includes/whatsapp_button.php'; ?>
</body>
</html>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="login-form">
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Recordar mis datos</label>
                </div>
                
                <button type="submit" class="btn-submit">Iniciar Sesión</button>
            </form>
            
            <div class="login-links">
                <a href="recuperar_contrasena.php" class="recovery-link">¿Olvidaste tu contraseña?</a>
                <p class="register-text">¿No tienes una cuenta? <a href="registro.php" class="register-link">Regístrate</a></p>
            </div>
        </div>
        <?php include '../includes/footer.php'; ?>