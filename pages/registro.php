<?php
require_once '../config/db.php';

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($nombre) || empty($apellido) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Por favor, complete todos los campos';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Las contraseñas no coinciden';
    } elseif (strlen($password) < 8) {
        $error_message = 'La contraseña debe tener al menos 8 caracteres';
    } else {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error_message = 'Este correo electrónico ya está registrado';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $nombre_completo = $nombre . ' ' . $apellido;
                
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (:nombre, :email, :contrasena)");
                $stmt->bindParam(':nombre', $nombre_completo);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':contrasena', $hashed_password);
                $stmt->execute();
                
                $success_message = 'Cuenta creada exitosamente. Ahora puedes iniciar sesión.';
            }
        } catch(PDOException $e) {
            $error_message = 'Error al registrar: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Triplaning</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/common_styles.css">
    <link rel="stylesheet" href="../assets/css/navbar_style.css">
    <link rel="stylesheet" href="../assets/css/footer_style.css">
    <link rel="stylesheet" href="../assets/css/whatsapp_style.css">
    <link rel="stylesheet" href="../assets/css/registro_style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    
    <main class="container mx-auto py-8">
        <div class="registro-container">
            <h1 class="registro-title">Crear Nueva Cuenta</h1>
            
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo $success_message; ?>
                    <p class="mt-2">
                        <a href="login.php" class="login-redirect">Ir a iniciar sesión</a>
                    </p>
                </div>
            <?php else: ?>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="registro-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="apellido">Apellido</label>
                            <input type="text" id="apellido" name="apellido" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <small class="form-text">La contraseña debe tener al menos 8 caracteres</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmar contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group form-check">
                        <input type="checkbox" id="terms" name="terms" class="form-check-input" required>
                        <label for="terms" class="form-check-label">Acepto los <a href="#" class="terms-link">términos y condiciones</a></label>
                    </div>
                    
                    <button type="submit" class="btn-submit">Registrarse</button>
                </form>
                
                <div class="registro-login">
                    <p class="login-text">¿Ya tienes una cuenta? <a href="login.php" class="login-link">Inicia sesión</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
    
    <?php include '../includes/whatsapp_button.php'; ?>
</body>
</html>