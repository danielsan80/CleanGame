<?php

namespace Dan\CleanGame\Google;

require_once __DIR__.'/../../../../src/Google/google-api-php-client/src/Google_Client.php';

class Client extends \Google_Client {

    private $session;
    
    public function setSession($session)
    {
        $this->session = $session;
        if ($session->get('token')) {
            $this->setAccessToken($session->get('token'));
        }
    }
    
    public function authenticate($code = null)
    {
        $token = parent::authenticate($code);
        $this->session->set('token', $this->getAccessToken());
        return $token;
    }
    
    public function logout()
    {
        $this->session->set('token', null);
    }
}

?>
