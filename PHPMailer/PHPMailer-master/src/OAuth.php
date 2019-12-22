<?php
/**
 * PHPMailer &#45; PHP email creation and transport class.
 * PHP Version 5.5.
 *
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 &#45; 2015 Marcus Bointon
 * @copyright 2010 &#45; 2012 Jim Jagielski
 * @copyright 2004 &#45; 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful &#45; WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

use League\OAuth2\Client\Grant\RefreshToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * OAuth &#45; OAuth2 authentication wrapper class.
 * Uses the oauth2&#45;client package from the League of Extraordinary Packages.
 *
 * @see     http://oauth2&#45;client.thephpleague.com
 *
 * @author  Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 */
class OAuth
{
    /**
     * An instance of the League OAuth Client Provider.
     *
     * @var AbstractProvider
     */
    protected $provider;

    /**
     * The current OAuth access token.
     *
     * @var AccessToken
     */
    protected $oauthToken;

    /**
     * The user&apos;s email address, usually used as the login ID
     * and also the from address when sending email.
     *
     * @var string
     */
    protected $oauthUserEmail = &apos;&apos;;

    /**
     * The client secret, generated in the app definition of the service you&apos;re connecting to.
     *
     * @var string
     */
    protected $oauthClientSecret = &apos;&apos;;

    /**
     * The client ID, generated in the app definition of the service you&apos;re connecting to.
     *
     * @var string
     */
    protected $oauthClientId = &apos;&apos;;

    /**
     * The refresh token, used to obtain new AccessTokens.
     *
     * @var string
     */
    protected $oauthRefreshToken = &apos;&apos;;

    /**
     * OAuth constructor.
     *
     * @param array $options Associative array containing
     *                       `provider`, `userName`, `clientSecret`, `clientId` and `refreshToken` elements
     */
    public function __construct($options)
    {
        $this&#45;>provider = $options[&apos;provider&apos;];
        $this&#45;>oauthUserEmail = $options[&apos;userName&apos;];
        $this&#45;>oauthClientSecret = $options[&apos;clientSecret&apos;];
        $this&#45;>oauthClientId = $options[&apos;clientId&apos;];
        $this&#45;>oauthRefreshToken = $options[&apos;refreshToken&apos;];
    }

    /**
     * Get a new RefreshToken.
     *
     * @return RefreshToken
     */
    protected function getGrant()
    {
        return new RefreshToken();
    }

    /**
     * Get a new AccessToken.
     *
     * @return AccessToken
     */
    protected function getToken()
    {
        return $this&#45;>provider&#45;>getAccessToken(
            $this&#45;>getGrant(),
            [&apos;refresh_token&apos; => $this&#45;>oauthRefreshToken]
        );
    }

    /**
     * Generate a base64&#45;encoded OAuth token.
     *
     * @return string
     */
    public function getOauth64()
    {
        // Get a new token if it&apos;s not available or has expired
        if (null === $this&#45;>oauthToken or $this&#45;>oauthToken&#45;>hasExpired()) {
            $this&#45;>oauthToken = $this&#45;>getToken();
        }

        return base64_encode(
            &apos;user=&apos; .
            $this&#45;>oauthUserEmail .
            "\001auth=Bearer " .
            $this&#45;>oauthToken .
            "\001\001"
        );
    }
}
