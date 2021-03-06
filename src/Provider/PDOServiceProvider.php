<?php

namespace CSanquer\Silex\PdoServiceProvider\Provider;

use CSanquer\Silex\PdoServiceProvider\Config\PdoConfigFactory;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Database PDO Provider.
 *
 * @author Charles Sanquer <charles.sanquer@gmail.com>
 */
class PDOServiceProvider implements ServiceProviderInterface
{
    /**
     * @param string $prefix
     */
    protected $prefix;

    /**
     * @param string $prefix Prefix name used to register the service provider in Silex.
     */
    public function __construct($prefix = 'pdo')
    {
        if (empty($prefix)) {
            throw new \InvalidArgumentException('The specified prefix is not valid.');
        }

        $this->prefix = $prefix;
    }

    /**
     * @param Application $app
     * @param string      $prefix
     *
     * @return \PDO
     */
    protected function getPdo(Application $app, $prefix)
    {
        return $app->share(function () use ($app, $prefix) {
            $factory = new PdoConfigFactory();

            $options = array_replace(
                array(
                    'driver' => 'sqlite',
                    'options' => array(),
                ),
                isset($app[$prefix.'.options']) ? (array) $app[$prefix.'.options'] : array()
            );
            $app[$prefix.'.options'] = $options;

            $cfg = $factory->createConfig($options);

            return $cfg->connect($options);
        });
    }

    public function register(Application $app)
    {
        $prefix = $this->prefix;
        $app[$prefix] = $this->getPdo($app, $prefix);
    }

    public function boot(Application $app)
    {

    }
}
