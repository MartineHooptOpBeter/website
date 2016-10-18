<?php get_header(); ?>

	<?php if (have_posts()) : the_post(); ?>
		<?php $hasExcerpt = (strlen($post->post_excerpt) > 0) ?>
		<?php $showIntro = $hasExcerpt || has_post_thumbnail(); ?>  

	<section class="content <?php if ($showIntro) { echo "intro dark "; } ?>right divider">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<?php the_title( '<h1>', '</h1>' ); ?>
				<div class="meta">
					<?php the_date('l, j F Y', '<time datetime="' . esc_attr(date('c', time($post->post_date))) . '">', '</time>'); ?> door <?php the_author(); ?> 
				</div>
				<?php if ($hasExcerpt) : ?>
					<?php the_excerpt(); ?>
				<?php endif; ?>
			</div>

		</div>
	</section>

	<section class="content right">
		<div class="sitewidth clearfix">

			<div class="text">
				<?php the_content(); ?>
				<a href="<?php
				
				$url = $_SERVER['HTTP_REFERER'];
				 
				if (get_option('page_for_posts')) {
					$fallback_url = get_permalink(get_option('page_for_posts'));
				} else {
					$$fallback_url = home_url('/');
				}

				echo esc_attr(wp_validate_redirect($url, $fallback_url));
				
				?>" class="btn"><?php _e('Back', 'martinehooptopbeter'); ?></a>
			</div>

		</div>
	</section>

	<?php endif ?>

<?php get_footer(); ?>