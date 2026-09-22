<!DOCTYPE html>
<html lang="es">
<head>
<!-- Configuración básica del documento y compatibilidad responsive -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sis Dit</title>

<style>
/* ======= RESET ======= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Segoe UI', sans-serif;
}

/* ===== HEADER ===== */
/* Contenedor principal de la barra de navegación */
.header{
    background:#7b0f2b;
    color:white;
    padding:15px 40px;
}

.nav-container{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
}

/* Área que contiene el logotipo y el nombre de la institución */
.logo-area{
    display:flex;
    align-items:center;
    gap:10px;
}

/* Dimensiones del logotipo institucional */
.logo-area img{
    height:50px;
}

/* Lista de enlaces del menú principal */
nav ul{
    display:flex;
    gap:25px;
    list-style:none;
}

/* Estilos generales de los enlaces de navegación */
nav a{
    color:white;
    text-decoration:none;
    font-weight:500;
}

/* Estilo destacado para el enlace de acceso al sistema */
.btn-nav{
    background:#2e7d6f;
    padding:8px 15px;
    border-radius:6px;
}

/* ===== FOOTER ===== */
/* Estilos generales del pie de página */
.footer{
    background:#7b0f2b;
    color:white;
    text-align:center;
    padding:40px 20px;
}

/* Tamaño y separación del logotipo en el pie de página */
.footer img{
    height:70px;
    margin-bottom:15px;
}

/* ===== RESPONSIVO ===== */
/* Ajustes de navegación para pantallas pequeñas */
@media(max-width:768px){
    nav ul{
        flex-direction:column;
        align-items:center;
        margin-top:10px;
    }

    .nav-container{
        flex-direction:column;
        gap:15px;
    }
}
</style>
</head>

<body>

<!-- Encabezado principal con identidad institucional y menú de navegación -->
<header class="header">
    <div class="nav-container">
        <!-- Logotipo y nombre de la Presidencia Municipal -->
        <div class="logo-area">
            <img src="logos/logo_presi.jpeg">
            <span>Presidencia Municipal<br>Rincón de Romos</span>
        </div>

        <!-- Menú principal del sitio -->
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="requisitos.php">Requisitos</a></li>
                <li><a href="acceso.php" class="btn-nav">Acceso al sistema</a></li>
            </ul>
        </nav>
    </div>
</header>