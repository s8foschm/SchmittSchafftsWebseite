<?php
/**
 * PHPMailer POP&#45;Before&#45;SMTP Authentication Class.
 * PHP Version 5.5.
 *
 * @see       https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 *
 * @author    Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author    Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author    Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author    Brent R. Matzelle (original founder)
 * @copyright 2012 &#45; 2017 Marcus Bointon
 * @copyright 2010 &#45; 2012 Jim Jagielski
 * @copyright 2004 &#45; 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful &#45; WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace PHPMailer\PHPMailer;

/**
 * PHPMailer POP&#45;Before&#45;SMTP Authentication Class.
 * Specifically for PHPMailer to use for RFC1939 POP&#45;before&#45;SMTP authentication.
 * 1) This class does not support APOP authentication.
 * 2) Opening and closing lots of POP3 connections can be quite slow. If you need
 *   to send a batch of emails then just perform the authentication once at the start,
 *   and then loop through your mail sending script. Providing this process doesn&apos;t
 *   take longer than the verification period lasts on your POP3 server, you should be fine.
 * 3) This is really ancient technology; you should only need to use it to talk to very old systems.
 * 4) This POP3 class is deliberately lightweight and incomplete, and implements just
 *   enough to do authentication.
 *   If you want a more complete class there are other POP3 classes for PHP available.
 *
 * @author  Richard Davey (original author) <rich@corephp.co.uk>
 * @author  Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author  Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author  Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 */
class POP3
{
    /**
     * The POP3 PHPMailer Version number.
     *
     * @var string
     */
    const VERSION = &apos;6.0.7&apos;;

    /**
     * Default POP3 port number.
     *
     * @var int
     */
    const DEFAULT_PORT = 110;

    /**
     * Default timeout in seconds.
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * Debug display level.
     * Options: 0 = no, 1+ = yes.
     *
     * @var int
     */
    public $do_debug = 0;

    /**
     * POP3 mail server hostname.
     *
     * @var string
     */
    public $host;

    /**
     * POP3 port number.
     *
     * @var int
     */
    public $port;

    /**
     * POP3 Timeout Value in seconds.
     *
     * @var int
     */
    public $tval;

    /**
     * POP3 username.
     *
     * @var string
     */
    public $username;

    /**
     * POP3 password.
     *
     * @var string
     */
    public $password;

    /**
     * Resource handle for the POP3 connection socket.
     *
     * @var resource
     */
    protected $pop_conn;

    /**
     * Are we connected?
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * Error container.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Line break constant.
     */
    const LE = "\r\n";

    /**
     * Simple static wrapper for all&#45;in&#45;one POP before SMTP.
     *
     * @param string   $host        The hostname to connect to
     * @param int|bool $port        The port number to connect to
     * @param int|bool $timeout     The timeout value
     * @param string   $username
     * @param string   $password
     * @param int      $debug_level
     *
     * @return bool
     */
    public static function popBeforeSmtp(
        $host,
        $port = false,
        $timeout = false,
        $username = &apos;&apos;,
        $password = &apos;&apos;,
        $debug_level = 0
    ) {
        $pop = new self();

        return $pop&#45;>authorise($host, $port, $timeout, $username, $password, $debug_level);
    }

    /**
     * Authenticate with a POP3 server.
     * A connect, login, disconnect sequence
     * appropriate for POP&#45;before SMTP authorisation.
     *
     * @param string   $host        The hostname to connect to
     * @param int|bool $port        The port number to connect to
     * @param int|bool $timeout     The timeout value
     * @param string   $username
     * @param string   $password
     * @param int      $debug_level
     *
     * @return bool
     */
    public function authorise($host, $port = false, $timeout = false, $username = &apos;&apos;, $password = &apos;&apos;, $debug_level = 0)
    {
        $this&#45;>host = $host;
        // If no port value provided, use default
        if (false === $port) {
            $this&#45;>port = static::DEFAULT_PORT;
        } else {
            $this&#45;>port = (int) $port;
        }
        // If no timeout value provided, use default
        if (false === $timeout) {
            $this&#45;>tval = static::DEFAULT_TIMEOUT;
        } else {
            $this&#45;>tval = (int) $timeout;
        }
        $this&#45;>do_debug = $debug_level;
        $this&#45;>username = $username;
        $this&#45;>password = $password;
        //  Reset the error log
        $this&#45;>errors = [];
        //  connect
        $result = $this&#45;>connect($this&#45;>host, $this&#45;>port, $this&#45;>tval);
        if ($result) {
            $login_result = $this&#45;>login($this&#45;>username, $this&#45;>password);
            if ($login_result) {
                $this&#45;>disconnect();

                return true;
            }
        }
        // We need to disconnect regardless of whether the login succeeded
        $this&#45;>disconnect();

        return false;
    }

    /**
     * Connect to a POP3 server.
     *
     * @param string   $host
     * @param int|bool $port
     * @param int      $tval
     *
     * @return bool
     */
    public function connect($host, $port = false, $tval = 30)
    {
        //  Are we already connected?
        if ($this&#45;>connected) {
            return true;
        }

        //On Windows this will raise a PHP Warning error if the hostname doesn&apos;t exist.
        //Rather than suppress it with @fsockopen, capture it cleanly instead
        set_error_handler([$this, &apos;catchWarning&apos;]);

        if (false === $port) {
            $port = static::DEFAULT_PORT;
        }

        //  connect to the POP3 server
        $this&#45;>pop_conn = fsockopen(
            $host, //  POP3 Host
            $port, //  Port #
            $errno, //  Error Number
            $errstr, //  Error Message
            $tval
        ); //  Timeout (seconds)
        //  Restore the error handler
        restore_error_handler();

        //  Did we connect?
        if (false === $this&#45;>pop_conn) {
            //  It would appear not...
            $this&#45;>setError(
                "Failed to connect to server $host on port $port. errno: $errno; errstr: $errstr"
            );

            return false;
        }

        //  Increase the stream time&#45;out
        stream_set_timeout($this&#45;>pop_conn, $tval, 0);

        //  Get the POP3 server response
        $pop3_response = $this&#45;>getResponse();
        //  Check for the +OK
        if ($this&#45;>checkResponse($pop3_response)) {
            //  The connection is established and the POP3 server is talking
            $this&#45;>connected = true;

            return true;
        }

        return false;
    }

    /**
     * Log in to the POP3 server.
     * Does not support APOP (RFC 2828, 4949).
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function login($username = &apos;&apos;, $password = &apos;&apos;)
    {
        if (!$this&#45;>connected) {
            $this&#45;>setError(&apos;Not connected to POP3 server&apos;);
        }
        if (empty($username)) {
            $username = $this&#45;>username;
        }
        if (empty($password)) {
            $password = $this&#45;>password;
        }

        // Send the Username
        $this&#45;>sendString("USER $username" . static::LE);
        $pop3_response = $this&#45;>getResponse();
        if ($this&#45;>checkResponse($pop3_response)) {
            // Send the Password
            $this&#45;>sendString("PASS $password" . static::LE);
            $pop3_response = $this&#45;>getResponse();
            if ($this&#45;>checkResponse($pop3_response)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Disconnect from the POP3 server.
     */
    public function disconnect()
    {
        $this&#45;>sendString(&apos;QUIT&apos;);
        //The QUIT command may cause the daemon to exit, which will kill our connection
        //So ignore errors here
        try {
            @fclose($this&#45;>pop_conn);
        } catch (Exception $e) {
            //Do nothing
        }
    }

    /**
     * Get a response from the POP3 server.
     *
     * @param int $size The maximum number of bytes to retrieve
     *
     * @return string
     */
    protected function getResponse($size = 128)
    {
        $response = fgets($this&#45;>pop_conn, $size);
        if ($this&#45;>do_debug >= 1) {
            echo &apos;Server &#45;> Client: &apos;, $response;
        }

        return $response;
    }

    /**
     * Send raw data to the POP3 server.
     *
     * @param string $string
     *
     * @return int
     */
    protected function sendString($string)
    {
        if ($this&#45;>pop_conn) {
            if ($this&#45;>do_debug >= 2) { //Show client messages when debug >= 2
                echo &apos;Client &#45;> Server: &apos;, $string;
            }

            return fwrite($this&#45;>pop_conn, $string, strlen($string));
        }

        return 0;
    }

    /**
     * Checks the POP3 server response.
     * Looks for for +OK or &#45;ERR.
     *
     * @param string $string
     *
     * @return bool
     */
    protected function checkResponse($string)
    {
        if (substr($string, 0, 3) !== &apos;+OK&apos;) {
            $this&#45;>setError("Server reported an error: $string");

            return false;
        }

        return true;
    }

    /**
     * Add an error to the internal error store.
     * Also display debug output if it&apos;s enabled.
     *
     * @param string $error
     */
    protected function setError($error)
    {
        $this&#45;>errors[] = $error;
        if ($this&#45;>do_debug >= 1) {
            echo &apos;<pre>&apos;;
            foreach ($this&#45;>errors as $e) {
                print_r($e);
            }
            echo &apos;</pre>&apos;;
        }
    }

    /**
     * Get an array of error messages, if any.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this&#45;>errors;
    }

    /**
     * POP3 connection error handler.
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     */
    protected function catchWarning($errno, $errstr, $errfile, $errline)
    {
        $this&#45;>setError(
            &apos;Connecting to the POP3 server raised a PHP warning:&apos; .
            "errno: $errno errstr: $errstr; errfile: $errfile; errline: $errline"
        );
    }
}
