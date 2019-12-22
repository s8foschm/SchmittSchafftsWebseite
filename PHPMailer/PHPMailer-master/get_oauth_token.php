<?php
/**
 * PHPMailer &#45; PHP email creation and transport class.
 * PHP Version 5.5
 * @package PHPMailer
 * @see https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 &#45; 2017 Marcus Bointon
 * @copyright 2010 &#45; 2012 Jim Jagielski
 * @copyright 2004 &#45; 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful &#45; WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * Get an OAuth2 token from an OAuth2 provider.
 * * Install this script on your server so that it&apos;s accessible
 * as [https/http]://<yourdomain>/<folder>/get_oauth_token.php
 * e.g.: http://localhost/phpmailer/get_oauth_token.php
 * * Ensure dependencies are installed with &apos;composer install&apos;
 * * Set up an app in your Google/Yahoo/Microsoft account
 * * Set the script address as the app&apos;s redirect URL
 * If no refresh token is obtained when running this file,
 * revoke access to your app and run the script again.
 */

namespace PHPMailer\PHPMailer;

/**
 * Aliases for League Provider Classes
 * Make sure you have added these to your composer.json and run `composer install`
 * Plenty to choose from here:
 * @see http://oauth2&#45;client.thephpleague.com/providers/thirdparty/
 */
// @see https://github.com/thephpleague/oauth2&#45;google
use League\OAuth2\Client\Provider\Google;
// @see https://packagist.org/packages/hayageek/oauth2&#45;yahoo
use Hayageek\OAuth2\Client\Provider\Yahoo;
// @see https://github.com/stevenmaguire/oauth2&#45;microsoft
use Stevenmaguire\OAuth2\Client\Provider\Microsoft;

if (!isset($_GET[&apos;code&apos;]) && !isset($_GET[&apos;provider&apos;])) {
?>
<html>
<body>Select Provider:<br/>
<a href=&apos;?provider=Google&apos;>Google</a><br/>
<a href=&apos;?provider=Yahoo&apos;>Yahoo</a><br/>
<a href=&apos;?provider=Microsoft&apos;>Microsoft/Outlook/Hotmail/Live/Office365</a><br/>
</body>
</html>
<?php
exit;
}

require &apos;vendor/autoload.php&apos;;

session_start();

$providerName = &apos;&apos;;

if (array_key_exists(&apos;provider&apos;, $_GET)) {
    $providerName = $_GET[&apos;provider&apos;];
    $_SESSION[&apos;provider&apos;] = $providerName;
} elseif (array_key_exists(&apos;provider&apos;, $_SESSION)) {
    $providerName = $_SESSION[&apos;provider&apos;];
}
if (!in_array($providerName, [&apos;Google&apos;, &apos;Microsoft&apos;, &apos;Yahoo&apos;])) {
    exit(&apos;Only Google, Microsoft and Yahoo OAuth2 providers are currently supported in this script.&apos;);
}

//These details are obtained by setting up an app in the Google developer console,
//or whichever provider you&apos;re using.
$clientId = &apos;RANDOMCHARS&#45;&#45;&#45;&#45;&#45;duv1n2.apps.googleusercontent.com&apos;;
$clientSecret = &apos;RANDOMCHARS&#45;&#45;&#45;&#45;&#45;lGyjPcRtvP&apos;;

//If this automatic URL doesn&apos;t work, set it yourself manually to the URL of this script
$redirectUri = (isset($_SERVER[&apos;HTTPS&apos;]) ? &apos;https://&apos; : &apos;http://&apos;) . $_SERVER[&apos;HTTP_HOST&apos;] . $_SERVER[&apos;PHP_SELF&apos;];
//$redirectUri = &apos;http://localhost/PHPMailer/redirect&apos;;

$params = [
    &apos;clientId&apos; => $clientId,
    &apos;clientSecret&apos; => $clientSecret,
    &apos;redirectUri&apos; => $redirectUri,
    &apos;accessType&apos; => &apos;offline&apos;
];

$options = [];
$provider = null;

switch ($providerName) {
    case &apos;Google&apos;:
        $provider = new Google($params);
        $options = [
            &apos;scope&apos; => [
                &apos;https://mail.google.com/&apos;
            ]
        ];
        break;
    case &apos;Yahoo&apos;:
        $provider = new Yahoo($params);
        break;
    case &apos;Microsoft&apos;:
        $provider = new Microsoft($params);
        $options = [
            &apos;scope&apos; => [
                &apos;wl.imap&apos;,
                &apos;wl.offline_access&apos;
            ]
        ];
        break;
}

if (null === $provider) {
    exit(&apos;Provider missing&apos;);
}

if (!isset($_GET[&apos;code&apos;])) {
    // If we don&apos;t have an authorization code then get one
    $authUrl = $provider&#45;>getAuthorizationUrl($options);
    $_SESSION[&apos;oauth2state&apos;] = $provider&#45;>getState();
    header(&apos;Location: &apos; . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET[&apos;state&apos;]) || ($_GET[&apos;state&apos;] !== $_SESSION[&apos;oauth2state&apos;])) {
    unset($_SESSION[&apos;oauth2state&apos;]);
    unset($_SESSION[&apos;provider&apos;]);
    exit(&apos;Invalid state&apos;);
} else {
    unset($_SESSION[&apos;provider&apos;]);
    // Try to get an access token (using the authorization code grant)
    $token = $provider&#45;>getAccessToken(
        &apos;authorization_code&apos;,
        [
            &apos;code&apos; => $_GET[&apos;code&apos;]
        ]
    );
    // Use this to interact with an API on the users behalf
    // Use this to get a new access token if the old one expires
    echo &apos;Refresh Token: &apos;, $token&#45;>getRefreshToken();
}
