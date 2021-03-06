<?php
namespace mpcmf\modules\defaultModule\models;

use mpcmf\modules\defaultModule\mappers\settingsMapper;
use mpcmf\modules\moduleBase\models\modelBase;
use mpcmf\system\cache\cache;
use mpcmf\system\pattern\singleton;

/**
 * Class shlinkModel
 *
 *
  * @generated by mpcmf/codeManager
 *
 * @package mpcmf\modules\defaultModule\models
 * @date 2016-07-29 11:38:42
 *
 * @author Gregory Ostrovsky <greevex@gmail.com>
 *
 * @method string getMongoId() Mongo ID
 * @method $this setMongoId(string $value) Mongo ID
 * @method string getCIp() C ip
 * @method $this setCIp(string $value) C ip
 * @method boolean getCJs() C js
 * @method $this setCJs(boolean $value) C js
 * @method int getCTm() C tm
 * @method $this setCTm(int $value) C tm
 * @method string getCUa() C ua
 * @method $this setCUa(string $value) C ua
 * @method string getId() Id
 * @method $this setId(string $value) Id
 * @method string getLong() Long
 * @method $this setLong(string $value) Long
 * @method array getOther() Other
 * @method $this setOther(array $value) Other
 * @method string getShort() Short
 * @method $this setShort(string $value) Short
 */
class shlinkModel
    extends modelBase
{

    use singleton;

    const BASE_DOMAIN_CKEY = 'shlink.base_domain';

    public function getShortUrl($https = false)
    {

        return ($https ? 'https' : 'http') . "://{$this->getBaseDomain()}/{$this->getShort()}";
    }

    protected function getBaseDomain()
    {
        $baseDomain = cache::getCached(self::BASE_DOMAIN_CKEY);
        if(!$baseDomain) {

            $baseDomain = settingsMapper::getInstance()->getById(self::BASE_DOMAIN_CKEY)->getValue();

            cache::setCached(self::BASE_DOMAIN_CKEY, $baseDomain);
        }

        return $baseDomain;
    }
}