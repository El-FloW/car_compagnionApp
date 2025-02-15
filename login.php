<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Compagnion App</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='./css/main.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/cadre.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/floatingButton.css'>
    <link rel="stylesheet" type='text/css' media='screen' href='./css/form.css'>
    <script src='main.js'></script>

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Inline&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
</head>

<?php
session_start();
require "config.php"; // Connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Vérifier si l'email existe
    $stmt = $pdo->prepare("SELECT id, name, password FROM ca_user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // L'utilisateur existe, demander le mot de passe
        if (isset($_POST["password"])) {
            if (password_verify($_POST["password"], $user["password"])) {
                // Connexion réussie, enregistrer en session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $email;
                echo "✅ Connexion réussie, bienvenue " . $_SESSION["user_name"] . " !";
                // Redirige l'utilisateur vers la page de connexion ou d'accueil
                header("Location: index.php");
                exit;
            } else {
                echo "❌ Mot de passe incorrect.";
            }
        } else {
            echo '
            <section class="form">
                <form method="post">
                    <input type="hidden" name="email" value="' . $email . '">
                    <label>Mot de passe :</label>
                    <input type="password" name="password" required>
            </section>
            <section class="submitSection">
                    <button type="submit" class="submit">Se connecter</button>
            </section>
                </form>
            ';
        }
    } else {
        // L'utilisateur n'existe pas, demander l'inscription
        if (isset($_POST["name"]) && isset($_POST["password"])) {
            $name = trim($_POST["name"]);
            $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash du mot de passe

            // Insérer l'utilisateur en base de données
            $stmt = $pdo->prepare("INSERT INTO ca_user (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);

            // Récupérer l'ID de l'utilisateur et enregistrer en session
            $_SESSION["user_id"] = $pdo->lastInsertId();
            $_SESSION["user_name"] = $name;
            $_SESSION["user_email"] = $email;
            echo "✅ Inscription réussie, bienvenue " . $_SESSION["user_name"] . " !";
            // Redirige l'utilisateur vers la page de connexion ou d'accueil
            header("Location: index.php");
            exit;
        } else {
            echo '
            <section class="form">
                <form method="post">
                    <input type="hidden" name="email" value="' . $email . '">
                    <label>Nom :</label>
                    <input type="text" name="name" required>
                    <label>Mot de passe :</label>
                    <input type="password" name="password" required>
            </section>
            <section class="submitSection">
                    <button type="submit" class="submit">Créer un compte</button>
            </section>
                </form>
            ';
        }
    }
} else {
    // Demander l'email au début
    echo '
    <section class="form">
        <form method="post">
            <label>Email :</label>
            <input type="email" name="email" required>
    </section>
    <section class="submitSection">
            <button type="submit" class="submit">Continuer</button>
    </section>
        </form>
    ';
}
?>
