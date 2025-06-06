<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vetado</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Funnel+Display:wght@300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    body {
        font-family: "Poppins", serif;
        background-color: rgb(250, 250, 250);
        overflow-x: hidden;
    }
    .h1{
        font-family: "Funnel display", serif;
        color: rgb(195, 0, 0);
        background-color: rgb(255, 155, 155);
        font-size: 4vw;
        margin: 20px;
    }
    .utils{
        display: flex;
        justify-content: center;
    }
    .utils a{
        text-align: center;
        padding: 20px 50px;
        color: rgb(63, 63, 63);
        text-decoration: none;
        background-color: rgb(220, 220, 220);
        font-weight: 700;
        font-size: 3vw;
        margin: 10px;
        border-radius: 100px;
    }
    @media (max-width: 800px) {
        .h1{font-size: 5vw;}
        .utils{flex-direction: column;}
        .utils a{font-size: 4vw;}
    }
</style>
<body>
    <div style="text-align: center;" class="h1">
        <h1>Acceso restringido</h1>
    </div>
    <div class="utils">
            <a href="">Ir al inicio</a>
            <a href="">Soporte</a>
    </div>
</body>
</html>