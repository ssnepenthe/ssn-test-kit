<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>JavaScript DOM Modification Test</title>
</head>
<body>
	<h1>Test Server Home</h1>
	<p>This is without JavaScript.</p>

	<script>
		document.querySelector('p').innerHTML = 'This is with JavaScript.';
	</script>
</body>
</html>