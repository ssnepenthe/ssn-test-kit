<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>JavaScript DOM Modification Test</title>

	<style>
		.hidden {
			display: none;
		}
	</style>
</head>
<body>
	<h1>Test Server Home</h1>
	<p class="test hidden">This is without JavaScript.</p>

	<script>
		setTimeout(() => {
			document.querySelector('p').classList.remove('hidden');
		}, 3000);
	</script>
</body>
</html>