<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Fallback to standard POST
    if (!$data) {
        $data = $_POST;
    }

    $name = strip_tags(trim($data["name"] ?? ''));
    $phone = strip_tags(trim($data["phone"] ?? ''));
    $email = filter_var(trim($data["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $interest = strip_tags(trim($data["interest"] ?? ''));
    $message = strip_tags(trim($data["message"] ?? ''));

    // Validation
    if (empty($name) || empty($phone) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Datos inválidos o incompletos."]);
        exit;
    }

    // --- Recipients ---
    $recipients = "Infobiaja@gmail.com, hectorgregory@gmail.com";

    // --- Subject ---
    $subject = "=?UTF-8?B?" . base64_encode("Nuevo Mensaje de Contacto - $name") . "?=";

    // --- HTML Email Body (matching original design) ---
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body style="margin:0; padding:0; background-color:#f4f4f4; font-family: Arial, Helvetica, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding: 30px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        
                        <!-- Header -->
                        <tr>
                            <td style="background-color:#DD5426; padding: 25px 30px; text-align:center;">
                                <h1 style="color:#ffffff; margin:0; font-size:22px; font-weight:bold;">Nuevo Mensaje de Contacto</h1>
                            </td>
                        </tr>
                        
                        <!-- Body -->
                        <tr>
                            <td style="padding: 30px;">
                                
                                <!-- Nombre -->
                                <p style="margin:0 0 5px 0; font-size:13px; font-weight:bold; color:#333;">Nombre:</p>
                                <div style="background-color:#fff; border:2px solid #DD5426; border-radius:6px; padding:12px 15px; margin-bottom:20px; font-size:14px; color:#333;">' . htmlspecialchars($name) . '</div>
                                
                                <!-- Email -->
                                <p style="margin:0 0 5px 0; font-size:13px; font-weight:bold; color:#333;">Email:</p>
                                <div style="background-color:#fff; border:2px solid #DD5426; border-radius:6px; padding:12px 15px; margin-bottom:20px; font-size:14px; color:#DD5426;">' . htmlspecialchars($email) . '</div>
                                
                                <!-- Teléfono -->
                                <p style="margin:0 0 5px 0; font-size:13px; font-weight:bold; color:#333;">Teléfono:</p>
                                <div style="background-color:#fff; border:2px solid #DD5426; border-radius:6px; padding:12px 15px; margin-bottom:20px; font-size:14px; color:#333;">' . htmlspecialchars($phone) . '</div>
                                
                                <!-- Interés -->
                                <p style="margin:0 0 5px 0; font-size:13px; font-weight:bold; color:#333;">Interés:</p>
                                <div style="background-color:#fff; border:2px solid #DD5426; border-radius:6px; padding:12px 15px; margin-bottom:20px; font-size:14px; color:#333;">' . htmlspecialchars($interest) . '</div>
                                
                                <!-- Mensaje -->
                                <p style="margin:0 0 5px 0; font-size:13px; font-weight:bold; color:#333;">Mensaje:</p>
                                <div style="background-color:#fff; border:2px solid #DD5426; border-radius:6px; padding:12px 15px; margin-bottom:20px; font-size:14px; color:#333;">' . nl2br(htmlspecialchars($message)) . '</div>
                                
                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style="background-color:#f9f9f9; padding:15px 30px; text-align:center; border-top:1px solid #eee;">
                                <p style="margin:0 0 5px 0; font-size:13px; color:#DD5426; font-weight:bold;">Enviado desde el sitio web de Biaja Tours</p>
                                <p style="margin:0; font-size:11px; color:#999;">Este es un mensaje automático, por favor no responder directamente a este correo si es una notificación del sistema.</p>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    // --- Headers ---
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Biaja Tours <noreply@biajatours.com>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";

    // --- Send ---
    if (mail($recipients, $subject, $html, $headers)) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Mensaje enviado correctamente."]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Hubo un problema al enviar el correo. Intenta por WhatsApp."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}
?>