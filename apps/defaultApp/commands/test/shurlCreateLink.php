<?php

namespace mpcmf\apps\defaultApp\commands\test;

use mpcmf\apps\defaultApp\libraries\shurl\shurlLib;
use mpcmf\modules\defaultModule\mappers\shlinkMapper;
use mpcmf\system\application\consoleCommandBase;
use mpcmf\system\helper\console\progressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Test sds app command
 *
 * @author Gregory Ostrovsky <greevex@gmail.com>
 */
class shurlCreateLink
    extends consoleCommandBase
{

    /**
     * Define arguments
     *
     * @return mixed
     */
    protected function defineArguments()
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'Url');
    }

    /**
     * Executes the current command.
     *
     * This method is not because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this method is not implemented
     *
     * @see setCode()
     */
    protected function handle(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');

        $shlink = shlinkMapper::getInstance()->storeByUrl($url);

        var_dump($shlink->export());
    }
}
