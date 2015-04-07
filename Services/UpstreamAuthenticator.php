<?php

namespace Webridge\Oauth2AccessBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

class UpstreamAuthenticator extends ContainerAware
{
    public function getBaseUrl()
    {
        return $this->container->getParameter('webridge_oauth2_access.upstream_base_url');
    }
}
