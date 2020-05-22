<?php


namespace Services;


use Models\UserModel;

class SupermetricsApiService
{
    private string $apiBaseUrl;
//    private string $clientId; // if client id only for this application not for all users'
    private UserModel $user;

    public function __construct(UserModel $user)
    {
        $this->model = $user;
        $this->apiBaseUrl = getenv('SUPERMETRICS_API_URL');
//        $this->clientId = getenv('SUPERMETRICS_API_CLIENT_ID'); // if client id only for this application not for all users'
    }

    public function fetchPosts($pagesCount = 10)
    {
        $result = [];

        for ($i = 1; $i <= $pagesCount; $i++) {
            $res = $this->fetch('posts', ['page' => $i]);
            $result = array_merge($result, $res["data"]["posts"]);
        }

        return $result;
    }

    private function fetch(
        $method,
        $params,
        $verb = "GET",
        $retry = true
    )
    {
        if ($this->model->getSlTokenExpiredAt() < new \DateTime())
            $this->refreshToken();

        $params["sl_token"] = $this->model->getSlToken();
        $res = $this->request($method, $params, $verb);

        $response = $res["response"];
        $error = $res["error"];
        $errno = $res["errno"];

        if ($errno === 500) {
            $response = json_decode($response, true);
            if ($response["error"]["message"] === "Invalid SL Token") {
                $this->refreshToken();
                if ($retry)
                    $this->fetch($method, $params, $verb, false);
            } else
                throw new \RuntimeException($error, $errno);
        } else if ($errno !== 0) {
            throw new \RuntimeException($error, $errno);
        }

        return json_decode($response, true);
    }

    private function refreshToken()
    {
        $params = [
            'client_id' => $this->model->getClientId(),
            'email' => $this->model->getEmail(),
            'name' => $this->model->getName(),
        ];
        $res = $this->request('register', $params, 'POST');
        if ($res["errno"] !== 0) {
            throw new \RuntimeException($res["error"], $res["errno"]);
        }
        $response = json_decode($res["response"], true);

        $expiredAt = new \DateTime(); //now
        $expiredAt->add(new \DateInterval('PT1H'));//add 1 hour

        $this->model->setSlToken($response["data"]["sl_token"]);
        $this->model->setSlTokenExpiredAt($expiredAt);
        $this->model->save();
    }

    private function request($method, $params, $verb)
    {
        $ch = curl_init($this->apiBaseUrl . '/' . $method);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);

        if (is_resource($ch)) {
            curl_close($ch);
        }

        return ["response" => $response, "error" => $error, "errno" => $errno];
    }
}