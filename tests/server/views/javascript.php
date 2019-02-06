<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Test Server Home</title>
</head>
<body>
	<h1>Test Server Home</h1>
	<p>This is without JavaScript.</p>

	<script>
		let enabled = <?= json_encode( $status === 'enabled' ) ?>;

		if (enabled) {
			document.querySelector('p').innerHTML = 'This is with JavaScript.';
		}
	</script>
</body>
</html>