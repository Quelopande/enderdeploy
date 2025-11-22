<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnderDeploy - Staff Panel | Tickets [Confidencial]</title>
    <link rel="stylesheet" href="/assets/styles/staffPanel/tickets.css">
    <link href="https://pro.fontawesome.com/releases/v6.0.0-beta1/css/all.css" rel="stylesheet"/>
    <link rel="website icon" type="ico" href="/assets/img/logo.ico">
</head>
<body>
    <div class="menu">
        <h2><a href="/staffPanel" style="text-decoration: none;">StaffPanel</a></h2>
        <div>
            <a href="/staffPanel/tickets">Tickets</a>
            <a href="/staffPanel/services">Servicios</a>
            <a href="/staffPanel/users">Usuarios</a>
        </div>
    </div>
    <h1>Tickets</h1>
    <a href="/staffPanel/tickets?filter=noResponseAlert">Sin respuesta</a>
    <a href="/staffPanel/tickets?filter=withResponse">Con respuesta</a>
    <a href="/staffPanel/tickets">Limpiar filtros</a>
    <div class="tickets">
        <?php 
        foreach ($messages as $message) {
            $responseStatement = $connection->prepare('SELECT * FROM helpanswers WHERE messageId = :messageId ORDER BY creationDate DESC LIMIT 1');
            $responseStatement->execute([':messageId' => $message['messageId']]);
            $response = $responseStatement->fetch(PDO::FETCH_ASSOC);

            $lastResponseId = $response ? $response['answerPublisherId'] : null;

            $noResponseAlert = "<p class='noResponse'>&#9888; Sin respuesta &#9888;</p>";

            if ($lastResponseId !== null) {
                $staffCheckstatement = $connection->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
                $staffCheckstatement->execute(array(':id' => $lastResponseId));
                $staffCheckresult = $staffCheckstatement->fetch(PDO::FETCH_ASSOC);

                if ($staffCheckresult && intval($staffCheckresult['role']) != -1) {
                    $noResponseAlert = "";
                }
            }
            if (!isset($_GET['filter']) || ($_GET['filter'] == "noResponseAlert" && !empty($noResponseAlert)) || ($_GET['filter'] == "withResponse" && empty($noResponseAlert))) {
                echo "
                <a class='ticket' href='/dashboard/supportMsg?msg=" . htmlspecialchars($message['messageId'], ENT_QUOTES, 'UTF-8') . "'>
                    <span class='status'>" . htmlspecialchars($message['status'], ENT_QUOTES, 'UTF-8') . "</span>
                    <h2>" . htmlspecialchars($message['title'], ENT_QUOTES, 'UTF-8') . "</h2>
                    <p class='description'>" . substr(htmlspecialchars($message['content'], ENT_QUOTES, 'UTF-8'), 0, 50) . "</p>" . $noResponseAlert . "
                </a>";
            }
        }
        ?>
</body>
</html>