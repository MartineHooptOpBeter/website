	<footer>
		<p>© <?php echo Date("Y"); ?> - <?php echo vsprintf(esc_attr(__('Website built for free by %1$s', 'martinehooptopbeter')), '<a href="http://www.virtualpages.nl" target="_blank"><img src="' . get_bloginfo('template_url') . '/img/virtualpages.svg" class="logo" alt="Virtual Pages" /></a>'); ?></p>
		<p class="small"><a href="<%= release.homepage %>" target="_blank"><?php echo vsprintf(esc_attr(__('Source code available on %1$s', 'martinehooptopbeter')), '<i class="icon icon-github"></i>GitHub'); ?></a></p>
	</footer>

<?php echo wp_footer(); ?>

</body>

<!--
	@@HEADER@@
-->

</html>