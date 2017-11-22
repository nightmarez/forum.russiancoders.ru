		<footer class="footer" style="text-align: center; line-height: 0.5;">
			<div class="footer-inner">
                <?php
                    $end_time = microtime();
                    $total_time = $end_time - $start_time;
                    printf("<!-- Страница сгенерирована за %f секунд -->", $total_time);
                ?>
				<p>Developed by Michael Makarov (aka NightmareZ)</p>
				<p>Welcome to my personal site <a href="https://nightmarez.net/">NightmareZ.net</a></p>
				<p>Hosted at <a href="https://www.jino.ru/?pl=14468430">jino.ru</a></p>
			</div>
		</footer>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter46607505 = new Ya.Metrika({
                    id:46607505,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/46607505" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
	</body>
</html>