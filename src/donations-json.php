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

    $afterDonationId = isset($_GET['last']) && is_numeric($_GET['last']) ? $_GET['last'] : null;
    $page = 1;
    $pageSize = 20;

	$configuration = new Configuration();
    $donations = new Donations($configuration->getPaymentsDatabaseDataSourceName(), $configuration->getPaymentsDatabaseUsername(), $configuration->getPaymentsDatabasePassword());

    $totalCount = $donations->getDonationsListCount();
    $totalValue = $donations->getTotalDonationsAmount();

    $goalValue = $configuration->getDonationsGoalValue();

    $items = $donations->getDonationsList($afterDonationId, $page, $pageSize, 'DESC');

    $jsonDonationsTotal = new JsonDonationsTotal();
    $jsonDonationsTotal->nr_of_donations = $totalCount;
    $jsonDonationsTotal->total_amount = $totalValue;
    $jsonDonationsTotal->goal_amount = $goalValue;
    $jsonDonationsTotal->goal_percentage = $donations->getPercentageOfGoal($totalValue, $goalValue, 100.0);
    $jsonDonationsTotal->since_date = date('c', $configuration->getDonationsStartDate()); 

    $jsonDonations = new JsonDonations();
    $jsonDonations->total = $jsonDonationsTotal;

    foreach($items as $item) {

        $jsonDonation = new JsonDonation();
        $jsonDonation->id = $item->id;
        $jsonDonation->showAnonymous = $item->showAnonymous;
        if (!$jsonDonation->showAnonymous) {
            $jsonDonation->name = $item->name;
        }
        $jsonDonation->message = $item->message;
        $jsonDonation->showNoAmount = $item->showNoAmount;
        if (!$jsonDonation->showNoAmount) {
            $jsonDonation->amount = $item->amount;
        }
        $jsonDonation->timestamp = date('c', $item->timestamp);

        $jsonDonations->donations[] = $jsonDonation;

    }

    echo json_encode($jsonDonations);

?>