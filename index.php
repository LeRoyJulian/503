<?php
header('HTTP/1.1 503 Service Temporarily Unavailable');
header("Retry-After: 86400");

use PHPMailer\PHPMailer\PHPMailer;
use Dotenv\Dotenv;

require 'vendor/autoload.php';
$dotenv = Dotenv::createImmutable(__DIR__)->load();
$mail = new PHPMailer(true);

$sent = false; 
$error = false;

if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
	try {
		$mail->isSMTP();
		$mail->Host       = $_ENV['SMTP_HOST'];  // Specify main and backup SMTP servers
		$mail->SMTPAuth   = true;                // Enable SMTP authentication
		$mail->Username   = $_ENV['SMTP_USER'];  // SMTP username
		$mail->Password   = $_ENV['SMTP_PASS'];  // SMTP password
		$mail->SMTPSecure = 'tls';               // Enable TLS encryption, `ssl` also accepted

		//Recipients
		$mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
		$mail->addAddress($_ENV['MAIL_TO']);
		$mail->addReplyTo($_POST['email'], $_POST['name']);
		//Content
		$mail->Subject = 'Formulaire de contact';
		$mail->Body = $_POST['message'];
		$mail->AltBody = $_POST['message'];

		$mail->send();
		$sent = true; 
	} catch (Exception $e) {
		$error = true;
	}
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<title><?php echo $_ENV['SITE_NAME']; ?></title>
	<style>
		body {
			background: <?php echo $_ENV['SITE_BACKGROUND']; ?>;
			font-family: Raleway;
		}
	</style>
</head>

<body class="py-4">
	<div class="container mx-auto" style="max-width: 740px;">
		<div class="text-center mb-4">
			<img src="<?php echo $_ENV['SITE_LOGO']; ?>" alt="<?php echo $_ENV['SITE_NAME']; ?>" height="54" />
		</div>
		<div class="bg-white mb-3 p-3">
			<p class="mb-0">Notre site est temporairement indisponible, veuillez nous en excuser. Vous pouvez nous contacter par email en utilisant le formulaire ci-dessous.</p>
		</div>
		<?php if ($error) { ?>
			<div class="alert alert-danger">Votre message n'a pas pu être envoyé.</div>
		<?php } ?>
		<?php if ($sent) { ?>
			<div class="alert alert-success">Votre message a bien été envoyé.</div>
		<?php } else { ?>
		<form class="bg-white p-3" method="POST">
			<div class="form-group">
				<label for="name">Votre nom</label>
				<input type="text" name="name" class="form-control" required />
			</div>
			<div class="form-group">
				<label for="name">Votre email</label>
				<input type="text" name="email" class="form-control" required />
			</div>
			<div class="form-group">
				<label for="name">Votre message</label>
				<textarea class="form-control" name="message" rows="6" required></textarea>
			</div>
			<button type="submit" class="btn btn-success">Envoyer</button>
		</form>
		<?php } ?>
	</div>

</body>

</html>