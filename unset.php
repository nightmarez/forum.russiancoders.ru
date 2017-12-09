<?php include_once('head.php'); ?>
<?php include_once('nav.php'); ?>

<script>
	//$(document).ready(function() {
		var names = ['userid', 'session'];

		// remove data from localstorage
		_.each(names, function(name) {
			if (!_.isNull(localStorage.getItem(name))) {
				localStorage.removeItem(name);
			}
		});

		// remove data from cookies
		var date = new Date(0);
		_.each(names, function(name) {
			document.cookie = name + '=; path=/; expires=' + date.toUTCString();
		});

		// redirect
		location.href = '/';
	//});
</script>

<?php include_once('footer.php'); ?>