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
        $this->addArgument('type', InputArgument::OPTIONAL, 'Hash type', 'hash2');
        $this->addArgument('length', InputArgument::OPTIONAL, 'Hash length', 12);
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
        $type = $input->getArgument('type');
        $length = $input->getArgument('length');

        $shurl = new shurlLib();

        if(!empty($url)) {
            var_dump($url, $shurl->{$type}($url, $length));
            exit;
        }

        $hashes = [];

        $offset = 'aaaa';
        $count = 15000;
        $pb = new progressBar($count);
        $counter = 1;
        $collisions = 0;
        for($i = $offset; $counter <= $count; $i++, $counter++) {
            $tipaUrl = $i;
            $hash = $shurl->{$type}((string)$tipaUrl, $length);
            if(!isset($hashes[$hash])) {
                $hashes[$hash] = $tipaUrl;

            } else {
                $collisions++;
                error_log("\n[COLLISION HASH] ({$counter}) {$hash} on {$hashes[$hash]} and {$tipaUrl}");
            }
            if($counter % 100 === 0) {
                $pb->update($counter);
                $pb->draw();
            }
        }

        error_log("\n COLLISION: {$collisions} on {$count}");
    }
}
