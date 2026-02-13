<?php
// contact.php - Backend for Biaja Tours Contact Form

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON input
    $input = json_decode(file_get_contents("php://input"), true);

    // Sanitize and Validate
    $nombre = strip_tags(trim($input["nombre"]));
    $telefono = strip_tags(trim($input["telefono"]));
    $email = filter_var(trim($input["email"]), FILTER_SANITIZE_EMAIL);
    $interes = strip_tags(trim($input["interes"]));
    $mensaje = strip_tags(trim($input["mensaje"]));
    $honeypot = isset($input["website"]) ? trim($input["website"]) : '';

    // HONEYPOT TRAP: If the hidden field has value, it's a bot.
    if (!empty($honeypot)) {
        // Pretend it worked to fool the bot
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "¡Mensaje enviado con éxito!"]);
        exit;
    }

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Por favor completa los campos obligatorios."]);
        exit;
    }

    // Email Configuration
    // Email Configuration
    $to = "hectorgregory@gmail.com"; // User's preferred email
    $subject = "Nuevo Mensaje Web: $interes - $nombre";

    // HTML Email Content
    $email_content = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; }
            .header { background-color: #DD5426; color: #fff; padding: 20px; text-align: center; }
            .header h2 { margin: 0; }
            .content { padding: 20px; background-color: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
            .value { background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
            .footer { background-color: #f1f1f1; padding: 10px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #e0e0e0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Nuevo Mensaje de Contacto</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Nombre:</span>
                    <div class='value'>$nombre</div>
                </div>
                <div class='field'>
                    <span class='label'>Email:</span>
                    <div class='value'><a href='mailto:$email' style='color: #DD5426; text-decoration: none;'>$email</a></div>
                </div>
                <div class='field'>
                    <span class='label'>Teléfono:</span>
                    <div class='value'>$telefono</div>
                </div>
                <div class='field'>
                    <span class='label'>Interés:</span>
                    <div class='value'>$interes</div>
                </div>
                <div class='field'>
                    <span class='label'>Mensaje:</span>
                    <div class='value'>" . nl2br($mensaje) . "</div>
                </div>
            </div>
            <div class='footer'>
                Enviado desde el sitio web de Biaja Tours<br>
                <small>Este es un mensaje automático, por favor no responder directamente a este correo si es una notificación del sistema.</small>
            </div>
        </div>
    </body>
    </html>
    ";

    // HTML Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: Biaja Web <webmaster@biajatours.com>" . "\r\n";
    $headers .= "Reply-To: $email" . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send Email
    if (mail($to, $subject, $email_content, $headers)) {
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "¡Mensaje enviado con éxito!"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Error al enviar el mensaje. Inténtalo más tarde."]);
    }

} else {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>