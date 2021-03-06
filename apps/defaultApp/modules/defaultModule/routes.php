<?php

namespace mpcmf\modules\defaultModule;

use mpcmf\modules\authex\actions\userActions;
use mpcmf\modules\moduleBase\actions\action;
use mpcmf\modules\moduleBase\actions\actionsBase;
use mpcmf\modules\moduleBase\exceptions\actionException;
use mpcmf\modules\moduleBase\moduleRoutesBase;
use mpcmf\system\acl\aclManager;
use mpcmf\system\pattern\singleton;

/**
 * airgate module routes
 *
 * @author Gregory Ostrovsky <greevex@gmail.com>
 * @date 2015-08-12
 */
class routes
    extends moduleRoutesBase
{

    use singleton;

    /**
     * Register some routes
     *
     * @return mixed
     * @throws actionException
     */
    public function bind()
    {

    }
}