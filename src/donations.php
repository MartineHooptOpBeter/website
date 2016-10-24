<?php

    @@HEADER@@

	require_once 'configuration.php';
	require_once 'donations-class.php';

    function show_donations_page($donationsUrl, $page = 1)
    {

?>	<section id="donations" class="content donations">
		<div class="sitewidth clearfix">

            <div class="text">
		
<?php

		$configuration = new Configuration();
		$donations = new Donations($configuration->getDonationsDatabaseDataSourceName(), $configuration->getDonationsDatabaseUsername(), $configuration->getDonationsDatabasePassword());
		
		$itemCount = $donations->getDonationsListCount();
		$totalCount = $itemCount;
		$totalValue = $donations->getTotalDonationsAmount();
		
		if ($donations_options = get_option('donations_options')) {
			$totalCount += intval($donations_options['offline_number']);
			$totalValue += intval($donations_options['offline_amount']);
		}
		
		$goalValue = $configuration->getDonationsGoalValue();
		$goalPercentage = $donations->percentageOfGoal($totalValue, $goalValue, 100.0);

		$pageSize = 10;
		$pageMax = intval(($itemCount - 1) / $pageSize) + 1;

		$page = intval($page);
		$page = $page > 0 ? $page : 1;
		$page = $page > $pageMax ? $pageMax : $page;

		$items = $donations->getDonationsList(($page - 1) * $pageSize, $pageSize, 'DESC');

?>			<?php if (count($items) < 1) : ?>
				<p><?php _e('There are no donations made yet.', 'martinehooptopbeter'); ?></p>
			<?php else : ?>
				<?php if (isset($goalValue) && is_numeric($goalValue) && ($goalValue > 0)) : ?>
					<div class="meter">
						<span style="width: <?php echo Donation::formatDecimal($goalPercentage * 100.0); ?>%"><span></span></span>
					</div>
				<?php endif; ?>
				<div class="metertext clearfix">
					<?php if (isset($goalValue) && is_numeric($goalValue) && ($goalValue > 0)) : ?>
						<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s of %2$s', 'martinehooptopbeter')), array('<strong>' . esc_attr(Donation::formatEuroPrice($totalValue)) . '</strong>', esc_attr(Donation::formatEuroPrice($goalValue)))); ?></span>
					<?php else : ?>
						<span class="value"><?php echo vsprintf(esc_attr(__('Total: %1$s', 'martinehooptopbeter')), array('<strong>' . esc_attr(Donation::formatEuroPrice($totalValue)) . '</strong>')); ?></span>
					<?php endif; ?>
					<?php if ($totalCount == 1) : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donation', 'martinehooptopbeter'), $totalCount)); ?></span>
					<?php else : ?>
						<span class="number"><?php echo esc_attr(vsprintf(__('%1$s donations', 'martinehooptopbeter'), $totalCount)); ?></span>
					<?php endif; ?>
				</div>

					<?php foreach($items as $item) : ?>

				<article>
					
					<?php if (!$item->showNoAmount) : ?><em><?php echo Donation::formatEuroPrice($item->amount); ?></em><?php endif; ?>
					<?php if ($item->showAnonymous) : ?><strong><?php _e('Anonymous', 'martinehooptopbeter'); ?></strong><?php else : ?><strong><?php echo esc_attr($item->name); ?></strong><?php endif; ?>
					
					<span><?php echo Donation::formatShortDateTime($item->timestamp, __('%1$s at %2$s', 'martinehooptopbeter')); ?></span>

					<?php if ($item->message) : ?><p><?php echo nl2br(esc_attr($item->message)); ?></p><?php endif ?>
					
				</article>
				
					<?php endforeach; ?>

				<div class="buttons">
					<?php if ($page > 1) : ?><a href="<?php echo esc_url( $donationsUrl . ($page > 2 ? "?donationpage=" . ($page - 1) : '') ); ?>#donations" class="btn left"><?php _e('Previous', 'martinehooptopbeter'); ?></a><?php endif ?>
					<?php if ($page < $pageMax) : ?><a href="<?php echo esc_url( $donationsUrl . "?donationpage=" . ($page + 1) ); ?>#donations" class="btn right"><?php _e('More', 'martinehooptopbeter'); ?></a><?php endif ?>
				</div>
				
			<?php endif; ?>

            </div>

        </div>
    </section>

<?php

    }

?>