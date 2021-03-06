<?php
namespace mpcmf\modules\defaultModule\mappers;

use mpcmf\apps\defaultApp\libraries\shurl\shurlLib;
use mpcmf\modules\defaultModule\models\shlinkModel;
use mpcmf\modules\moduleBase\exceptions\mapperException;
use mpcmf\modules\moduleBase\mappers\mapperBase;
use mpcmf\system\cache\cache;
use mpcmf\system\pattern\singleton;

/**
 * Class shlinkMapper
 *
 *
 * @generated by mpcmf/codeManager
 *
 * @package mpcmf\modules\defaultModule\mappers
 * @date 2016-07-29 11:38:42
 *
 * @author Gregory Ostrovsky <greevex@gmail.com>
 */
class shlinkMapper
    extends mapperBase
{

    use singleton;

    const HASH_LENGTH_CKEY = 'shlink.baseLength';

    const FIELD__C_IP = 'c_ip';
    const FIELD__C_JS = 'c_js';
    const FIELD__C_TM = 'c_tm';
    const FIELD__C_UA = 'c_ua';
    const FIELD__ID = 'id';
    const FIELD__LONG = 'long';
    const FIELD__OTHER = 'other';
    const FIELD__SHORT = 'short';

    public function getPublicName()
    {
        return 'Short link';
    }

    /**
     * Entity map
     *
     * @return array[]
     */
    public function getMap()
    {
        return [
            self::FIELD__C_IP => [
                'getter' => 'getCIp',
                'setter' => 'setCIp',
                'role' => [
                    'searchable' => true,
                    'sortable' => true,
                ],
                'name' => 'C ip',
                'description' => 'C ip',
                'type' => 'string',
                'formType' => 'text',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => true,
                    'unique' => false,
                ],
            ],
            self::FIELD__C_JS => [
                'getter' => 'getCJs',
                'setter' => 'setCJs',
                'role' => [
                ],
                'name' => 'C js',
                'description' => 'C js',
                'type' => 'boolean',
                'formType' => 'checkbox',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => false,
                    'unique' => false,
                ],
            ],
            self::FIELD__C_TM => [
                'getter' => 'getCTm',
                'setter' => 'setCTm',
                'role' => [
                    'sortable' => true,
                ],
                'name' => 'C tm',
                'description' => 'C tm',
                'type' => 'int',
                'formType' => 'datetimepicker',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => true,
                    'unique' => false,
                ],
            ],
            self::FIELD__C_UA => [
                'getter' => 'getCUa',
                'setter' => 'setCUa',
                'role' => [
                    'searchable' => true,
                ],
                'name' => 'C ua',
                'description' => 'C ua',
                'type' => 'string',
                'formType' => 'text',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => true,
                    'unique' => false,
                ],
            ],
            self::FIELD__LONG => [
                'getter' => 'getLong',
                'setter' => 'setLong',
                'role' => [
                ],
                'name' => 'Long',
                'description' => 'Long',
                'type' => 'string',
                'formType' => 'text',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => true,
                    'unique' => false,
                ],
            ],
            self::FIELD__OTHER => [
                'getter' => 'getOther',
                'setter' => 'setOther',
                'role' => [
                ],
                'name' => 'Other',
                'description' => 'Other',
                'type' => 'array',
                'formType' => 'json',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => false,
                    'unique' => false,
                ],
            ],
            self::FIELD__SHORT => [
                'getter' => 'getShort',
                'setter' => 'setShort',
                'role' => [
                    'key' => true,
                    'generate-key' => false,
                ],
                'name' => 'Short',
                'description' => 'Short',
                'type' => 'string',
                'formType' => 'text',
                'validator' => [
                ],
                'relations' => [
                ],
                'options' => [
                    'required' => true,
                    'unique' => false,
                ],
            ],
        ];
    }

    /**
     * @param string $url
     * @param array  $params 'is_web' : bool, 'addr' : string, 'user_agent' : string, 'js' : boolean
     *
     * @return shlinkModel
     * @throws \mpcmf\modules\moduleBase\exceptions\mapperException
     */
    public function storeByUrl($url, array $params = [
        'is_web' => false,
        'addr' => 'console',
        'user_agent' => 'console',
        'js' => false,
    ])
    {
        $attempts = 3;
        do {
            error_log("Iteration started for {$url}");

            $baseLength = $this->getBaseHashLength();
            $shortHash = shurlLib::getInstance()->mixed($url, $baseLength);

            $newModel = shlinkModel::fromArray([
                self::FIELD__C_IP => isset($params['addr']) ? $params['addr'] : 'console',
                self::FIELD__C_JS => isset($params['js']) && $params['js'],
                self::FIELD__C_TM => time(),
                self::FIELD__C_UA => isset($params['user_agent']) ? $params['user_agent'] : 'console',
                self::FIELD__LONG => $url,
                self::FIELD__SHORT => $shortHash,
                self::FIELD__OTHER => $params,
            ]);

            /** @noinspection PhpUnusedLocalVariableInspection */
            $saved = false;
            try {
                $this->save($newModel);
                $saved = true;
            } catch (mapperException $mongoException) {
                MPCMF_LL_DEBUG && error_log("[ERROR] mongoExc on shurl store: {$mongoException->getMessage()}");
                try {
                    MPCMF_LL_DEBUG && error_log("Trying to find already saved url [{$shortHash}] ...");
                    /** @var shlinkModel $savedModel */
                    $savedModel = $this->getBy([
                        self::FIELD__SHORT => $shortHash,
                        self::FIELD__LONG => $url,
                    ]);
                    $saved = true;
                } catch(mapperException $savedModelMapperExc) {
                    MPCMF_LL_DEBUG && error_log("[ERROR] mapperExc on shurl search after store error: {$savedModelMapperExc->getMessage()}");
                    $baseLength++;
                    $this->setBaseHashLength($baseLength);
                    continue;
                }

                error_log("Saved url was found [{$shortHash}] ...");
                $newModel = $savedModel;
            }
        } while(!$saved && --$attempts > 0);

        return $newModel;
    }

    protected function getBaseHashLength()
    {
        $length = cache::getCached(self::HASH_LENGTH_CKEY);
        if(!$length) {

            $length = settingsMapper::getInstance()->getById(self::HASH_LENGTH_CKEY)->getValue();
            cache::setCached(self::HASH_LENGTH_CKEY, $length);
        }

        return $length;
    }

    protected function setBaseHashLength($length)
    {
        cache::setCached(self::HASH_LENGTH_CKEY, $length);
        settingsMapper::getInstance()->updateBy([
            settingsMapper::FIELD__KEY => self::HASH_LENGTH_CKEY
        ], [
            settingsMapper::FIELD__VALUE => $length
        ]);
    }
}