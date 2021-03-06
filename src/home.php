<?php get_header(); ?>

	<?php

		$page_for_posts_id = get_option('page_for_posts');
		$page_for_posts = get_post($page_for_posts_id);

	?>

	<?php if ($page_for_posts) : ?>

	<section class="content intro right dark divider">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail( $page_for_posts->ID ) ) { get_the_post_thumbnail( $page_for_posts->ID, 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<h1><?php echo esc_html(apply_filters('the_title', $page_for_posts->post_title)); ?></h1>
				<?php echo apply_filters('the_content', $page_for_posts->post_content); ?>
			</div>

		</div>
	</section>

	<?php endif; ?>

	<?php while ( have_posts() ) : the_post(); ?>

	<article class="blogpost">
		<section class="content right">
			<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { echo '<a href="', the_permalink(), '">', the_post_thumbnail('martinehooptopbeter_square-200'), '</a>'; } ?>

				<div class="text">
					<h2><a href="<?php echo esc_html(get_the_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a></h2>
					<div class="meta">
						<?php echo esc_html( sprintf( __( '%1$s by %2$s', 'martinehooptopbeter' ), get_the_time('l, j F Y'), get_the_author() ) ); ?> 
					</div>
					<p><?php echo apply_filters('the_excerpt', esc_html(get_the_excerpt())); ?></p>
					<a href="<?php the_permalink() ?>" class="btn"><?php _e('Read more', 'martinehooptopbeter'); ?></a>
				</div>

			</div>
		</section>
	</article>

	<?php endwhile; ?>

	<section class="pagenavigation">
		<div class="sitewidth clearfix">
			<?php echo paginate_links(array(
			'mid_size'           => 2,
            'prev_text'          => _x( 'Previous', 'previous post' ),
            'next_text'          => _x( 'Next', 'next post' ),
            'screen_reader_text' => __( 'Posts navigation' ),
			)); ?>
		</div>
	</section>

<?php get_footer(); ?>