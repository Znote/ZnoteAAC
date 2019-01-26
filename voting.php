<?php
require_once 'engine/init.php';
include 'layout/overall/header.php';

$otservers_eu_voting = $config['otservers_eu_voting'];

if ($otservers_eu_voting['enabled']) {
	if (user_logged_in()) {
		$isRewardRequest = isset($_GET['action']) && $_GET['action'] === 'reward';
		if (!$isRewardRequest) {
			$result = vote($user_data['id'], $otservers_eu_voting);
			if ($result === false) {
				echo '<p>Something went wrong! Could not make a vote request.</p>';
			} else {
				header('Location: ' . $result['voteLink']);
				die;
			}
		} else {
			$result = checkHasVoted($user_data['id'], $otservers_eu_voting);
			if ($result !== false) {
				if ($result['voted'] === true) {
					$points = $otservers_eu_voting['points'];
					$pointsText = $points === '1' ? 'point' : 'points';
					mysql_update("UPDATE `znote_accounts` SET `points` = `points` + '$points' WHERE `account_id`=" . $user_data['id']);
					echo "<p>Thank you for voting! You have been rewarded with $points $pointsText!</p>";
				} else {
					echo '<p>It does not seem like you have voted.</p>';
				}
			} else {
				echo '<p>Could not verify that you have voted.</p>';
			}
		}
	} else {
		header('Location: ' . $otservers_eu_voting['simpleVoteUrl']);
		die;
	}
} else {
	echo '<p>Voting is not enabled.</p>';
}

include 'layout/overall/footer.php';

function vote($otUserId, $otservers_eu_voting) {
	$context  = stream_context_create([
		'http' => [
			'header'  => "Content-type: application/json",
			'method'  => 'POST',
			'content' => json_encode([
				'otUserId' => $otUserId,
				'secretToken' => $otservers_eu_voting['secretToken'],
				'landingPage' => $otservers_eu_voting['landingPage']
			])
		]
	]);
	$result = file_get_contents($otservers_eu_voting['voteUrl'], false, $context);
	return $result !== false ? json_decode($result, true) : false;
}

function checkHasVoted($otUserId, $otservers_eu_voting) {
	$context  = stream_context_create([
		'http' => [
			'header'  => "Content-type: application/json",
			'method'  => 'POST',
			'content' => json_encode([
				'otUserId' => $otUserId,
				'secretToken' => $otservers_eu_voting['secretToken'],
				'consume' => true
			])
		]
	]);
	$result = file_get_contents($otservers_eu_voting['voteCheckUrl'], false, $context);
	return $result !== false ? json_decode($result, true) : false;
}
