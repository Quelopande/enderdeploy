<!doctype html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RenderCores</title>
    <meta name="description" content="ERP moderno para PYMEs en México. Implementación rápida, personalización low-code y soporte humano. Crea flujos, formularios y reportes sin tocar código, o amplía con Python/Javascript cuando lo necesites.">
    <link href="./assets/styles/index.css" rel="stylesheet">
    <link href="./assets/styles/fonts/aspekta.css" rel="stylesheet">
    <link href="./assets/styles/fonts/poppins.css" rel="stylesheet">
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet">
    <link rel="preload" href="/assets/img/logo.png" as="image">
    <link rel="icon" type="image/x-icon" href="/assets/img/logo.ico">
    <!-- Facebook Meta Tags -->
    <meta property="og:url" content="https://rendercores.com">
    <meta property="og:type" content="website">
    <meta property="og:title" content="RenderCores">
    <meta property="og:description" content="ERP moderno para PYMEs en México. Implementación rápida, personalización low-code y soporte humano. Crea flujos, formularios y reportes sin tocar código, o amplía con Python/Javascript cuando lo necesites.">
    <meta property="og:image" content="https://rendercores.com/assets/img/logo.png">
    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="rendercores.com">
    <meta property="twitter:url" content="https://rendercores.com">
    <meta name="twitter:title" content="RenderCores">
    <meta name="twitter:description" content="ERP moderno para PYMEs en México. Implementación rápida, personalización low-code y soporte humano. Crea flujos, formularios y reportes sin tocar código, o amplía con Python/Javascript cuando lo necesites.">
    <meta property="twitter:image" content="https://rendercores.com/assets/img/logo.png">
</head>
<body class="font-poppins bg-standard text-txt overflow-x-hidden">
    <nav class="px-5 lg:px-3 flex flex-row left-0 right-0 justify-between z-70 fixed top-0 mx-4 min-[1450px]:mx-25 border border-gray-300 mt-2 pt-4 lg:py-2 rounded-6xl lg:rounded-5xl bg-standard">
        <a class="flex items-center mb-4 lg:mb-0 z-10" href="/">
            <img src="https://rendercores.com/assets/img/logo.png" alt="Logo azul y blanco de RenderCores" class="w-12 h-12" loading="lazy" decoding="async">
            <h1 class="ml-3 text-xl lg:text-2xl font-bold">RenderCores</h1>
        </a>
        <div class="hidden lg:flex items-center">
            <ul class="flex space-x-6">
                <li class="hover:underline decoration-branding decoration-2 underline-offset-4"><a href="/#caracteristicas">Características</a></li>
                <li class="hover:underline decoration-branding decoration-2 underline-offset-4"><a href="/#modulos">Módulos</a></li>
                <li class="hover:underline decoration-branding decoration-2 underline-offset-4"><a href="/#planes">Precios</a></li>
                <li class="hover:underline decoration-branding decoration-2 underline-offset-4"><a href="/#recursos">Recursos</a></li>
            </ul>
            <a href="/auth/signin" class="ml-6 border-1 border-primary-hover py-2 px-6 hover:bg-primary transition-colors rounded-2xl active:bg-primary-hover">Iniciar Sesión</a>
            <a href="/#contacto" class="ml-3 border-1 border-branding py-2 px-6 bg-branding/30 hover:bg-branding hover:text-white transition-colors rounded-2xl active:bg-branding-hover">Probar gratis</a>
        </div>
        <button class="lg:hidden text-2xl z-10 -mt-3" id="menuBtn" aria-label="Despliega el menú"><i class="fa-solid fa-bars" id="menuBtnIcon"></i></button>
    </nav>
    <div id="mobileMenu" class="hidden fixed lg:hidden bg-standard w-full h-full py-4 px-5 top-0 z-5">
        <ul class="space-y-4 text-center pt-30">
            <li><a href="/#caracteristicas" class="block py-2">Características</a></li>
            <li><a href="/#modulos" class="block py-2">Módulos</a></li>
            <li><a href="#planes" class="block py-2">Precios</a></li>
            <li><a href="/#recursos" class="block py-2">Recursos</a></li>
            <div class="mt-6 flex flex-col gap-3 absolute left-5 right-5 bottom-10">
                <li><a href="/auth/signin" class="block mt-4 py-3 border-2 border-primary-hover rounded-2xl">Iniciar Sesión</a></li>
                <li><a href="/#contacto" class="block py-3 border-2 text-white border-branding bg-branding px-6 rounded-2xl">Probar gratis</a></li>
            </div>
        </ul>
    </div>
    <header class="mx-4 min-[1450px]:mx-25 flex flex-col z-20 h-[90vh] justify-center items-center text-center">
        <div class="flex flex-col justify-between items-center text-center">
            <div class="flex flex-col gap-4 text-center mt-35 lg:mt-25">
                <h2 class="text-5xl md:text-6xl lg:text-7xl xl:text-8xl font-bold text-gray-800 leading-[1.25] font-aspekta"><span class="underline decoration-branding">ERP moderno</span> para PYMEs en México</h2>
                <p class="mt-5 text-lg lg:text-2xl text-gray-500 font-aspekta tracking-wide">Creado por una PYME para las PYMEs</p>
                <div class="mt-7 flex flex-col md:flex-row justify-center gap-3">
                    <a href="/#planes" class="bg-branding text-white py-3 px-18 md:px-28 rounded-2xl hover:bg-branding-hover active:bg-branding duration-300">Ver planes</a>
                    <a href="/#contacto" class="border-1 border-branding py-3 px-18 md:px-28 bg-branding/30 hover:bg-branding hover:text-white duration-300 rounded-2xl active:bg-branding-hover">Probar gratis</a>
                </div>
            </div>
            <div class="mt-10 flex gap-3 lg:gap-6 md:flex-row flex-col text-gray-600">
                <p><i class="fa-solid fa-check"></i> Implementación rápida</p>
                <p><i class="fa-solid fa-check"></i> Personalización low‑code</p>
                <p><i class="fa-solid fa-check"></i> Soporte humano</p>
            </div>
            <div class="hidden xl:mt-20 xl:flex flex-wrap justify-center items-center gap-6 xl:gap-20 w-full max-w-4xl px-4">
                <div class="flex flex-col items-center">
                    <div class="text-4xl md:text-6xl lg:text-8xl text-branding px-7 py-6 rounded-3xl shadow-[0px_0px_54px_0px_#4284f5] bg-white transform -rotate-6 border border-branding/70">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-4xl md:text-6xl lg:text-8xl text-branding px-7 py-6 rounded-3xl shadow-[0px_0px_54px_0px_#4284f5] bg-white transform rotate-6 border border-branding/70">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-4xl md:text-6xl lg:text-8xl text-branding px-8 py-6 rounded-3xl shadow-[0px_0px_54px_0px_#4284f5] bg-white transform rotate-3 border border-branding/70">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <div class="text-4xl md:text-6xl lg:text-8xl text-branding p-6 rounded-3xl shadow-[0px_0px_54px_0px_#4284f5] bg-white transform -rotate-3 border border-branding/70">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main class="mx-4 min-[1450px]:mx-25 mt-[10vh]">
        <div class="mt-10 lg:mt-15 border rounded-5xl font-aspekta">
            <div class="rounded-5xl overflow-hidden">
                <div class="bg-slate-50 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="size-3 rounded-full bg-red-400"></span>
                        <span class="size-3 rounded-full bg-yellow-400"></span>
                        <span class="size-3 rounded-full bg-emerald-400"></span>
                    </div>
                    <span class="text-xs text-slate-500">EnderSuite Dashboard</span>
                </div>
                <div class="px-6 py-10 bg-white">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs text-slate-500 mb-2">Ventas</p>
                            <p class="text-2xl font-bold">$ 128,450</p>
                            <p class="text-xs text-emerald-600 mt-1">+12% vs mes anterior</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 p-4">
                            <p class="text-xs text-slate-500 mb-2">Cuentas por cobrar</p>
                            <p class="text-2xl font-bold">$ 42,980</p>
                            <p class="text-xs text-amber-600 mt-1">8 facturas vencen en 7 días</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 p-4 col-span-2">
                            <p class="text-xs text-slate-500 mb-3">Pipeline de ventas</p>
                            <div class="flex gap-2">
                                <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full w-2/5 bg-branding"></div>
                                </div>
                                <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full w-1/3 bg-emerald-500"></div>
                                </div>
                                <div class="flex-1 h-2 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full w-1/4 bg-amber-500"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-3 text-xs text-gray-600">
                        <div class="rounded-xl bg-slate-50 p-3">Tickets abiertos: <span class="font-semibold">3</span></div>
                        <div class="rounded-xl bg-slate-50 p-3">NPS: <span class="font-semibold">62</span></div>
                        <div class="rounded-xl bg-slate-50 p-3">Rotación: <span class="font-semibold">2.1%</span></div>
                    </div>
                </div>
            </div>
        </div>
        <section class="mt-25 md:mt-40 text-center" id="caracteristicas">
            <p class="border rounded-full py-2 block md:inline-block md:px-40 border-branding bg-branding/30 text-branding-hover text-md">Características</p>
            <h2 class="mt-5 text-3xl md:text-5xl text-branding-hover font-[600]">Lo último en tecnología para cumplir tus necesidades</h2>
            <p class="mt-5 text-base md:text-xl">Crea flujos, formularios y reportes sin tocar código, o amplía con Python/Javascript cuando lo necesites.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                <div class="px-6 bg-primary p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 p-4 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-code"></i></div>
                    <h3 class="text-xl font-[600] mb-2">Low‑code / No‑code</h3>
                    <p class="text-gray-600">Adapta el sistema a tus procesos con nuestro editor low-code, sin necesidad de programar.</p>
                </div>
                <div class="px-6 bg-primary/60 p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 py-4 px-6 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <h3 class="text-xl font-[600] mb-2">Contabilidad local</h3>
                    <p class="text-gray-600">Plantillas y catálogos listos para México. Conciliación bancaria y reportes contables claros.</p>
                </div>
                <div class="px-6 bg-primary/60 p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 py-4 px-5 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-list-check"></i></div>
                    <h3 class="text-xl font-[600] mb-2">CRM + Ventas</h3>
                    <p class="text-gray-600">Pipeline, cotizaciones, facturas y seguimiento en un solo lugar con historial centralizado.</p>
                </div>
                <div class="px-6 bg-primary/60 p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 p-4 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-link"></i></div>
                    <h3 class="text-xl font-[600] mb-2">API‑first</h3>
                    <p class="text-gray-600">Integra con tu e‑commerce, bancos o BI. Webhooks y autenticación segura.</p>
                </div>
                <div class="px-6 bg-primary/60 p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 py-4 px-5 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-plus"></i></div>
                    <h3 class="text-xl font-[600] mb-2">Escalable</h3>
                    <p class="text-gray-600">Arquitectura lista para crecer contigo, con monitoreo y mejores prácticas de seguridad.</p>
                </div>
                <div class="px-6 bg-primary/60 p-6 rounded-6xl">
                    <div class="text-3xl text-branding mb-4 py-4 px-5 border inline-block rounded-full bg-branding/20"><i class="fa-solid fa-headset"></i></div>
                    <h3 class="text-xl font-[600] mb-2">Soporte humano</h3>
                    <p class="text-gray-600">Equipo local, SLA claros y asistencia en español. Nos importa tu operación.</p>
                </div>
            </div>
        </section>
        <section class="mt-25 md:mt-40 text-center" id="modulos">
            <p class="border rounded-full py-2 block md:inline-block md:px-40 border-branding bg-branding/30 text-branding-hover text-md">Modulos</p>
            <h2 class="mt-5 text-3xl md:text-5xl text-branding-hover font-[600]">Modulos integrados</h2>
            <p class="mt-5 text-base md:text-xl">Trabajan en armonía para darte control total: desde la primera oportunidad hasta el cierre contable.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-10">
                <div class="px-6 bg-primary p-6 rounded-6xl text-start">
                    <h3 class="text-xl font-[600] mb-2 text-center">Ventas</h3>
                    <p class="text-gray-600">Cotizaciones, pedidos, facturas, impuestos locales.</p>
                    <ul class="text-gray-600 text-left mt-4 list-disc list-inside">
                        <li>Listas de precio y descuentos</li>
                        <li>Flujos de aprobación</li>
                        <li>Reportes de margen</li>
                    </ul>
                </div>
                <div class="px-6 bg-primary p-6 rounded-6xl text-start">
                    <h3 class="text-xl font-[600] mb-2 text-center">Contabilidad</h3>
                    <p class="text-gray-600">Catálogo de cuentas, pólizas, conciliación.</p>
                    <ul class="text-gray-600 text-left mt-4 list-disc list-inside">
                        <li>Estado de resultados y balance</li>
                        <li>CFDI (proximamente)</li>
                        <li>Activos fijos</li>
                    </ul>
                </div>
                <div class="px-6 bg-primary p-6 rounded-6xl text-start">
                    <h3 class="text-xl font-[600] mb-2 text-center">CRM</h3>
                    <p class="text-gray-600">Leads, oportunidades y pipelines personalizables.</p>
                    <ul class="text-gray-600 text-left mt-4 list-disc list-inside">
                        <li>Tareas y recordatorios</li>
                        <li>Email tracking (próximamente)</li>
                        <li>Embudo por etapas</li>
                    </ul>
                </div>
                <div class="px-6 bg-primary p-6 rounded-6xl text-start">
                    <h3 class="text-xl font-[600] mb-2 text-center">Inventarios (próx.)</h3>
                    <p class="text-gray-600">Productos, almacenes y valuación.</p>
                    <ul class="text-gray-600 text-left mt-4 list-disc list-inside">
                        <li>Entradas/salidas</li>
                        <li>Series y lotes</li>
                        <li>Reorden automático</li>
                    </ul>
                </div>
            </div>
        </section>
        <section class="mt-25 md:mt-40 text-center" id="planes">
            <p class="border rounded-full py-2 block md:inline-block md:px-40 border-branding bg-branding/30 text-branding-hover text-md">Precios</p>
            <h2 class="mt-5 text-3xl md:text-5xl text-branding-hover font-[600]">Precios simples, planes buenos</h2>
            <p class="mt-5 text-base md:text-xl">Comienza gratis. Planes que crecen contigo.</p>
            <div class="mt-12 grid md:grid-cols-3 gap-6">
                <div class="rounded-6xl bg-white border border-primary-hover p-6 flex flex-col">
                    <h3 class="font-semibold font-aspekta text-3xl">Gratis</h3>
                    <p class="text-sm mt-4 text-gray-600">Para validar tu operación</p>
                    <p class="mt-4 text-4xl font-extrabold">$0</p>
                    <ul class="mt-4 space-y-2 text-sm text-gray-600 list-disc list-inside">
                        <li>2 usuarios</li>
                        <li>Ventas y CRM básicos</li>
                        <li>Soporte comunitario</li>
                    </ul>
                    <a href="/auth/signup" class="mt-6 inline-flex justify-center rounded-2xl border border-primary-hover px-4 py-3 hover:bg-primary active:bg-primary-hover transition-all">Empezar</a>
                </div>
                <div class="rounded-6xl bg-white border-2 border-branding p-6 flex flex-col relative">
                    <span class="absolute -top-3 right-4 rounded-full bg-branding text-white text-xs px-3 py-1">Recomendado</span>
                    <h3 class="font-semibold font-aspekta text-3xl">PYME</h3>
                    <p class="text-sm mt-4 text-gray-600">Para equipos en crecimiento</p>
                    <p class="mt-4 text-4xl font-extrabold">$1,499 <span class="text-base font-medium text-slate-500">/mes</span></p>
                    <ul class="mt-4 space-y-2 text-sm text-gray-600 list-disc list-inside">
                        <li>10 usuarios</li>
                        <li>Ventas, Contabilidad y CRM</li>
                        <li>Soporte estándar</li>
                    </ul>
                    <a href="#contacto" class="mt-6 inline-flex justify-center rounded-2xl bg-branding text-white px-4 py-3 hover:bg-branding-hover- transition-all">Probar PYME</a>
                </div>
                <div class="rounded-6xl bg-white border border-primary-hover p-6 flex flex-col">
                    <h3 class="font-semibold font-aspekta text-3xl">Empresa</h3>
                    <p class="text-sm mt-4 text-gray-600">Para necesidades avanzadas</p>
                    <p class="mt-4 text-4xl font-extrabold">A la medida</p>
                    <ul class="mt-4 space-y-2 text-sm text-gray-600 list-disc list-inside">
                        <li>Usuarios ilimitados</li>
                        <li>Integraciones y SLA</li>
                        <li>Soporte prioritario</li>
                    </ul>
                    <a href="#contacto" class="mt-6 inline-flex justify-center rounded-2xl border border-primary-hover px-4 py-3 hover:bg-primary active:bg-primary-hover transition-all">Hablar con ventas</a>
                </div>
            </div>
        </section>
        <section class="mt-25 md:mt-40 text-center" id="recursos">
            <p class="border rounded-full py-2 block md:inline-block md:px-40 border-branding bg-branding/30 text-branding-hover text-md">Recursos</p>
            <h2 class="mt-5 text-3xl md:text-5xl text-branding-hover font-[600]">Obtén ayuda e información con nuestros recursos</h2>
            <p class="mt-5 text-base md:text-xl">Infórmate del funcionamiento del ERP y obtén ayuda a través de la comunidad o desde soporte privado.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-10">
                <a href="/" target="_blank" class="flex flex-col justify-between p-5 rounded-6xl bg-primary bg-center bg-no-repeat card cursor-pointer">
                    <span class="block relative w-full text-right"><i class="fa-solid fa-arrow-right"></i></span>
                    <h3 class="text-2xl -mt-9 font-semibold">Documentación</h3>
                    <p class="my-5">Guías para admins y usuarios. APIs y webhooks para integraciones.</p>
                    <p class="border-2 w-full text-center py-2 rounded-3xl cardBtn text-[17px]">Ir a docs</p>
                </a>
                <a href="/" target="_blank" class="flex flex-col justify-between p-5 rounded-6xl bg-primary bg-center bg-no-repeat card cursor-pointer">
                    <span class="block relative w-full text-right"><i class="fa-solid fa-arrow-right"></i></span>
                    <h3 class="text-2xl -mt-9 font-semibold">Soporte</h3>
                    <p class="my-5">SLA claros, mesa de ayuda y base de conocimiento en español.</p>
                    <p class="border-2 w-full text-center py-2 rounded-3xl cardBtn text-[17px]">Centro de ayuda</p>
                </a>
                <a href="https://discord.gg/jpMUCJcN66" target="_blank" class="flex flex-col justify-between p-5 rounded-6xl bg-primary bg-center bg-no-repeat card cursor-pointer">
                    <span class="block relative w-full text-right"><i class="fa-solid fa-arrow-right"></i></span>
                    <h3 class="text-2xl -mt-9 font-semibold">Comunidad</h3>
                    <p class="my-5">Eventos, tutoriales y partners que impulsan tu crecimiento.</p>
                    <p class="border-2 w-full text-center py-2 rounded-3xl cardBtn text-[17px]">Unirme al Discord</p>
                </a>
            </div>
        </section>
        <section id="contacto" class="mt-25 md:mt-40 text-center mb-20">
            <p class="border rounded-full py-2 block md:inline-block md:px-40 border-branding bg-branding/30 text-branding-hover text-md">Contacto</p>
            <h2 class="mt-5 text-3xl md:text-5xl text-branding-hover font-[600]">¿Listo para transformar tu negocio?</h2>
            <p class="mt-5 text-base md:text-xl">Contáctanos para una demo personalizada o resolver tus dudas.</p>
            <a href="mailto:hola@rendercores.online" class="mt-6 inline-flex justify-center rounded-2xl bg-branding text-white px-6 py-3 hover:bg-branding-hover transition-all">Contactar ventas</a>
        </section>
    </main>
    <footer class="flex flex-col my-10 bg-primary/60 rounded-5xl p-10 mx-4 min-[1450px]:mx-25">
        <div class="flex flex-col lg:flex-row justify-between ">
            <div>
                <h2 class="text-4xl flex gap-3"><img src="https://rendercores.com/assets/img/logo.png" alt="Logo azul y blanco de RenderCores" class="w-12 h-12" loading="lazy" decoding="async"><span class="mt-1">RenderCores</span></h2>
                <h3 class="text-[19px] text-gray-500 mt-5">Tu servidor, nuestro compromiso</h3>
                <div class="mt-5">
                    <a href="https://discord.com/invite/QKVE898psb" class="rounded-full border border-white text-white hover:bg-white hover:text-primary duration-300" style="padding: 5px 6px;"><i class="fa-brands fa-discord"></i></a>
                    <a href="https://x.com/RenderCores" class="rounded-full border border-white text-white hover:bg-white hover:text-primary duration-300" style="padding: 5px 8px;"><i class="fa-brands fa-twitter"></i></a>
                    <a href="https://facebook.com/profile.php?id=61559791887146" class="rounded-full border border-white text-white hover:bg-white hover:text-primary duration-300 outline-0" style="padding: 5px 8px;"><i class="fa-brands fa-facebook"></i></a>
                    <a href="https://instagram.com/enderhostingmx" class="rounded-full border border-white text-white hover:bg-white hover:text-primary duration-300" style="padding: 5px 9px;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.youtube.com/@rendercores" class="rounded-full border border-white text-white hover:bg-white hover:text-primary duration-300" style="padding: 5px 8px;"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>
            <div class="flex flex-col text-center mt-10 lg:mt-0 lg:text-left ml-0 lg:ml-[60px]">
                <h3 class="font-bold text-xl">Enlaces</h3>
                <a href="/#caracteristicas" class="text-gray-500 hover:underline">Características</a>
                <a href="/#modulos" class="text-gray-500 hover:underline">Módulos</a>
                <a href="/#planes" class="text-gray-500 hover:underline">Precios</a>
                <a href="/#recursos" class="text-gray-500 hover:underline">Recursos</a>
            </div>
            <div class="flex flex-col text-center mt-10 lg:mt-0 lg:text-left ml-0 lg:ml-[60px]">
                <h3 class="font-bold text-xl">Contacto</h3>
                <a href="mailto:hola@rendercores.online" class="text-gray-500 hover:underline">hola@rendercores.online</a>
                <a href="tel:+523113935413" class="text-gray-500 hover:underline">+52 311-393-5413</a>
            </div>
        </div>
        <hr class="lg:block border-gray-300 w-full my-5 ">
        <p>© 2025 RenderCores. Todos los derechos reservados. <a href="https://rendercores.online/terms.html" class="text-blue-500 font-semibold hover:underline">Términos de servicio</a> <a href="https://rendercores.online/privacy.html" class="text-blue-500 font-semibold hover:underline">Política de privacidad</a></p>
    </footer>
    <script src="./assets/js/mobileMenu.js"></script>
</body>
</html>