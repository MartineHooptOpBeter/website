<?php require_once 'donations-class.php' ?><?php

	function formatPrice($amount) {
		return number_format((float)$amount / 100, 2, ',', '.');
	}
	
	function formatEuroPrice($amount) {
		return '€ ' . formatPrice($amount);
	}

    function show_donations_page($donationsUrl, $page = 1)
    {
		global $config;
		
		

?>	<section class="content donations">
		<div class="sitewidth clearfix">

            <div class="text">
		
<?php

		$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
		
		$itemCount = $donations->getDonationsListCount();
		$totalValue = $donations->getTotalDonationsAmount();
		
		$goalValue = $config['donate_goal'];
		if ($goalValue > 0) {
			$goalPercentage = ((float)$totalValue / (float)$goalValue) * 100;
			if ($goalPercentage > 100) { $goalPercentage = 100.0; }
		} else {
			$goalPercentage = 0;
		}

		$pageSize = 10;
		$pageMax = intval($itemCount / $pageSize) + 1;

		$page = intval($page);
		$page = $page > 0 ? $page : 1;
		$page = $page > $pageMax ? $pageMax : $page;

		$items = $donations->getDonationsList(($page - 1) * $pageSize, $pageSize, 'DESC');

		if (count($items) < 1) {

?>				<p><?php _e('There are no donations made yet.', 'martinehooptopbeter'); ?></p>

<?php
			
		} else {
			
?>				<div class="meter">
					<span style="width: <?php echo number_format($goalPercentage, 2, '.', '') ?>%"><span></span></span>
				</div>
				<div class="metertext clearfix">
					<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s of %2$s', 'martinehooptopbeter')), array('<strong>' . esc_attr(formatEuroPrice($totalValue)) . '</strong>', esc_attr(formatEuroPrice($goalValue)))); ?></span>
					<?php if ($itemCount == 1) : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donation', 'martinehooptopbeter'), $itemCount)); ?></span>
					<?php else : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donations', 'martinehooptopbeter'), $itemCount)); ?></span>
					<?php endif; ?>
				</div>

<?php

			foreach($items as $item) {

?>				<article>
					
					<?php if (!$item->showNoAmount) : ?><em>&euro; <?php echo number_format($item->amount / 100, 2, ',', '.'); ?></em><?php endif; ?>
					<?php if ($item->showAnonymous) : ?><strong><?php _e('Anonymous', 'martinehooptopbeter'); ?></strong><?php else : ?><strong><?php echo esc_attr($item->name); ?></strong><?php endif; ?>
					
					<?php
					
						$dateFormat = 'j F';
						if (date('Y', $item->timestamp) != date('Y')) { $dateFormat .= ' Y'; }
						$dateFormat .= ', G:i';
					
					?><span><?php echo date($dateFormat, $item->timestamp); ?></span>
					
					<?php if ($item->message) : ?><p><?php echo nl2br(esc_attr($item->message)); ?></p><?php endif ?>
					
				</article>
				
<?php

			}
			
?>				<div class="buttons">
					<?php if ($page > 1) : ?><a href="<?php echo esc_url( $donationsUrl . ($page > 2 ? "?donationpage=" . ($page - 1) : '') ); ?>" class="btn left"><?php _e('Previous', 'martinehooptopbeter'); ?></a><?php endif ?>
					<?php if ($page < $pageMax) : ?><a href="<?php echo esc_url( $donationsUrl . "?donationpage=" . ($page + 1) ); ?>" class="btn right"><?php _e('More', 'martinehooptopbeter'); ?></a><?php endif ?>
				</div>
				
<?php				
			
		}

?>            </div>

        </div>
    </section>

<?php

    }

?>