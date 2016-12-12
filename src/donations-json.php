<?php

    @@HEADER@@

    require_once 'configuration.php';
    require_once 'donations.class.php';

    class JsonDonations {
        public $total;
        public $donations; 
    }

    class JsonDonationsTotal {
        public $nr_of_donations;
        public $total_amount;
        public $goal_amount;
        public $goal_percentage;
        public $since_date;
    }

    class JsonDonation {
        public $id;
        public $name;
        public $showAnonymous;
        public $message;
        public $amount;
        public $showNoAmount;
        public $timestamp;
    }

    header('Content-type: application/json;charset=utf-8');

    // Get ID of the last donation that the requester has already received
    $lastDonationId = isset($_GET['last']) && is_numeric($_GET['last']) ? $_GET['last'] : null;

    // Set defaults for page and page size
    $page = 1;
    $pageSize = 20;

	$configuration = new Configuration();

    // Get the donations
    $donations = new Donations($configuration->getPaymentsDatabaseDataSourceName(), $configuration->getPaymentsDatabaseUsername(), $configuration->getPaymentsDatabasePassword());
    $items = $donations->getDonationsList($lastDonationId, $page, $pageSize, 'DESC');

    // We only report the totals when no last ID is provided 
    if (!$lastDonationId || ($lastDonationId == 0)) {

        $totalCount = $donations->getDonationsListCount();
        $totalValue = $donations->getTotalDonationsAmount();

        $goalValue = $configuration->getDonationsGoalValue();

        $jsonDonationsTotal = new JsonDonationsTotal();
        $jsonDonationsTotal->nr_of_donations = $totalCount;
        $jsonDonationsTotal->total_amount = $totalValue;
        $jsonDonationsTotal->goal_amount = $goalValue;
        $jsonDonationsTotal->goal_percentage = $donations->getPercentageOfGoal($totalValue, $goalValue, 100.0);
        $jsonDonationsTotal->since_date = date('c', $configuration->getDonationsStartDate());
    } else
        $jsonDonationsTotal = null;

    // Cretae the object that we are going to return and set the totals
    $jsonDonations = new JsonDonations();
    $jsonDonations->total = $jsonDonationsTotal;

    // Loop through each item we retrieved from the database to add it to the result
    if ($items) {
        foreach($items as $item) {

            $jsonDonation = new JsonDonation();
            $jsonDonation->id = $item->id;

            // We don't show the name if the user choose to be anonymous
            $jsonDonation->showAnonymous = $item->showAnonymous;
            if (!$jsonDonation->showAnonymous) {
                $jsonDonation->name = $item->name;
            }

            // We don't show the amount of the user choose not to
            $jsonDonation->showNoAmount = $item->showNoAmount;
            if (!$jsonDonation->showNoAmount) {
                $jsonDonation->amount = $item->amount;
            }

            $jsonDonation->message = $item->message;
            $jsonDonation->timestamp = date('c', $item->timestamp);

            $jsonDonations->donations[] = $jsonDonation;
        }
    }

    // Result the result in JSON
    echo json_encode($jsonDonations);
