<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Noticias</title>
</head>
<body>
    <h1>Bienvenido, <?php echo $_SESSION['nombre']; ?></h1>
    <form method="POST" action="publicar.php">
        <input type="text" name="titulo" placeholder="Título de la noticia" required><br>
        
        <select name="categoria" required>
            <option value="">-- Selecciona Categoría --</option>
            <option value="Política">Política</option>
            <option value="Tecnología">Tecnología</option>
            <option value="Deportes">Deportes</option>
            <option value="Internacional">Internacional</option>
            <option value="Educación">Educación</option>
        </select><br>

        <textarea name="contenido" placeholder="Contenido de la noticia" required></textarea><br>
        <button type="submit">Publicar</button>
    </form>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
