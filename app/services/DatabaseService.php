<?php

namespace App\Services;

use Kdyby\Console\Application;
use Kdyby\Console\StringOutput;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\DI\Container;
use Symfony\Component\Console\Input\ArrayInput;


/**
 * Služba pro správu databáze.
 *
 * @author Jan Staněk <jan.stanek@skaut.cz>
 */
class DatabaseService
{
    /**
     * @var Application
     */
    public $application;

    /**
     * @var Container
     */
    public $container;

    /**
     * @var string
     */
    public $dir;

    /**
     * @var Cache
     */
    protected $cache;

    
    /**
     * DatabaseService constructor.
     * @param string $dir
     * @param Application $application
     * @param Container $container
     * @param IStorage $storage
     */
    public function __construct($dir, Application $application, Container $container, IStorage $storage)
    {
        $this->dir = $dir;
        $this->application = $application;
        $this->container = $container;

        $this->cache = new Cache($storage, 'Database');
    }

    /**
     * Vytvoří zálohu databáze a spustí migrace. Spouští se pouze pokud není v cache záznam o provedeném update.
     */
    public function update()
    {
        if ($this->cache->load('updated') === NULL) {
            $this->cache->save('lock', function () {
                if ($this->cache->load('updated') === NULL) {
                    $this->cache->save('updated', new \DateTime());

                    $this->backup();

                    $output = new StringOutput();
                    $input = new ArrayInput([
                        'command' => 'migrations:migrate',
                        '--no-interaction' => TRUE
                    ]);
                    $this->application->run($input, $output);
                }
                return TRUE;
            });
        }
    }

    /**
     * Vytvoří zálohu databáze.
     */
    public function backup()
    {
        $database = $this->container->parameters['database'];

        $host = $database['host'];
        $user = $database['user'];
        $password = $database['password'];
        $dbname = $database['dbname'];

        $dump = new \MySQLDump(new \mysqli($host, $user, $password, $dbname));

        $timestamp = (new \DateTime())->format('YmdHi');

        $dump->save($this->dir . '/backup/' . $dbname . '-' . $timestamp . '.sql.gz');
    }
}

