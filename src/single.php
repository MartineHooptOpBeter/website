<?php

	require_once 'blog-comments-walker.class.php';

?><?php get_header(); ?>

	<?php if (have_posts()) : the_post(); ?>
		<?php $hasExcerpt = (strlen($post->post_excerpt) > 0) ?>
		<?php $showIntro = $hasExcerpt || has_post_thumbnail(); ?>  

	<section class="content intro <?php if ($showIntro) { echo "dark "; } ?>right divider">
		<div class="sitewidth clearfix">

			<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'martinehooptopbeter_square-400' ); } ?>

			<div class="text">
				<h1><?php echo esc_html(get_the_title()); ?></h1>
				<div class="meta">
					<?php the_date('l, j F Y', '<time datetime="' . esc_attr(date('c', time($post->post_date))) . '">', '</time>'); ?> door <?php the_author(); ?> 
				</div>
				<?php if ($hasExcerpt) : ?>
					<?php echo apply_filters('the_excerpt', esc_html(get_the_excerpt())); ?>
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

		<?php if (comments_open() || get_comments_number()): ?>
		
	<section class="comments">
		<div class="sitewidth clearfix">

		<?php $comments = get_comments(array('post_id' => $post->ID)); ?>
		<?php if ($comments): ?>

		<div class="comments-title">
			<?php $comments_number = get_comments_number(); ?>
			<?php if ($comments_number == 1) : ?>
				<?php echo esc_attr(sprintf( __('There is %1$s comment', 'martinehooptopbeter'), number_format_i18n($comments_number))); ?>
			<?php else: ?>
				<?php echo esc_attr(sprintf( __('There are %1$s comments', 'martinehooptopbeter'), number_format_i18n($comments_number))); ?>
			<?php endif; ?>
		</div>

		<?php the_comments_navigation(); ?>

		<div class="comments-block">
		<?php
			wp_list_comments( array(
				'walker'      => new BlogCommentsWalker(),
			), $comments );
		?>
		</div>

		<?php the_comments_navigation(); ?>

		<?php endif; ?>

		<?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')): ?>
			<p class="no-comments"><?php _e('Comments are closed.', 'martinehooptopbeter'); ?></p>
		<?php endif; ?>

		<?php
		
			comment_form(array(
				'fields' =>  array(
								'author' =>
									'<p><label for="comment-author">' . __('Your name', 'martinehooptopbeter') . '</label> ' .
									( $req ? '<span class="required">*</span>' : '' ) .
									'<input id="comment-author" name="author" type="text" class="textinput" value="' . esc_attr( $commenter['comment_author'] ) .
									'" size="30"' . $aria_req . ' /></p>',

								'email' =>
									'<p><label for="comment-email">' . __('Your E-mail address', 'martinehooptopbeter') . '</label> ' .
									( $req ? '<span class="required">*</span>' : '' ) .
									'<input id="comment-email" name="email" type="text" class="textinput" value="' . esc_attr(  $commenter['comment_author_email'] ) .
									'" size="30"' . $aria_req . ' /></p>' . 
									'<p class="comment-notes">' . __('Your e-mail address will never be shown on the website.', 'martinehooptopbeter') . '</p>',
							),

				'comment_field' => '<p class="comment-form-comment"><label for="comment-comment">' . _x( 'Comment', 'noun' ) . '</label><textarea id="comment-comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
			
				'comment_notes_before' => '',
				'comment_notes_after' => '',
			
				'title_reply' => ($comments_number > 0 ?  __('Also want to comment?', 'martinehooptopbeter') : __('Want to comment?', 'martinehooptopbeter')),
				'title_reply_to' => __('You reply to %1$s', 'martinehooptopbeter'),
			
				'title_reply_before' => '<div class="comment-reply-title">',
				'title_reply_after'  => '</div>',
				
				'cancel_reply_before' => '<span class="btn">',
				'cancel_reply_after' => '</span>',
				
				'class_submit' => 'btn',
			));
		?>

		</div>
	</section>

		<?php endif ?>

	<?php endif ?>

<?php get_footer(); ?>