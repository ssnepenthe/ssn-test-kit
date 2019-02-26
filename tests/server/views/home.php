<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Test Server Home</title>

	<style>
		.hidden { display: none; }
	</style>
</head>
<body>
	<h1>Home</h1>
	<p>This is a paragraph</p>
	<p class="js-delayed-visibility hidden">This is hidden by default</p>

	<script>
		setTimeout(() => {
			let el = document.querySelector('.js-delayed-visibility');

			el.innerText = 'This is visible now';
			el.classList.remove('hidden');
		}, 1500);
	</script>
</body>
</html>