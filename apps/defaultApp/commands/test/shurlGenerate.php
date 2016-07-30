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
class shurlGenerate
    extends consoleCommandBase
{

    /**
     * Define arguments
     *
     * @return mixed
     */
    protected function defineArguments()
    {
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
        $this->generateGreat();
    }

    public function generateGreat()
    {
        $length = 4;
        $options = [
            'h0' => 3583331880,
            'h1' => 2336997804,
            'h2' => 2214680746,

            'k0' => 1005295650,
            'k1' => 3334547280,
            'k2' => 152863840,
            'k3' => 3472684634
        ];

        $shurl = new shurlLib();
        $count = 15000;
        $initOffset = 'aaaa';

        $greatestCollision = 9999999;

        $glIt = 0;
        for(;;) {
            $glIt++;

            $counter = 0;
            $collisions = 0;
            $hashes = [];

            $pb = new progressBar($count);
            for($i = $initOffset; $counter <= $count; $i++) {
                $counter++;
                $hash = $shurl->hash2($i, $length, $options);

                if(!isset($hashes[$hash])) {
                    $hashes[$hash] = true;

                } elseif(++$collisions > $greatestCollision) {
                    error_log("\n[COLLISIONS] Breaking cycle, cuz too much collisions ({$collisions} > {$greatestCollision}");
                    break;
                }
                if($counter % 500 === 0) {
                    $pb->update($counter);
                    $pb->draw();
                    error_log("\n[COLLISIONS] {$collisions} on {$counter}");
                }
            }

            if($collisions <= $greatestCollision) {
                file_put_contents("/tmp/SHURL/{$collisions}.i-{$glIt}.json", json_encode($options, 448));
                error_log("\n[!!! GREAT FOUND !!!] old: {$greatestCollision} / new: {$collisions} {on {$count} iterations}");
                error_log(json_encode($options, 448));
                $greatestCollision = $collisions;
            } else {
                error_log("\n[~~~ NOT GREAT !~~] old: {$greatestCollision} / new: {$collisions} {on {$count} iterations}");
            }

            $options = [
                'h0' => mt_rand(0x00000000, 0xFFFFFFFF),
                'h1' => mt_rand(0x00000000, 0xFFFFFFFF),
                'h2' => mt_rand(0x00000000, 0xFFFFFFFF),

                'k0' => mt_rand(0x00000000, 0xFFFFFFFF),
                'k1' => mt_rand(0x00000000, 0xFFFFFFFF),
                'k2' => mt_rand(0x00000000, 0xFFFFFFFF),
                'k3' => mt_rand(0x00000000, 0xFFFFFFFF),
            ];
        }
    }
}
