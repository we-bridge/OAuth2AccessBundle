<?php
namespace Webridge\Oauth2AccessBundle\User;

interface UpstreamUserInterface
{
    /**
     * Set upstreamUserId
     *
     * @param string $upstreamUserId
     * @return Oauth2AccessUserInterface
     */
    public function setUpstreamUserId($upstreamUserId);

    /**
     * Get upstreamUserId
     *
     * @return string
     */
    public function getUpstreamUserId();
}
