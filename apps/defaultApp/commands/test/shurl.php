<?php

namespace mpcmf\apps\defaultApp\commands\test;

use mpcmf\apps\defaultApp\libraries\shurl\shurlLib;
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
class shurl
    extends consoleCommandBase
{

    /**
     * Define arguments
     *
     * @return mixed
     */
    protected function defineArguments()
    {
        $this->addArgument('url', InputArgument::OPTIONAL, 'Url');
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

        $shurl = new shurlLib();

        if(!empty($url)) {
            var_dump($url, $shurl->hash($url));
            exit;
        }

        $hashes = [];

        $offset = 100000;
        $count = 1000000;
        $pb = new progressBar($count);
        for($i = 0; $i < $count; $i++) {
            $tipaUrl = $i + $offset;
            $hash = $shurl->hash((string)$tipaUrl);
            if(!isset($hashes[$hash])) {
                $hashes[$hash] = $tipaUrl;

            } else {

                var_dump('COLLISION', $hash, $hashes[$hash], $tipaUrl);
            }
            if($count % 1000 === 0) {
                $pb->update($i);
                $pb->draw();
            }
        }
    }
}
