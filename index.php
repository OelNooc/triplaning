<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Triplaning - Planifica tus viajes</title>
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="assets/css/common_styles.css">
    <link rel="stylesheet" href="assets/css/navbar_style.css">
    <link rel="stylesheet" href="assets/css/footer_style.css">
    <link rel="stylesheet" href="assets/css/whatsapp_style.css">
    <link rel="stylesheet" href="assets/css/index_style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <main class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-6 text-center">Bienvenido a Triplaning</h1>
        <p class="text-lg text-center mb-8">Tu plataforma para planificar viajes y aventuras inolvidables</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-3">Planifica tu Viaje</h2>
                <p>Organiza tus destinos, actividades y presupuesto de manera sencilla.</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-3">Descubre Destinos</h2>
                <p>Explora lugares populares y rincones poco conocidos para tu próxima aventura.</p>
            </div>
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold mb-3">Comparte Experiencias</h2>
                <p>Conecta con otros viajeros y comparte tus experiencias y recomendaciones.</p>
            </div>
        </div>
    </main>
    
    <?php include 'includes/footer.php'; ?>
    
    <?php include 'includes/whatsapp_button.php'; ?>
</body>
</html>