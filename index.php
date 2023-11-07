<?php
declare(strict_types = 1);

use Sumsub\AppTokenUsageExample\SumsubClient;

require_once __DIR__ . '/vendor/autoload.php';

// The description of the authorization method is available here: https://developers.sumsub.com/api-reference/#app-tokens
// Don't forget to change token and secret key values to production ones when switching to production
$sumsub_secret_key = $_POST["SUMSUB_SECRET_KEY"]; // Example: Hej2ch71kG2kTd1iIUDZFNsO5C1lh5Gq
$sumsub_app_token = $_POST["SUMSUB_APP_TOKEN"]; // Example: sbx:uY0CgwELmgUAEyl4hNWxLngb.0WSeQeiYny4WEqmAALEAiK2qTC96fBad

// The description of the flow can be found here: https://developers.sumsub.com/api-flow/#api-integration-phases

// Such actions are presented below:
// 1) Creating an applicant
// 2) Adding a document to the applicant
// 3) Getting applicant status
// 4) Getting access token

$externalUserId = $_POST["USER_ID"]; // Use your internal UserID instead in production code
$levelName = $_POST["LEVEL_NAME"];
$method = $_POST["METHOD"];
$timestamp = $_POST["TIMESTAMP"];

$testObject = new SumsubClient($sumsub_app_token, $sumsub_secret_key);

// $externalLink = $testObject->getExternalLink($externalUserId, $levelName);
// echo $externalLink["url"];

$now = time();
$url = $_POST["URL"];
$request = new GuzzleHttp\Psr7\Request($method, $url);
$signature = $testObject->createSignature($request, intval($timestamp));

echo $signature;
return;

$applicantId = $testObject->createApplicant($externalUserId, $levelName);
echo 'The applicant was successfully created: ' . $applicantId . PHP_EOL;

$imageId = $testObject->addDocument(
    $applicantId,
    __DIR__ . '/resources/sumsub-logo.png',
    ['idDocType' => 'PASSPORT', 'country' => 'GBR'],
);
echo 'Identifier of the added document: ' . $imageId . PHP_EOL;

$applicantStatusInfo = $testObject->getApplicantStatus($applicantId);
echo 'Applicant status (json): ' . json_encode($applicantStatusInfo, JSON_PRETTY_PRINT) . PHP_EOL;

$accessTokenInfo = $testObject->getAccessToken($externalUserId, $levelName);
echo 'Access token (json): ' . json_encode($accessTokenInfo, JSON_PRETTY_PRINT) . PHP_EOL;