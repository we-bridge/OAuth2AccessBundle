<?php

namespace Webridge\Oauth2AccessBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

use Webridge\Oauth2AccessBundle\DependencyInjection\WebridgeOauth2AccessExtension;

class WebridgeOauth2AccessExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDatabaseDriverSet()
    {
        $loader = new WebridgeOauth2AccessExtension();
        $config = $this->getEmptyConfig();
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * getEmptyConfig
     *
     * @return array
     */
    protected function getEmptyConfig()
    {
        $parser = new Parser();

        return $parser->parse("");
    }
}
