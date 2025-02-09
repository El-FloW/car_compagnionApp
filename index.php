<!DOCTYPE html>
<?php
    session_start();
    require "config.php"; // Connexion à la base de données
?>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Compagnion App</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/main.css'>
    <script src='main.js'></script>
</head>
<body>
    <?php
    if($_SESSION["user_id"]) :
    ?>
    <section>
        <h2>Bienvenue</h2>
        <p>Bienvenue <?php echo $_SESSION['user_name']?></p>
    </section>
    <?php
    else :
    ?>
    <section>
        <h2>Connecte toi !</h2>
        <a href="./login.php">Connexion</a>
    </section>
    <?php
    endif;
    ?>
    
    <?php
    $user_id = $_SESSION["user_id"];

    // Récupérer les voitures de l'utilisateur
    $stmt = $pdo->prepare("SELECT id, car_name, kilometrage FROM ca_car WHERE fk_user = ?");
    $stmt->execute([$user_id]);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<section>";
    echo "<h2>Mes véhicules</h2>";

    if (empty($cars)) {
        echo "<a href='./addCar.php'>Ajouter un véhicule</a>";
    } else {
        echo "<ul>";
        foreach ($cars as $car) {
            echo "<li><strong>Voiture :</strong> " . htmlspecialchars($car["car_name"]) . 
                " | <strong>Kilométrage :</strong> " . htmlspecialchars($car["kilometrage"]) . " km</li>";
        }
        echo "</ul>";
    }
    echo "</section>";
    ?>

</body>
</html>