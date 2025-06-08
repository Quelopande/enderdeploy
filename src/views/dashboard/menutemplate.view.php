    <nav>
        <div class="topbar">
            <div class="logo">
                <img src="/assets/img/logo.png" alt="Logo">
                <p>Deploy</p>
            </div>
            <div class="updetails">
            <p><?php echo htmlspecialchars(trim($result['email']), ENT_QUOTES, 'UTF-8') ?></p>
            <p>Dashboard</p>
            </div>
        </div>
        <div class="sidebar">
            <ul>
                <a href="/dashboard/"><li id="1"><i class="fa-solid fa-table-columns"></i> <p class="overTxt">Panel de control</p></li></a>
                <a href="/dashboard/support"><li id="2"><i class="fa-regular fa-life-ring"></i> <p class="overTxt" id="overTxt">Soporte</p></li></a>
                <a href=""><li id="3"><i class="fa-solid fa-chart-line"></i> <p class="overTxt">Estado servicio</p></li></a>
                <a href="/dashboard/services"><li id="4"><i class="fa-solid fa-plus"></i> <p class="overTxt">Nuevo Servicio</p></li></a>
                <a href="/dashboard/settings"><li id="5"><i class="fa-solid fa-gear"></i> <p class="overTxt">Opciones</p></li></a>
                <?php if($result['role'] != '-1') {
                    echo '<a href="/staffPanel"><li id="5"><i class="fa-solid fa-person"></i> <p class="overTxt">Staff</p></li></a>';
                } ?>
            </ul>
        </div>
    </nav>