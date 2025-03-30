<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et sécuriser les données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validation des champs
    if (empty($nom) || empty($email) || empty($message)) {
        echo "Tous les champs doivent être remplis.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'adresse e-mail n'est pas valide.";
        exit;
    }

    // Configuration de l'email
    $to = "fr.hugoalves@gmail.com"; // Adresse de destination
    $subject = "Nouveau message du formulaire de contact";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Corps du message
    $message_body = "
    <html>
    <head>
        <title>Nouveau message</title>
    </head>
    <body>
        <h2>Message reçu via le formulaire de contact</h2>
        <p><strong>Nom :</strong> $nom</p>
        <p><strong>Email :</strong> $email</p>
        <p><strong>Message :</strong> $message</p>
    </body>
    </html>
    ";

    // Envoi de l'email
    if (mail($to, $subject, $message_body, $headers)) {
        echo "Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.";
    } else {
        echo "Désolé, un problème est survenu lors de l'envoi de votre message. Veuillez réessayer plus tard.";
    }
}
?>
