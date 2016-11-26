<?php

	@@HEADER@@

	class BlogCommentsWalker extends Walker_Comment {
 
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$GLOBALS['comment_depth'] = $depth + 1;
	 
		}
 
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$GLOBALS['comment_depth'] = $depth + 1;
		}
 
		public function end_el( &$output, $comment, $depth = 0, $args = array() ) {
			if ( !empty( $args['end-callback'] ) ) {
				ob_start();
				call_user_func( $args['end-callback'], $comment, $args, $depth );
				$output .= ob_get_clean();
				return;
			}
		}
 
		protected function comment( $comment, $depth, $args ) {
			
			if (!$comment->comment_approved)
				return;

?>			<div <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?> id="comment-<?php comment_ID(); ?>">
				<?php
					comment_reply_link( array_merge( $args, array(
						'add_below' => 'comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<em>',
						'after'     => '</em>'
					) ) );
				?>
				<strong><?php echo get_comment_author_link($comment); ?></strong>
				<span><?php echo esc_attr(sprintf(__('%1$s at %2$s', 'martinehooptopbeter'), get_comment_date('', $comment), get_comment_time())); ?></span>
				<div class="comment-text">
					<?php comment_text( $comment, array_merge( $args, array( 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div>
			</div>
<?php

		}
 
	}
