<?php


$access_token =$_GET['accesstoken'];
#$access_token = 'xxxx.yyyy.zzzz';

$meeting_data = [
    "topic" =>  'hello world',
    "type" => 2,
    "start_time" => "2023-10-01T10:00:00Z",
    "duration" =>  120,
    "password" => "12345678",
    "agenda" => "40 mins limit demonstration",
    "pre_schedule" => false,
    "timezone"=> "Asia/Singapore",
    "default_password" => false
];

$api_url = 'https://api.zoom.us/v2/users/me/meetings';
$ch_meeting = curl_init($api_url );


curl_setopt($ch_meeting, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer $access_token",
  'Content-Type: application/json',
  'Accept: application/json'
));

curl_setopt($ch_meeting, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch_meeting, CURLOPT_POSTFIELDS, json_encode($meeting_data));
curl_setopt($ch_meeting, CURLOPT_RETURNTRANSFER, true);

    $meeting_response = curl_exec($ch_meeting);
    echo "Meeting Details : ";
    echo $meeting_response;

$this->revokeAccessToken($CLIENT_ID, $CLIENT_SECRET, $access_token);

?>