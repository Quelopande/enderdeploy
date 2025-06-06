<!doctype html>
<html lang="es">
<head>
    <!-- EnderDeploy No redirects-->
    <meta name="theme-color" content="#00d601">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="EnderDeploy">
    <meta name="description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderCore, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa.">
    <!-- Twitter card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="EnderDeploy">
    <meta name="twitter:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderCore, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa.">
    <meta name="twitter:image" content="assets/media/logo.webp">
    <!-- Facebook & discord -->
    <meta property="og:locale" content="es"/>
    <meta property="og:site_name" content="©EnderCore"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title" content="EnderDeploy"/>
    <meta property="og:description" content="EnderDeploy es una plataforma avanzada de despliegue de aplicaciones SaaS y administración de infraestructura como servicio (IaaS), diseñada para optimizar la implementación y gestión de soluciones tecnológicas. Desarrollada por EnderCore, proporciona un entorno flexible y escalable para facilitar el crecimiento de tu empresa."/>
    <meta property="og:url" content="https://deploy.EnderCore.com.mx"/>
    <meta property="og:image" content="/assets/img/logo.png"/>
    <meta property="og:image:width" content="540"/>
    <meta property="og:image:height" content="520"/>
    <!-- EnderDeploy -->
    <title>EnderDeploy</title>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
    <link rel="stylesheet" href="/assets/styles/index.css">
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
</head>
<body>
    <nav class="nav">
        <div class="logo">
            <img src="/assets/img/logo.png" alt="Logo" class="logo-image" height="50">
            <p>Deploy</p>
            <i class="fa-solid fa-bars util" onclick="menu('open')"></i>
            <i class="fa-solid fa-xmark util" onclick="menu('close')"></i>
        </div>
        <div class="ullast">
            <ul>
                <li><a href="/">Inicio</a></li>
                <li><a href="/prices">Precio</a></li>
                <li><a href="/docs">Documentación</a></li>
                <li><a href="/support">Soporte</a></li>
            </ul>
            <div class="last">
            <?php 
            if (!isset($_COOKIE['id'])){
                echo 
                '<a href="../auth/signup.php">Regístrate</a>
                <a href="../auth/signin.php">Inicia Sesión</a>';
            } else {
                echo 
                '<a href="../dashboard/index.php">Panel de control</a>';
            }
            ?>
            </div>
        </div>
    </nav>
    <div class="hero">
        <h1>Simplificando tu infraestructura</h1>
        <p>maximizando <u>tu potencial</u></p>
        <img src="/assets/img/cohete.png" alt="">
        <div class="heroButtons">
            <a class="discover"><span class="text">Ver más</span><span class="svg"><svg xmlns="http://www.w3.org/2000/svg"width="50"height="20"viewBox="0 0 38 15"fill="none"><path fill="white"d="M10 7.519l-.939-.344h0l.939.344zm14.386-1.205l-.981-.192.981.192zm1.276 5.509l.537.843.148-.094.107-.139-.792-.611zm4.819-4.304l-.385-.923h0l.385.923zm7.227.707a1 1 0 0 0 0-1.414L31.343.448a1 1 0 0 0-1.414 0 1 1 0 0 0 0 1.414l5.657 5.657-5.657 5.657a1 1 0 0 0 1.414 1.414l6.364-6.364zM1 7.519l.554.833.029-.019.094-.061.361-.23 1.277-.77c1.054-.609 2.397-1.32 3.629-1.787.617-.234 1.17-.392 1.623-.455.477-.066.707-.008.788.034.025.013.031.021.039.034a.56.56 0 0 1 .058.235c.029.327-.047.906-.39 1.842l1.878.689c.383-1.044.571-1.949.505-2.705-.072-.815-.45-1.493-1.16-1.865-.627-.329-1.358-.332-1.993-.244-.659.092-1.367.305-2.056.566-1.381.523-2.833 1.297-3.921 1.925l-1.341.808-.385.245-.104.068-.028.018c-.011.007-.011.007.543.84zm8.061-.344c-.198.54-.328 1.038-.36 1.484-.032.441.024.94.325 1.364.319.45.786.64 1.21.697.403.054.824-.001 1.21-.09.775-.179 1.694-.566 2.633-1.014l3.023-1.554c2.115-1.122 4.107-2.168 5.476-2.524.329-.086.573-.117.742-.115s.195.038.161.014c-.15-.105.085-.139-.076.685l1.963.384c.192-.98.152-2.083-.74-2.707-.405-.283-.868-.37-1.28-.376s-.849.069-1.274.179c-1.65.43-3.888 1.621-5.909 2.693l-2.948 1.517c-.92.439-1.673.743-2.221.87-.276.064-.429.065-.492.057-.043-.006.066.003.155.127.07.099.024.131.038-.063.014-.187.078-.49.243-.94l-1.878-.689zm14.343-1.053c-.361 1.844-.474 3.185-.413 4.161.059.95.294 1.72.811 2.215.567.544 1.242.546 1.664.459a2.34 2.34 0 0 0 .502-.167l.15-.076.049-.028.018-.011c.013-.008.013-.008-.524-.852l-.536-.844.019-.012c-.038.018-.064.027-.084.032-.037.008.053-.013.125.056.021.02-.151-.135-.198-.895-.046-.734.034-1.887.38-3.652l-1.963-.384zm2.257 5.701l.791.611.024-.031.08-.101.311-.377 1.093-1.213c.922-.954 2.005-1.894 2.904-2.27l-.771-1.846c-1.31.547-2.637 1.758-3.572 2.725l-1.184 1.314-.341.414-.093.117-.025.032c-.01.013-.01.013.781.624zm5.204-3.381c.989-.413 1.791-.42 2.697-.307.871.108 2.083.385 3.437.385v-2c-1.197 0-2.041-.226-3.19-.369-1.114-.139-2.297-.146-3.715.447l.771 1.846z"></path></svg></span></a>
            <a class="start">
              <div class="bgContainer">
                <span>Empieza Ahora</span>
              </div>
              <div class="arrowContainer"><i class="fa-solid fa-bolt"></i></div>
            </a>
        </div>
    </div>
    <div class="box">
        <h2>“Despega tu negocio hoy, conquista el futuro con la infraestructura que necesitas”</h2>
    </div>
    <div class="features">
        <div class="feature">
            <img src="/assets/img/logo.png" alt="Logo" height="230px" style="padding: 30px;">
            <div>
                <h2>Potenciado por tu Host favorito</h2>
                <p>Tecnología confiable, velocidad imparable y soporte siempre disponible. Con EnderCore, tu infraestructura crece contigo, brindándote el rendimiento y la seguridad que necesitas.</p>
            </div>
        </div>
        <div class="feature">
            <img src="/assets/img/cloud.png" alt="Imágen de nube" height="230px">
            <div>
                <h2>Fácil integración con otros SaaS</h2>
            <p>EnderDeploy se integra sin esfuerzo con las principales plataformas SaaS, facilitando la gestión y el despliegue de tu infraestructura.</p>
            </div>
        </div>
        <div class="feature">
            <img src="/assets/img/phone.png" alt="Imágen de teléfono móvil" height="230px">
            <div>
                <h2>Todo lo que necesitas desde el celular</h2>
            <p>Nuestro equipo está aquí para ayudarte. Con soporte técnico rápido y eficiente, nunca estarás solo en tu camino hacia el éxito.</p>
            </div>
        </div>
        <div class="feature">
            <img src="/assets/img/support.png" alt="Imágen de teléfono móvil" height="230px">
            <div>
                <h2>Soporte a cada paso</h2>
            <p>Nuestro equipo está aquí para ayudarte. Con soporte técnico rápido y eficiente, nunca estarás solo en tu camino hacia el éxito.</p>
            </div>
        </div>
    </div>
    <div class="informationSection">
        <h2>Con EnderDeploy, una gestión inteligente que garantiza un crecimiento sostenido y exitoso para tu negocio o proyecto</h2>
        <div>
            <div class="information" style="--color: #036cff;--bg-color: #bcd5ff;">
                <div>
                    <i class="fa-regular fa-shield"></i>
                    <h3>Seguridad</h3>
                </div>
                <h2>Tu tranquilidad es nuestra prioridad</h2>
                <p>Cuidamos tus datos con cifrado avanzado acceso seguro y una infraestructura siempre protegida</p>
                <a href="">Más Información</a>
            </div>
            <div class="information" style="--color: #ff3a28;--bg-color: #ffc9c5;">
                <div>
                    <i class="fa-duotone fa-solid fa-bolt" style="padding: 10px 14px;"></i>
                    <h3>Velocidad</h3>
                </div>
                <h2>La velocidad que necesitas para la mejora</h2>
                <p>Despliegues rápidos, infraestructura optimizada y rendimiento sin límites para que nada te detenga.</p>
                <a href="">Más Información</a>
            </div>
            <div class="information" style="--color: #188038;--bg-color: #ceead6;">
                <div>
                    <i class="fa-regular fa-chart-line-up"></i>
                    <h3>Crecimiento</h3>
                </div>
                <h2>Impulsa tu crecimiento y amplía fronteras</h2>
                <p>Herramientas escalables soporte confiable y tecnología lista para crecer contigo</p>
                <a href="">Más Información</a>
            </div>
        </div>
    </div>
    <div class="wrapper">
        <div class="txt">
            <h2>Accede a más de 10 productos de vanguardia, y a la infraestructura capaz de construir el futuro</h2>
            <p>Nosotros proporcionamos herramientas para desarrolladores e infraestructura en la nube para construir, implementar y escalar</p>
        </div>
        <div class="callAppearanceTxt">
            <button class="active" id="callAppearance1" onclick="changeTxt('callAppearance1', 'El ERP de Código Abierto más ágil del planeta', 'EnderSuit es un sistema ERP de código abierto diseñado para gestionar y optimizar todas las áreas clave de una empresa, desde contabilidad y gestión de inventarios hasta proyectos y CRM. Ofrece una solución completa, escalable y personalizable para pequeñas, medianas y grandes empresas, ayudándolas a centralizar datos, automatizar procesos y tomar decisiones informadas para impulsar su crecimiento.')">EnderSuit</button>
            <button id="callAppearance2" onclick="changeTxt('callAppearance2', 'La alternativa de Sistema de Pasarela de Código Abierto para Hostings', 'Paymenter es una plataforma de código abierto para la gestión y facturación de empresas de hosting. Permite automatizar procesos como activación y suspensión de servicios, integra herramientas como Pterodactyl y Stripe, y ofrece personalización, escalabilidad, seguridad avanzada y optimización SEO. Es ideal para empresas de cualquier tamaño por su enfoque en eficiencia y facilidad de uso.')">Paymenter</button>
            <button id="callAppearance3" onclick="changeTxt('callAppearance3', 'Vende más, preocúpese menos: soluciones de facturación automatizadas', 'WemX es una plataforma diseñada para automatizar las operaciones de negocios digitales, como servicios de SaaS, hosting y otros relacionados. Es una solución integral que facilita la gestión de pagos, suscripciones, pedidos y facturación con funcionalidades avanzadas.')">WemX</button>
        </div>
        <div class="txtToShow">
            <h2 id="h2TxtBox">El ERP de Código Abierto más ágil del planeta</h2>
            <p id="pTxtBox">EnderSuit es un sistema ERP de código abierto diseñado para gestionar y optimizar todas las áreas clave de una empresa, desde contabilidad y gestión de inventarios hasta proyectos y CRM. Ofrece una solución completa, escalable y personalizable para pequeñas, medianas y grandes empresas, ayudándolas a centralizar datos, automatizar procesos y tomar decisiones informadas para impulsar su crecimiento.</p>
        </div>
    </div>
    <script src="/assets/js/indexMobileMenu.js"></script>
    <script src="/assets/js/indexScrollMenu.js"></script>
    <script src="/assets/js/indexWrapperChangeTxt.js"></script>
</body>
</html>