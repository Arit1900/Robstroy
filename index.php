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

namespace LINE\LINEBot\KitchenSink\EventHandler\MessageHandler;

use Predis\Client;

class TextMessageHandler implements EventHandler
{
    /** @var LINEBot $bot */
    private $bot;
    /** @var \Monolog\Logger $logger */
    private $logger;
    /** @var \Slim\Http\Request $logger */
    private $req;
    /** @var TextMessage $textMessage */
    private $textMessage;

    private $redis;

    /**
     * TextMessageHandler constructor.
     * @param $bot
     * @param $logger
     * @param \Slim\Http\Request $req
     * @param TextMessage $textMessage
     */
    public function __construct($bot, $logger, \Slim\Http\Request $req, TextMessage $textMessage)
    {
        $this->bot = $bot;
        $this->logger = $logger;
        $this->req = $req;
        $this->textMessage = $textMessage;
        $this->redis = new Client(getenv('REDIS_URL'));
    }

    public function handle()
    {
        $TEACH_SIGN = '==';
        $text = $this->textMessage->getText();
        $text = trim($text);
        # Remove ZWSP
        $text = str_replace("\xE2\x80\x8B", "", $text);
        $replyToken = $this->textMessage->getReplyToken();

        if ($text == 'บอท') {
            $this->bot->replyText($replyToken, $out =
                "ใช้ $TEACH_SIGN เพื่อสอนเราได้นะ\nเช่น สวัสดี" . $TEACH_SIGN . "สวัสดีชาวโลก");
            return true;
        }

        $sep_pos = strpos($text, $TEACH_SIGN);
        if ($sep_pos > 0) {
            $text_arr = explode($TEACH_SIGN, $text, 2);
            if (count($text_arr) == 2) {
                $this->saveResponse($text_arr[0], $text_arr[1]);
            }
            return true;
        }

        $re = $this->getResponse($text);
        $re_count = count($re);
        if ($re_count > 0) {
            // Random response.
            $randNum = rand(0, $re_count - 1);
            $response = $re[$randNum];
            $this->bot->replyText($replyToken, $response);
            return true;
        }
        return false;
    }

    private function saveResponse($keyword, $response)
    {
        $this->redis->lpush("response:$keyword", $response);
    }

    private function getResponse($keyword)
    {
        return $this->redis->lrange("response:$keyword", 0, -1);
    }
}

}
}
}
}
echo "OK";