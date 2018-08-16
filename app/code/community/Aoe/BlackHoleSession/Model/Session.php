<?php

class Aoe_BlackHoleSession_Model_Session extends Mage_Core_Model_Session
{

    protected $isBot = false;

    public function __construct(array $data)
    {
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $config = Mage::getConfig()->getNode('global');
            if (($config = $config->descend('aoeblackholesession')) && $botRegex = (string)$config->descend('bot_regex')) {
                if (preg_match($botRegex, $_SERVER['HTTP_USER_AGENT'])) {
                    $this->isBot = true;
                }
            }
        }
        parent::__construct($data);
    }

    public function getSessionSaveMethod()
    {
        if ($this->isBot) {
            return 'user';
        } else {
            return parent::getSessionSaveMethod();
        }
    }

    public function getSessionSavePath()
    {
        if ($this->isBot) {
            /** @var Aoe_BlackHoleSession_Model_SessionHandler $sessionHandler */
            $sessionHandler = Mage::getModel('aoeblackholesession/sessionHandler');
            return array($sessionHandler, 'setHandler');
        } else {
            return parent::getSessionSavePath();
        }
    }

}