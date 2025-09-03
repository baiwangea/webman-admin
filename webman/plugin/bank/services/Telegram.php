<?php
declare (strict_types=1);
namespace plugin\bank\services;

class Telegram
{
    public $bot_token;
    public $chat_id;
    // 初始化函数
    public function __construct()
    {
        $this->bot_token = '5423189185:AAEoymgQU6_g1wLlIyL0b2_NjT4Fie5_4FE';
        $this->chat_id = 974141020;
    }

    public function setConfig($bot_token = '', $chat_id = '')
    {
        !empty($bot_token) && $this->bot_token = $bot_token;
        !empty($chat_id) && $this->chat_id = $chat_id;
    }

    public function sendMessage($message){
        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        $curl_data = [
            'chat_id' => $this->chat_id,
            'text' => $message
        ];
        $this->curl($url, $curl_data);
    }

    public function sendDocumnet($filepath, $filename = ''){
        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendDocument";
        $curl_data = [
            'chat_id' => $this->chat_id,
            'caption' => $filename, // 可选的标题或描述
            'document' => new \CURLFile($filepath, mime_content_type($filepath), basename($filepath))
        ];
        $this->curl($url, $curl_data);
    }

    private function curl($url, $curl_data)
    {
        // 创建cURL会话
        $ch = curl_init();

        // 设置cURL选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_data);

        // 执行cURL请求并获取响应
        $response = curl_exec($ch);

        // 检查是否有错误
        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            return false;
        } else {
            // 解析并输出响应
            $responseArray = json_decode($response, true);
            // print_r($responseArray);
            return $responseArray;
        }

        // 关闭cURL会话
        curl_close($ch);
    }
}