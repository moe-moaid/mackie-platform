<?php 

include("config.php");


$response  = checkActions($_GET);
if(!$response['status'])
{
	echo makeApiResponse($response['message'], $response['status']);
}

$_method = array_keys($_GET)[0];

if($_method == "getRtcToken")
{
	$response  = checkApiType($_SERVER['REQUEST_METHOD'], $_method);
	if(!$response['status'])
	{
		echo makeApiResponse($response['message'], $response['status']);
	}

	//Check Api Creds 
	$response = checkApiCredentials($_POST);
	if(!$response['status'])
	{
		echo makeApiResponse($response['message'], $response['status']);
		
	}

	//Check Paramters  
	$response = checkApiParameters($_method,$_POST);
	if(!$response['status'])
	{
		echo makeApiResponse($response['message'], $response['status']);
	}

	$channelName = $_POST['channel_name']; 
	$uid = rand(1000,5000);
	$uid = 0;
	$response = applyRTCToken($channelName, $uid);
	if(!$response['status'])
	{
		echo makeApiResponse($response['message'], $response['status']);
	}

	$logFileData = [
		'channelName'	=> $channelName,
		'uid'			=> $uid,
		'token'			=> $response['message'],
		'params'		=> $_POST,
	];


	logToFile('token_list.log', json_encode($logFileData));
	// $data 	= ['token' => $response['message'] , 'channel_name' => $channelName, 'uid' => $uid];
	$data 	= [
		'token' => $response['message'] , 
		'channel_name' => $channelName, 
		'app_id' => CREDENTIALS['app_id']
	];
	echo makeApiResponse($data, $response['status']);

}
