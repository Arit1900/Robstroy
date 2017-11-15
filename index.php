<?php
require_once('./vendor/autoload.php');
// Namespace
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
$channel_token =
'1caj07yURrrjtw3H9hzJ+bWYo+rsam3iGzbrYrrRhjH7zZqHuz3Dujm0jtJsVTThFpev/g0oK8CZYAON18xdMenFO+a02ALh4r2OlXPFyPsBscfr7b4IZwNoBOVLfS6RUA70p2pIWk3dpq+U8aHwtQdB04t89/1O/w1cDnyilFU=';
$channel_secret = '5e8d1f791608a58b43c5bd31c986d955';
// Get message from Line API
$content = file_get_contents('php://input');
$events = json_decode($content, true);
if (!is_null($events['events'])) {
// Loop through each event
foreach ($events['events'] as $event) {
// Line API send a lot of event type, we interested in message only.
if ($event['type'] == 'message') {
switch($event['message']['type']) {
case 'text':
// Get replyToken
$replyToken = $event['replyToken'];
switch($event['message']['type']) {
case 'image':
$messageID = $event['message']['id'];
$respMessage = 'Hello, your image ID is '. $messageID;
break;
default:
}
$httpClient = new CurlHTTPClient($channel_token);
$bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));
$textMessageBuilder = new TextMessageBuilder($respMessage);
$response = $bot->replyMessage($replyToken, $textMessageBuilder);
// Reply message
$respMessage = 'Hello, your message is '. $event['message']['text'];
$httpClient = new CurlHTTPClient($channel_token);
$bot = new LINEBot($httpClient, array('channelSecret' => $channel_secret));
$textMessageBuilder = new TextMessageBuilder($respMessage);
$response = $bot->replyMessage($replyToken, $textMessageBuilder);
break;
}
}
}
}

public function __construct ($channelSecret, $access_token) {
		
        $this->httpClient     = new CurlHTTPClient($access_token);
        $this->channelSecret  = $channelSecret;
        $this->endpointBase   = LINEBot::DEFAULT_ENDPOINT_BASE;
		
        $this->content        = file_get_contents('php://input');
        $events               = json_decode($this->content, true);
		
        if (!empty($events['events'])) {
			
            $this->isEvents = true;
            $this->events   = $events['events'];
			
            foreach ($events['events'] as $event) {
				
                $this->replyToken = $event['replyToken'];
                $this->source     = (object) $event['source'];
                $this->message    = (object) $event['message'];
                $this->timestamp  = $event['timestamp'];
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
                    $this->isText = true;
                    $this->text   = $event['message']['text'];
                }
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'image') {
                    $this->isImage = true;
                }
				
                if ($event['type'] == 'message' && $event['message']['type'] == 'sticker') {
                    $this->isSticker = true;
                }
				
            }
        }
		
        parent::__construct($this->httpClient, [ 'channelSecret' => $channelSecret ]);
		
    }
	
    public function sendMessageNew ($to = null, $message = null) {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/push', [
            'to' => $to,
            // 'toChannel' => 'Channel ID,
            'messages'  => $messageBuilder->buildMessage()
        ]);
    }
	
    public function replyMessageNew ($replyToken = null, $message = null) {
        $messageBuilder = new TextMessageBuilder($message);
        $this->response = $this->httpClient->post($this->endpointBase . '/v2/bot/message/reply', [
            'replyToken' => $replyToken,
            'messages'   => $messageBuilder->buildMessage(),
        ]);
    }
	
    public function isSuccess () {
        return !empty($this->response->isSucceeded()) ? true : false;
    }
	
    public static function verify ($access_token) {
		
        $ch = curl_init('https://api.line.me/v1/oauth/verify');
		
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $access_token ]);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
		
    }

echo "OK";