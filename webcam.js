//$(document).ready(function() {
document.addEventListener("DOMContentLoaded", function() {
	var video = document.getElementById('video');

	if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
		// Not adding `{ audio: true }` since we only want video now
		navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
			video.src = window.URL.createObjectURL(stream);
			video.play();
		});
	}

	// Elements for taking the snapshot
	var canvas = document.getElementById('canvas');
	var context = canvas.getContext('2d');

	// Trigger photo take
	document.getElementById("snap").addEventListener("click", function() {
		context.drawImage(video, 0, 0, 640, 480);
		document.getElementById("snap").disabled = true;
		document.getElementById("save").disabled = false;
	});

	// Save photo
	document.getElementById("save").addEventListener("click", function() {
		var dataURL = canvas.toDataURL('image/jpeg');

		$.ajax({
			type: "POST",
			url: "/upload.php",
			data: { 
				imgUrl: dataURL
			}
		}).done(function(o) {
			alert('uploaded!');
		});
	});
});
//});