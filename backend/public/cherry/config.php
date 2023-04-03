<?php



define("VALID_API_KEY", "jS8vMEEaIBzQadVaEdjna7jGOnTmSR8gOMR7e6THsgJ0z41gho9NHsFmEY9beywt");
define("ACCOUNTS_LISTS", ['android','ios']);
define("VALID_ACTIONS", [
    'getRtcToken' => [
        'type'      => 'POST',
        'params'    => ['user_1', 'user_2']
    ]
]);
define("CREDENTIALS", [
    'app_id'            => '6718eaa963e04495af96e4f533d66536',
    'app_certificate'   => '46bc88299d174a39a4bf32d37ac40d6c',
    'issueTs'           => '1111111',
    'salt'              => 1,
]);
 

function checkApiType($requestType, $action)
{
    $status     = true;
    $message    = "Data Valid";
    if(VALID_ACTIONS[$action]['type'] != $requestType)
    {
        $message    = "Sorry This Method is not Supported ";
        $status     = false;
    }
    return ['status' => $status, 'message' => $message];
}
function checkActions($params)
{
    $status     = true;
    $message    = "Data Valid";
    foreach(VALID_ACTIONS as $action => $type)
    {
        if(!array_key_exists($action, $params))
        {
            $message    = "Sorry This Method Not Exists";
            $status     = false;

        }
    }
    

    return ['status' => $status, 'message' => $message];
}
function checkApiParameters($action, $params)
{
    $status     = true;
    $message    = "Data Valid";

    foreach (VALID_ACTIONS[$action]['params'] as  $requiredParam) 
    {
        if(!array_key_exists($requiredParam,$params))
        {
            $status     = false;
            $message    = "Sorry ".$requiredParam." is Required";
            break;
        }
    }

    $message = VALID_ACTIONS[$action]['params'];

    return ['status' => $status, 'message' => $message];

}
function checkApiCredentials($params)
{
    $status     = true;
    $message    = "Data Valid";
    // Checkpoint 1
    if(!array_key_exists("api_key", $params))
    {
        $message = "Sorry Api Key Not Found ";
        $status = false;
    }else if(!array_key_exists("acc_id", $params))
    {
        $message = "Sorry account not Found ";
        $status = false;
    }else if($params['api_key'] != VALID_API_KEY)
    {
        $message = "API KEY NOT FOUND ! please try again with Valid Api Key ";
        $status = false;
    }else if(!in_array($params['acc_id'], ACCOUNTS_LISTS))
    {
        $message = "Account Id  NOT FOUND ! please try again with Valid Account id ";
        $status = false;
    }

    return ['status' => $status, 'message' => $message ];
}

function makeApiResponse($getData, $getStatus)
{
    $status = $message = $data = "";
    if(!$getStatus)
    {
        $message    = $getData;
        $data       = null;
    }else 
    {
        $status     = 1;
        $message    = null;
        $data       = $getData;
    }

    $status = ($status)?1:0;
    echo json_encode(['status' => $status , 'message' => $message, 'data' => $data]);die();
}

function logToFile($filename, $msg)
{
    // open file
    $fd = fopen($filename, "a");
    // append date/time to message
    $str = "[" . date("Y/m/d H:i:s", time()) . "] " . $msg. "\n";
    // write string
    fwrite($fd, $str . "\r\n");
    // close file
    fclose($fd);
}

function applyForToken($channelName, $uid, $expireTimeInSeconds = 600)
{

    include("src/RtcTokenBuilder2.php");
    $status     = true;

    $token      = RtcTokenBuilder2::buildTokenWithUid(CREDENTIALS['app_id'], CREDENTIALS['app_certificate'], $channelName, $uid, RtcTokenBuilder2::ROLE_PUBLISHER, $expireTimeInSeconds);
    $message    = $token;

    if(empty($token))
    {
        $status     = false;
        $message    = "Sorry Token is Empty! Problem With Your Creds";
    }


    return ['status' => $status, 'message' => $message ];

}

function applyForAllAccess($channelName, $uid, $expireTimeInSeconds = 1600)
{

    include("src/AccessToken2.php");
    $accessToken = new AccessToken2(CREDENTIALS['app_id'], CREDENTIALS['app_certificate'], $expireTimeInSeconds);
    $accessToken->issueTs = CREDENTIALS['issueTs'];
    $accessToken->salt = CREDENTIALS['salt'];

    // grant rtc privileges
    $serviceRtc = new ServiceRtc($channelName, $uid);
    $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $expireTimeInSeconds);
    $accessToken->addService($serviceRtc);

    // grant rtm privileges
    $serviceRtm = new ServiceRtm((string)$uid);
    $serviceRtm->addPrivilege($serviceRtm::PRIVILEGE_JOIN_LOGIN, $expireTimeInSeconds);
    $accessToken->addService($serviceRtm);

    // grant chat privileges
    $serviceChat = new ServiceChat((string)$uid);
    $serviceChat->addPrivilege($serviceChat::PRIVILEGE_USER, $expireTimeInSeconds);
    $accessToken->addService($serviceChat);

    

    $serviceChat = new ServiceChat((string)$uid);
    $serviceChat->addPrivilege(ServiceRtc::PRIVILEGE_PUBLISH_AUDIO_STREAM, $expireTimeInSeconds);
    $accessToken->addService($serviceChat);

    $serviceChat = new ServiceChat((string)$uid);
    $serviceChat->addPrivilege(ServiceRtc::PRIVILEGE_PUBLISH_VIDEO_STREAM, $expireTimeInSeconds);
    $accessToken->addService($serviceChat);

    $serviceChat = new ServiceChat((string)$uid);
    $serviceChat->addPrivilege(ServiceRtc::PRIVILEGE_PUBLISH_DATA_STREAM, $expireTimeInSeconds);
    $accessToken->addService($serviceChat);

    

    $token      = $accessToken->build();
    $status     = true;
    $message    = $token;

    if(empty($token))
    {
        $status     = false;
        $message    = "Sorry Token is Empty! Problem With Your Creds";
    }

    return ['status' => $status, 'message' => $message ];
}


function applyRTcAccess($channelName, $uid, $expireTimeInSeconds=1600)
{
    include("src/AccessToken2.php");
    $accessToken = new AccessToken2(CREDENTIALS['app_id'], CREDENTIALS['app_certificate'], $expireTimeInSeconds);
    $accessToken->issueTs = CREDENTIALS['issueTs'];
    $accessToken->salt = CREDENTIALS['salt'];

    $serviceRtc = new ServiceRtc($channelName, $uid);
    $serviceRtc->addPrivilege($serviceRtc::PRIVILEGE_JOIN_CHANNEL, $expireTimeInSeconds);

    $accessToken->addService($serviceRtc);
    $token = $accessToken->build();

    $token      = $accessToken->build();
    $status     = true;
    $message    = $token;

    if(empty($token))
    {
        $status     = false;
        $message    = "Sorry Token is Empty! Problem With Your Creds";
    }

    return ['status' => $status, 'message' => $message ];
}

function applyRTCToken($channelName, $uid, $expireTimeInSeconds = 1600)
{
    include("src/RtcTokenBuilder.php");

    $role = RtcTokenBuilder::RoleAttendee;
    $currentTimestamp = (new DateTime("now", new DateTimeZone('UTC')))->getTimestamp();
    $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
    $token = RtcTokenBuilder::buildTokenWithUid(CREDENTIALS['app_id'], CREDENTIALS['app_certificate'], $channelName, $uid, $role, $privilegeExpiredTs);
    $status     = true;
    $message    = $token;
    if(empty($token))
    {
        $status     = false;
        $message    = "Sorry Token is Empty! Problem With Your Creds";
    }

    return ['status' => $status, 'message' => $message ];
}