<?php
namespace mpcmf\modules\defaultModule\controllers;

use mpcmf\apps\defaultApp\libraries\shurl\shurlLib;
use mpcmf\modules\defaultModule\mappers\shlinkMapper;
use mpcmf\modules\defaultModule\models\shlinkModel;
use mpcmf\modules\moduleBase\controllers\controllerBase;
use mpcmf\system\pattern\singleton;

/**
 * Class shlinkController
 *
 *
  * @generated by mpcmf/codeManager
 *
 * @package mpcmf\modules\defaultModule\controllers;
 * @date 2016-07-29 11:38:42
 *
 * @author Gregory Ostrovsky <greevex@gmail.com>
 */
class shlinkController
    extends controllerBase
{

    use singleton;

    public function __home()
    {
        return self::success([
            'shlinkMapper' => shlinkMapper::getInstance()
        ]);
    }

    public function __shorten()
    {
        $js = (bool)$this->getSlim()->request()->post('js');
        $url = (string)$this->getSlim()->request()->post('url');
        $url = trim($url);

        /** @noinspection IsEmptyFunctionUsageInspection */
        if(empty($url)) {
            return self::error([
                'url' => $url,
                'error' => 'URL is empty',
            ]);
        }
        if(!preg_match('/[a-z]+\:\/\//ui', $url)) {
            return self::error([
                'url' => $url,
                'error' => 'Invalid URL',
            ]);
        }

        $params = [
            'is_web' => true,
            'addr' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'js' => $js,
        ];

        try {
            $shlinkModel = shlinkMapper::getInstance()->storeByUrl($url, $params);
        } catch(\Exception $e) {

            return self::errorByException($e);
        }

        return self::success([
            'url' => $url,
            'shlink' => $shlinkModel,
        ]);
    }

    public function __redirect($short)
    {
        try {
            /** @var shlinkModel $shlink */
            $shlink = shlinkMapper::getInstance()->getById($short);
            $longUrl = $shlink->getLong();
            $slim = $this->getSlim();

            $slim->setCookie('usr', $this->generateCookieUser(), strtotime('+6 month'));
            $this->getSlim()->redirect($longUrl, 301);

            return self::success([
                'url' => $longUrl
            ]);
        } catch(\Exception $e) {
            return self::errorByException($e);
        }
    }

    private function generateCookieUser()
    {
        return shurlLib::getInstance()->mixed("{$_SERVER['REMOTE_ADDR']}:{$_SERVER['HTTP_USER_AGENT']}", 12);
    }
}