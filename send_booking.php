<?php
// send_booking.php
// Simple handler to send flight booking details via email

// Configuration
$to = 'info@boomingplacetravelsandtours.com';
$subject = 'New Flight Booking Request';

// Helper: sanitize
function field($key) {
    return isset($_POST[$key]) ? trim(strip_tags((string)$_POST[$key])) : '';
}

$flightScope       = field('flight_scope'); // Local or International
$tripType          = field('trip_type');    // roundtrip or oneway
$from              = field('from');
$toDest            = field('to');
$departureDate     = field('departure_date');
$returnDate        = field('return_date');
$adults            = field('adults');
$children          = field('children');
$travelClass       = field('travel_class');
$phone             = field('phone');
$additional        = field('additional_requirements');

// Basic validation
$errors = [];
if ($from === '') $errors[] = 'From location is required';
if ($toDest === '') $errors[] = 'To location is required';
if ($departureDate === '') $errors[] = 'Departure date is required';
if ($tripType !== 'oneway' && $tripType !== 'roundtrip') $errors[] = 'Trip type is invalid';
if ($tripType === 'roundtrip' && $returnDate === '') $errors[] = 'Return date is required for round trip';
if ($adults === '' || !is_numeric($adults) || (int)$adults < 1) $errors[] = 'Adults must be at least 1';
if ($phone === '') $errors[] = 'Phone number is required';

if (!empty($errors)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['ok' => false, 'errors' => $errors]);
    exit;
}

// Build HTML email (aesthetic)
$brandPrimary = '#0f172a';
$brandAccent = '#12b981';
$brandSecondary = '#38bdf8';
$bg = '#f6f9fc';
$cardBg = '#ffffff';
$muted = '#64748b';

$html = "<!DOCTYPE html>
<html lang=\"en\">
<head>
<meta charset=\"UTF-8\" />
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />
<title>New Flight Booking</title>
<link rel=\"preconnect\" href=\"https://fonts.googleapis.com\">
<link rel=\"preconnect\" href=\"https://fonts.gstatic.com\" crossorigin>
<link href=\"https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap\" rel=\"stylesheet\">
</head>
<body style=\"margin:0;background:$bg;font-family:Poppins,Arial,Helvetica,sans-serif;\">
  <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"background:$bg;padding:32px 12px;\">
    <tr>
      <td align=\"center\">
        <table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" style=\"max-width:720px;background:$cardBg;border-radius:16px;box-shadow:0 10px 30px rgba(2,6,23,0.08);overflow:hidden;\">
          <tr>
            <td style=\"padding:28px 32px;background:linear-gradient(135deg,$brandPrimary 0%,$brandSecondary 50%,$brandAccent 100%);color:#fff;\">
              <table width=\"100%\">
                <tr>
                  <td>
                    <h1 style=\"margin:0;font-size:22px;line-height:1.3;\">New Flight Booking Request</h1>
                    <p style=\"margin:6px 0 0;color:rgba(255,255,255,0.85);\">Booming Place Travels & Tours</p>
                  </td>
                  <td align=\"right\">
                    <span style=\"display:inline-block;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,0.12);color:#fff;font-weight:600;font-size:12px;letter-spacing:.3px;\">" . htmlspecialchars($flightScope ?: 'Local') . "</span>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style=\"padding:28px 32px;\">
              <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:separate;border-spacing:0 12px;\">
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">Trip Type</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars(ucfirst($tripType)) . "</td>
                </tr>
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">From</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars($from) . "</td>
                </tr>
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">To</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars($toDest) . "</td>
                </tr>
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">Departure</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars($departureDate) . "</td>
                </tr>
                " . ($tripType === 'roundtrip' ? "<tr><td style=\"color:$muted;font-size:12px;\">Return</td><td style=\"font-weight:600;\">" . htmlspecialchars($returnDate) . "</td></tr>" : "") . "
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">Passengers</td>
                  <td style=\"font-weight:600;\">Adults: " . htmlspecialchars($adults) . " | Children: " . htmlspecialchars($children !== '' ? $children : '0') . "</td>
                </tr>
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">Class</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars($travelClass ?: 'Economy') . "</td>
                </tr>
                <tr>
                  <td style=\"color:$muted;font-size:12px;\">Phone</td>
                  <td style=\"font-weight:600;\">" . htmlspecialchars($phone) . "</td>
                </tr>
              </table>

              <div style=\"margin-top:24px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;\">
                <div style=\"color:$muted;font-size:12px;margin-bottom:6px;\">Additional Requirements</div>
                <div style=\"font-size:14px;line-height:1.6;\">" . nl2br(htmlspecialchars($additional ?: 'None provided')) . "</div>
              </div>

              <div style=\"margin-top:28px;text-align:center;\">
                <a href=\"tel:+2349065212188\" style=\"display:inline-block;background:$brandAccent;color:#fff;text-decoration:none;padding:12px 18px;border-radius:999px;font-weight:700;box-shadow:0 10px 20px rgba(16,185,129,0.25);\">Call Customer Support</a>
              </div>
            </td>
          </tr>
          <tr>
            <td style=\"padding:16px 32px;background:#0b1220;color:#94a3b8;font-size:12px;\">
              <table width=\"100%\">
                <tr>
                  <td>
                    Booming Place Travels & Tours<br/>
                    <span style=\"color:#64748b\">New Booking Notification</span>
                  </td>
                  <td align=\"right\">www.boomingplacetravelsandtours.com</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>";

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Booming Place Travels <no-reply@boomingplacetravelsandtours.com>\r\n";
$headers .= "Reply-To: Booming Place Travels <no-reply@boomingplacetravelsandtours.com>\r\n";

$sent = @mail($to, $subject, $html, $headers);

if ($sent) {
    // Redirect back with success message
    header('Location: index.html?status=success');
} else {
    header('Location: index.html?status=error');
}
exit;
