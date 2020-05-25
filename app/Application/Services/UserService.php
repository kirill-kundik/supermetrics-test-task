<?php


class UserService
{
    private UserModel $userModel;
    private array $userData;

    public function __construct($userModel, $userData)
    {
        $this->userModel = $userModel;

        $candidate = $this->userModel->findBy(["email" => $userData["email"]]);
        if (count($candidate) == 0) {
            $userData["sl_token"] = null;
            $userData["token_expired_at"] = null;

            $this->userData = $this->userModel->save($userData);
        } else {
            $this->userData = $candidate[0];
            $this->userData["token_expired_at"] = str_replace(
                    " ", "T", $this->userData["token_expired_at"]
                ) . "+00:00";
        }
    }

    public function getClientId()
    {
        return $this->userData["client_id"];
    }

    public function getName()
    {
        return $this->userData["name"];
    }

    public function getEmail()
    {
        return $this->userData["email"];
    }

    public function getTokenExpiredAt()
    {
        return DateTime::createFromFormat(
            DateTime::ISO8601, $this->userData["token_expired_at"]
        );
    }

    public function getSlToken()
    {
        return $this->userData["sl_token"];
    }

    public function setSlToken($slToken)
    {
        $this->userData["sl_token"] = $slToken;
    }

    public function setTokenExpiredAt($tokenExpiredAt)
    {
        $this->userData["token_expired_at"] = $tokenExpiredAt->format(DateTime::ISO8601);
    }

    public function save()
    {
        $this->userModel->save($this->userData);
    }

    public function getUserData()
    {
        return $this->userData;
    }
}