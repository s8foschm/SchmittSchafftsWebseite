<?php
/**
 * PHPMailer RFC821 SMTP email transport class.
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
 * PHPMailer RFC821 SMTP email transport class.
 * Implements RFC 821 SMTP commands and provides some utility methods for sending mail to an SMTP server.
 *
 * @author  Chris Ryan
 * @author  Marcus Bointon <phpmailer@synchromedia.co.uk>
 */
class SMTP
{
    /**
     * The PHPMailer SMTP version number.
     *
     * @var string
     */
    const VERSION = &apos;6.0.7&apos;;

    /**
     * SMTP line break constant.
     *
     * @var string
     */
    const LE = "\r\n";

    /**
     * The SMTP port to use if one is not specified.
     *
     * @var int
     */
    const DEFAULT_PORT = 25;

    /**
     * The maximum line length allowed by RFC 2822 section 2.1.1.
     *
     * @var int
     */
    const MAX_LINE_LENGTH = 998;

    /**
     * Debug level for no output.
     */
    const DEBUG_OFF = 0;

    /**
     * Debug level to show client &#45;> server messages.
     */
    const DEBUG_CLIENT = 1;

    /**
     * Debug level to show client &#45;> server and server &#45;> client messages.
     */
    const DEBUG_SERVER = 2;

    /**
     * Debug level to show connection status, client &#45;> server and server &#45;> client messages.
     */
    const DEBUG_CONNECTION = 3;

    /**
     * Debug level to show all messages.
     */
    const DEBUG_LOWLEVEL = 4;

    /**
     * Debug output level.
     * Options:
     * * self::DEBUG_OFF (`0`) No debug output, default
     * * self::DEBUG_CLIENT (`1`) Client commands
     * * self::DEBUG_SERVER (`2`) Client commands and server responses
     * * self::DEBUG_CONNECTION (`3`) As DEBUG_SERVER plus connection status
     * * self::DEBUG_LOWLEVEL (`4`) Low&#45;level data output, all messages.
     *
     * @var int
     */
    public $do_debug = self::DEBUG_OFF;

    /**
     * How to handle debug output.
     * Options:
     * * `echo` Output plain&#45;text as&#45;is, appropriate for CLI
     * * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
     * * `error_log` Output to error log as configured in php.ini
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     *
     * ```php
     * $smtp&#45;>Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * ```
     *
     * Alternatively, you can pass in an instance of a PSR&#45;3 compatible logger, though only `debug`
     * level output is used:
     *
     * ```php
     * $mail&#45;>Debugoutput = new myPsr3Logger;
     * ```
     *
     * @var string|callable|\Psr\Log\LoggerInterface
     */
    public $Debugoutput = &apos;echo&apos;;

    /**
     * Whether to use VERP.
     *
     * @see http://en.wikipedia.org/wiki/Variable_envelope_return_path
     * @see http://www.postfix.org/VERP_README.html Info on VERP
     *
     * @var bool
     */
    public $do_verp = false;

    /**
     * The timeout value for connection, in seconds.
     * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2.
     * This needs to be quite high to function correctly with hosts using greetdelay as an anti&#45;spam measure.
     *
     * @see http://tools.ietf.org/html/rfc2821#section&#45;4.5.3.2
     *
     * @var int
     */
    public $Timeout = 300;

    /**
     * How long to wait for commands to complete, in seconds.
     * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2.
     *
     * @var int
     */
    public $Timelimit = 300;

    /**
     * Patterns to extract an SMTP transaction id from reply to a DATA command.
     * The first capture group in each regex will be used as the ID.
     * MS ESMTP returns the message ID, which may not be correct for internal tracking.
     *
     * @var string[]
     */
    protected $smtp_transaction_id_patterns = [
        &apos;exim&apos; => &apos;/[\d]{3} OK id=(.*)/&apos;,
        &apos;sendmail&apos; => &apos;/[\d]{3} 2.0.0 (.*) Message/&apos;,
        &apos;postfix&apos; => &apos;/[\d]{3} 2.0.0 Ok: queued as (.*)/&apos;,
        &apos;Microsoft_ESMTP&apos; => &apos;/[0&#45;9]{3} 2.[\d].0 (.*)@(?:.*) Queued mail for delivery/&apos;,
        &apos;Amazon_SES&apos; => &apos;/[\d]{3} Ok (.*)/&apos;,
        &apos;SendGrid&apos; => &apos;/[\d]{3} Ok: queued as (.*)/&apos;,
        &apos;CampaignMonitor&apos; => &apos;/[\d]{3} 2.0.0 OK:([a&#45;zA&#45;Z\d]{48})/&apos;,
    ];

    /**
     * The last transaction ID issued in response to a DATA command,
     * if one was detected.
     *
     * @var string|bool|null
     */
    protected $last_smtp_transaction_id;

    /**
     * The socket for the server connection.
     *
     * @var ?resource
     */
    protected $smtp_conn;

    /**
     * Error information, if any, for the last SMTP command.
     *
     * @var array
     */
    protected $error = [
        &apos;error&apos; => &apos;&apos;,
        &apos;detail&apos; => &apos;&apos;,
        &apos;smtp_code&apos; => &apos;&apos;,
        &apos;smtp_code_ex&apos; => &apos;&apos;,
    ];

    /**
     * The reply the server sent to us for HELO.
     * If null, no HELO string has yet been received.
     *
     * @var string|null
     */
    protected $helo_rply = null;

    /**
     * The set of SMTP extensions sent in reply to EHLO command.
     * Indexes of the array are extension names.
     * Value at index &apos;HELO&apos; or &apos;EHLO&apos; (according to command that was sent)
     * represents the server name. In case of HELO it is the only element of the array.
     * Other values can be boolean TRUE or an array containing extension options.
     * If null, no HELO/EHLO string has yet been received.
     *
     * @var array|null
     */
    protected $server_caps = null;

    /**
     * The most recent reply received from the server.
     *
     * @var string
     */
    protected $last_reply = &apos;&apos;;

    /**
     * Output debugging info via a user&#45;selected method.
     *
     * @param string $str   Debug string to output
     * @param int    $level The debug level of this message; see DEBUG_* constants
     *
     * @see SMTP::$Debugoutput
     * @see SMTP::$do_debug
     */
    protected function edebug($str, $level = 0)
    {
        if ($level > $this&#45;>do_debug) {
            return;
        }
        //Is this a PSR&#45;3 logger?
        if ($this&#45;>Debugoutput instanceof \Psr\Log\LoggerInterface) {
            $this&#45;>Debugoutput&#45;>debug($str);

            return;
        }
        //Avoid clash with built&#45;in function names
        if (!in_array($this&#45;>Debugoutput, [&apos;error_log&apos;, &apos;html&apos;, &apos;echo&apos;]) and is_callable($this&#45;>Debugoutput)) {
            call_user_func($this&#45;>Debugoutput, $str, $level);

            return;
        }
        switch ($this&#45;>Debugoutput) {
            case &apos;error_log&apos;:
                //Don&apos;t output, just log
                error_log($str);
                break;
            case &apos;html&apos;:
                //Cleans up output a bit for a better looking, HTML&#45;safe output
                echo gmdate(&apos;Y&#45;m&#45;d H:i:s&apos;), &apos; &apos;, htmlentities(
                    preg_replace(&apos;/[\r\n]+/&apos;, &apos;&apos;, $str),
                    ENT_QUOTES,
                    &apos;UTF&#45;8&apos;
                ), "<br>\n";
                break;
            case &apos;echo&apos;:
            default:
                //Normalize line breaks
                $str = preg_replace(&apos;/\r\n|\r/ms&apos;, "\n", $str);
                echo gmdate(&apos;Y&#45;m&#45;d H:i:s&apos;),
                "\t",
                    //Trim trailing space
                trim(
                //Indent for readability, except for trailing break
                    str_replace(
                        "\n",
                        "\n                   \t                  ",
                        trim($str)
                    )
                ),
                "\n";
        }
    }

    /**
     * Connect to an SMTP server.
     *
     * @param string $host    SMTP server IP or host name
     * @param int    $port    The port number to connect to
     * @param int    $timeout How long to wait for the connection to open
     * @param array  $options An array of options for stream_context_create()
     *
     * @return bool
     */
    public function connect($host, $port = null, $timeout = 30, $options = [])
    {
        static $streamok;
        //This is enabled by default since 5.0.0 but some providers disable it
        //Check this once and cache the result
        if (null === $streamok) {
            $streamok = function_exists(&apos;stream_socket_client&apos;);
        }
        // Clear errors to avoid confusion
        $this&#45;>setError(&apos;&apos;);
        // Make sure we are __not__ connected
        if ($this&#45;>connected()) {
            // Already connected, generate error
            $this&#45;>setError(&apos;Already connected to a server&apos;);

            return false;
        }
        if (empty($port)) {
            $port = self::DEFAULT_PORT;
        }
        // Connect to the SMTP server
        $this&#45;>edebug(
            "Connection: opening to $host:$port, timeout=$timeout, options=" .
            (count($options) > 0 ? var_export($options, true) : &apos;array()&apos;),
            self::DEBUG_CONNECTION
        );
        $errno = 0;
        $errstr = &apos;&apos;;
        if ($streamok) {
            $socket_context = stream_context_create($options);
            set_error_handler([$this, &apos;errorHandler&apos;]);
            $this&#45;>smtp_conn = stream_socket_client(
                $host . &apos;:&apos; . $port,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT,
                $socket_context
            );
            restore_error_handler();
        } else {
            //Fall back to fsockopen which should work in more places, but is missing some features
            $this&#45;>edebug(
                &apos;Connection: stream_socket_client not available, falling back to fsockopen&apos;,
                self::DEBUG_CONNECTION
            );
            set_error_handler([$this, &apos;errorHandler&apos;]);
            $this&#45;>smtp_conn = fsockopen(
                $host,
                $port,
                $errno,
                $errstr,
                $timeout
            );
            restore_error_handler();
        }
        // Verify we connected properly
        if (!is_resource($this&#45;>smtp_conn)) {
            $this&#45;>setError(
                &apos;Failed to connect to server&apos;,
                &apos;&apos;,
                (string) $errno,
                (string) $errstr
            );
            $this&#45;>edebug(
                &apos;SMTP ERROR: &apos; . $this&#45;>error[&apos;error&apos;]
                . ": $errstr ($errno)",
                self::DEBUG_CLIENT
            );

            return false;
        }
        $this&#45;>edebug(&apos;Connection: opened&apos;, self::DEBUG_CONNECTION);
        // SMTP server can take longer to respond, give longer timeout for first read
        // Windows does not have support for this timeout function
        if (substr(PHP_OS, 0, 3) != &apos;WIN&apos;) {
            $max = ini_get(&apos;max_execution_time&apos;);
            // Don&apos;t bother if unlimited
            if (0 != $max and $timeout > $max) {
                @set_time_limit($timeout);
            }
            stream_set_timeout($this&#45;>smtp_conn, $timeout, 0);
        }
        // Get any announcement
        $announce = $this&#45;>get_lines();
        $this&#45;>edebug(&apos;SERVER &#45;> CLIENT: &apos; . $announce, self::DEBUG_SERVER);

        return true;
    }

    /**
     * Initiate a TLS (encrypted) session.
     *
     * @return bool
     */
    public function startTLS()
    {
        if (!$this&#45;>sendCommand(&apos;STARTTLS&apos;, &apos;STARTTLS&apos;, 220)) {
            return false;
        }

        //Allow the best TLS version(s) we can
        $crypto_method = STREAM_CRYPTO_METHOD_TLS_CLIENT;

        //PHP 5.6.7 dropped inclusion of TLS 1.1 and 1.2 in STREAM_CRYPTO_METHOD_TLS_CLIENT
        //so add them back in manually if we can
        if (defined(&apos;STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT&apos;)) {
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT;
        }

        // Begin encrypted connection
        set_error_handler([$this, &apos;errorHandler&apos;]);
        $crypto_ok = stream_socket_enable_crypto(
            $this&#45;>smtp_conn,
            true,
            $crypto_method
        );
        restore_error_handler();

        return (bool) $crypto_ok;
    }

    /**
     * Perform SMTP authentication.
     * Must be run after hello().
     *
     * @see    hello()
     *
     * @param string $username The user name
     * @param string $password The password
     * @param string $authtype The auth type (CRAM&#45;MD5, PLAIN, LOGIN, XOAUTH2)
     * @param OAuth  $OAuth    An optional OAuth instance for XOAUTH2 authentication
     *
     * @return bool True if successfully authenticated
     */
    public function authenticate(
        $username,
        $password,
        $authtype = null,
        $OAuth = null
    ) {
        if (!$this&#45;>server_caps) {
            $this&#45;>setError(&apos;Authentication is not allowed before HELO/EHLO&apos;);

            return false;
        }

        if (array_key_exists(&apos;EHLO&apos;, $this&#45;>server_caps)) {
            // SMTP extensions are available; try to find a proper authentication method
            if (!array_key_exists(&apos;AUTH&apos;, $this&#45;>server_caps)) {
                $this&#45;>setError(&apos;Authentication is not allowed at this stage&apos;);
                // &apos;at this stage&apos; means that auth may be allowed after the stage changes
                // e.g. after STARTTLS

                return false;
            }

            $this&#45;>edebug(&apos;Auth method requested: &apos; . ($authtype ? $authtype : &apos;UNSPECIFIED&apos;), self::DEBUG_LOWLEVEL);
            $this&#45;>edebug(
                &apos;Auth methods available on the server: &apos; . implode(&apos;,&apos;, $this&#45;>server_caps[&apos;AUTH&apos;]),
                self::DEBUG_LOWLEVEL
            );

            //If we have requested a specific auth type, check the server supports it before trying others
            if (null !== $authtype and !in_array($authtype, $this&#45;>server_caps[&apos;AUTH&apos;])) {
                $this&#45;>edebug(&apos;Requested auth method not available: &apos; . $authtype, self::DEBUG_LOWLEVEL);
                $authtype = null;
            }

            if (empty($authtype)) {
                //If no auth mechanism is specified, attempt to use these, in this order
                //Try CRAM&#45;MD5 first as it&apos;s more secure than the others
                foreach ([&apos;CRAM&#45;MD5&apos;, &apos;LOGIN&apos;, &apos;PLAIN&apos;, &apos;XOAUTH2&apos;] as $method) {
                    if (in_array($method, $this&#45;>server_caps[&apos;AUTH&apos;])) {
                        $authtype = $method;
                        break;
                    }
                }
                if (empty($authtype)) {
                    $this&#45;>setError(&apos;No supported authentication methods found&apos;);

                    return false;
                }
                self::edebug(&apos;Auth method selected: &apos; . $authtype, self::DEBUG_LOWLEVEL);
            }

            if (!in_array($authtype, $this&#45;>server_caps[&apos;AUTH&apos;])) {
                $this&#45;>setError("The requested authentication method \"$authtype\" is not supported by the server");

                return false;
            }
        } elseif (empty($authtype)) {
            $authtype = &apos;LOGIN&apos;;
        }
        switch ($authtype) {
            case &apos;PLAIN&apos;:
                // Start authentication
                if (!$this&#45;>sendCommand(&apos;AUTH&apos;, &apos;AUTH PLAIN&apos;, 334)) {
                    return false;
                }
                // Send encoded username and password
                if (!$this&#45;>sendCommand(
                    &apos;User & Password&apos;,
                    base64_encode("\0" . $username . "\0" . $password),
                    235
                )
                ) {
                    return false;
                }
                break;
            case &apos;LOGIN&apos;:
                // Start authentication
                if (!$this&#45;>sendCommand(&apos;AUTH&apos;, &apos;AUTH LOGIN&apos;, 334)) {
                    return false;
                }
                if (!$this&#45;>sendCommand(&apos;Username&apos;, base64_encode($username), 334)) {
                    return false;
                }
                if (!$this&#45;>sendCommand(&apos;Password&apos;, base64_encode($password), 235)) {
                    return false;
                }
                break;
            case &apos;CRAM&#45;MD5&apos;:
                // Start authentication
                if (!$this&#45;>sendCommand(&apos;AUTH CRAM&#45;MD5&apos;, &apos;AUTH CRAM&#45;MD5&apos;, 334)) {
                    return false;
                }
                // Get the challenge
                $challenge = base64_decode(substr($this&#45;>last_reply, 4));

                // Build the response
                $response = $username . &apos; &apos; . $this&#45;>hmac($challenge, $password);

                // send encoded credentials
                return $this&#45;>sendCommand(&apos;Username&apos;, base64_encode($response), 235);
            case &apos;XOAUTH2&apos;:
                //The OAuth instance must be set up prior to requesting auth.
                if (null === $OAuth) {
                    return false;
                }
                $oauth = $OAuth&#45;>getOauth64();

                // Start authentication
                if (!$this&#45;>sendCommand(&apos;AUTH&apos;, &apos;AUTH XOAUTH2 &apos; . $oauth, 235)) {
                    return false;
                }
                break;
            default:
                $this&#45;>setError("Authentication method \"$authtype\" is not supported");

                return false;
        }

        return true;
    }

    /**
     * Calculate an MD5 HMAC hash.
     * Works like hash_hmac(&apos;md5&apos;, $data, $key)
     * in case that function is not available.
     *
     * @param string $data The data to hash
     * @param string $key  The key to hash with
     *
     * @return string
     */
    protected function hmac($data, $key)
    {
        if (function_exists(&apos;hash_hmac&apos;)) {
            return hash_hmac(&apos;md5&apos;, $data, $key);
        }

        // The following borrowed from
        // http://php.net/manual/en/function.mhash.php#27225

        // RFC 2104 HMAC implementation for php.
        // Creates an md5 HMAC.
        // Eliminates the need to install mhash to compute a HMAC
        // by Lance Rushing

        $bytelen = 64; // byte length for md5
        if (strlen($key) > $bytelen) {
            $key = pack(&apos;H*&apos;, md5($key));
        }
        $key = str_pad($key, $bytelen, chr(0x00));
        $ipad = str_pad(&apos;&apos;, $bytelen, chr(0x36));
        $opad = str_pad(&apos;&apos;, $bytelen, chr(0x5c));
        $k_ipad = $key ^ $ipad;
        $k_opad = $key ^ $opad;

        return md5($k_opad . pack(&apos;H*&apos;, md5($k_ipad . $data)));
    }

    /**
     * Check connection state.
     *
     * @return bool True if connected
     */
    public function connected()
    {
        if (is_resource($this&#45;>smtp_conn)) {
            $sock_status = stream_get_meta_data($this&#45;>smtp_conn);
            if ($sock_status[&apos;eof&apos;]) {
                // The socket is valid but we are not connected
                $this&#45;>edebug(
                    &apos;SMTP NOTICE: EOF caught while checking if connected&apos;,
                    self::DEBUG_CLIENT
                );
                $this&#45;>close();

                return false;
            }

            return true; // everything looks good
        }

        return false;
    }

    /**
     * Close the socket and clean up the state of the class.
     * Don&apos;t use this function without first trying to use QUIT.
     *
     * @see quit()
     */
    public function close()
    {
        $this&#45;>setError(&apos;&apos;);
        $this&#45;>server_caps = null;
        $this&#45;>helo_rply = null;
        if (is_resource($this&#45;>smtp_conn)) {
            // close the connection and cleanup
            fclose($this&#45;>smtp_conn);
            $this&#45;>smtp_conn = null; //Makes for cleaner serialization
            $this&#45;>edebug(&apos;Connection: closed&apos;, self::DEBUG_CONNECTION);
        }
    }

    /**
     * Send an SMTP DATA command.
     * Issues a data command and sends the msg_data to the server,
     * finializing the mail transaction. $msg_data is the message
     * that is to be send with the headers. Each header needs to be
     * on a single line followed by a <CRLF> with the message headers
     * and the message body being separated by an additional <CRLF>.
     * Implements RFC 821: DATA <CRLF>.
     *
     * @param string $msg_data Message data to send
     *
     * @return bool
     */
    public function data($msg_data)
    {
        //This will use the standard timelimit
        if (!$this&#45;>sendCommand(&apos;DATA&apos;, &apos;DATA&apos;, 354)) {
            return false;
        }

        /* The server is ready to accept data!
         * According to rfc821 we should not send more than 1000 characters on a single line (including the LE)
         * so we will break the data up into lines by \r and/or \n then if needed we will break each of those into
         * smaller lines to fit within the limit.
         * We will also look for lines that start with a &apos;.&apos; and prepend an additional &apos;.&apos;.
         * NOTE: this does not count towards line&#45;length limit.
         */

        // Normalize line breaks before exploding
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $msg_data));

        /* To distinguish between a complete RFC822 message and a plain message body, we check if the first field
         * of the first line (&apos;:&apos; separated) does not contain a space then it _should_ be a header and we will
         * process all lines before a blank line as headers.
         */

        $field = substr($lines[0], 0, strpos($lines[0], &apos;:&apos;));
        $in_headers = false;
        if (!empty($field) and strpos($field, &apos; &apos;) === false) {
            $in_headers = true;
        }

        foreach ($lines as $line) {
            $lines_out = [];
            if ($in_headers and $line == &apos;&apos;) {
                $in_headers = false;
            }
            //Break this line up into several smaller lines if it&apos;s too long
            //Micro&#45;optimisation: isset($str[$len]) is faster than (strlen($str) > $len),
            while (isset($line[self::MAX_LINE_LENGTH])) {
                //Working backwards, try to find a space within the last MAX_LINE_LENGTH chars of the line to break on
                //so as to avoid breaking in the middle of a word
                $pos = strrpos(substr($line, 0, self::MAX_LINE_LENGTH), &apos; &apos;);
                //Deliberately matches both false and 0
                if (!$pos) {
                    //No nice break found, add a hard break
                    $pos = self::MAX_LINE_LENGTH &#45; 1;
                    $lines_out[] = substr($line, 0, $pos);
                    $line = substr($line, $pos);
                } else {
                    //Break at the found point
                    $lines_out[] = substr($line, 0, $pos);
                    //Move along by the amount we dealt with
                    $line = substr($line, $pos + 1);
                }
                //If processing headers add a LWSP&#45;char to the front of new line RFC822 section 3.1.1
                if ($in_headers) {
                    $line = "\t" . $line;
                }
            }
            $lines_out[] = $line;

            //Send the lines to the server
            foreach ($lines_out as $line_out) {
                //RFC2821 section 4.5.2
                if (!empty($line_out) and $line_out[0] == &apos;.&apos;) {
                    $line_out = &apos;.&apos; . $line_out;
                }
                $this&#45;>client_send($line_out . static::LE, &apos;DATA&apos;);
            }
        }

        //Message data has been sent, complete the command
        //Increase timelimit for end of DATA command
        $savetimelimit = $this&#45;>Timelimit;
        $this&#45;>Timelimit = $this&#45;>Timelimit * 2;
        $result = $this&#45;>sendCommand(&apos;DATA END&apos;, &apos;.&apos;, 250);
        $this&#45;>recordLastTransactionID();
        //Restore timelimit
        $this&#45;>Timelimit = $savetimelimit;

        return $result;
    }

    /**
     * Send an SMTP HELO or EHLO command.
     * Used to identify the sending server to the receiving server.
     * This makes sure that client and server are in a known state.
     * Implements RFC 821: HELO <SP> <domain> <CRLF>
     * and RFC 2821 EHLO.
     *
     * @param string $host The host name or IP to connect to
     *
     * @return bool
     */
    public function hello($host = &apos;&apos;)
    {
        //Try extended hello first (RFC 2821)
        return $this&#45;>sendHello(&apos;EHLO&apos;, $host) or $this&#45;>sendHello(&apos;HELO&apos;, $host);
    }

    /**
     * Send an SMTP HELO or EHLO command.
     * Low&#45;level implementation used by hello().
     *
     * @param string $hello The HELO string
     * @param string $host  The hostname to say we are
     *
     * @return bool
     *
     * @see    hello()
     */
    protected function sendHello($hello, $host)
    {
        $noerror = $this&#45;>sendCommand($hello, $hello . &apos; &apos; . $host, 250);
        $this&#45;>helo_rply = $this&#45;>last_reply;
        if ($noerror) {
            $this&#45;>parseHelloFields($hello);
        } else {
            $this&#45;>server_caps = null;
        }

        return $noerror;
    }

    /**
     * Parse a reply to HELO/EHLO command to discover server extensions.
     * In case of HELO, the only parameter that can be discovered is a server name.
     *
     * @param string $type `HELO` or `EHLO`
     */
    protected function parseHelloFields($type)
    {
        $this&#45;>server_caps = [];
        $lines = explode("\n", $this&#45;>helo_rply);

        foreach ($lines as $n => $s) {
            //First 4 chars contain response code followed by &#45; or space
            $s = trim(substr($s, 4));
            if (empty($s)) {
                continue;
            }
            $fields = explode(&apos; &apos;, $s);
            if (!empty($fields)) {
                if (!$n) {
                    $name = $type;
                    $fields = $fields[0];
                } else {
                    $name = array_shift($fields);
                    switch ($name) {
                        case &apos;SIZE&apos;:
                            $fields = ($fields ? $fields[0] : 0);
                            break;
                        case &apos;AUTH&apos;:
                            if (!is_array($fields)) {
                                $fields = [];
                            }
                            break;
                        default:
                            $fields = true;
                    }
                }
                $this&#45;>server_caps[$name] = $fields;
            }
        }
    }

    /**
     * Send an SMTP MAIL command.
     * Starts a mail transaction from the email address specified in
     * $from. Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more recipient
     * commands may be called followed by a data command.
     * Implements RFC 821: MAIL <SP> FROM:<reverse&#45;path> <CRLF>.
     *
     * @param string $from Source address of this message
     *
     * @return bool
     */
    public function mail($from)
    {
        $useVerp = ($this&#45;>do_verp ? &apos; XVERP&apos; : &apos;&apos;);

        return $this&#45;>sendCommand(
            &apos;MAIL FROM&apos;,
            &apos;MAIL FROM:<&apos; . $from . &apos;>&apos; . $useVerp,
            250
        );
    }

    /**
     * Send an SMTP QUIT command.
     * Closes the socket if there is no error or the $close_on_error argument is true.
     * Implements from RFC 821: QUIT <CRLF>.
     *
     * @param bool $close_on_error Should the connection close if an error occurs?
     *
     * @return bool
     */
    public function quit($close_on_error = true)
    {
        $noerror = $this&#45;>sendCommand(&apos;QUIT&apos;, &apos;QUIT&apos;, 221);
        $err = $this&#45;>error; //Save any error
        if ($noerror or $close_on_error) {
            $this&#45;>close();
            $this&#45;>error = $err; //Restore any error from the quit command
        }

        return $noerror;
    }

    /**
     * Send an SMTP RCPT command.
     * Sets the TO argument to $toaddr.
     * Returns true if the recipient was accepted false if it was rejected.
     * Implements from RFC 821: RCPT <SP> TO:<forward&#45;path> <CRLF>.
     *
     * @param string $address The address the message is being sent to
     *
     * @return bool
     */
    public function recipient($address)
    {
        return $this&#45;>sendCommand(
            &apos;RCPT TO&apos;,
            &apos;RCPT TO:<&apos; . $address . &apos;>&apos;,
            [250, 251]
        );
    }

    /**
     * Send an SMTP RSET command.
     * Abort any transaction that is currently in progress.
     * Implements RFC 821: RSET <CRLF>.
     *
     * @return bool True on success
     */
    public function reset()
    {
        return $this&#45;>sendCommand(&apos;RSET&apos;, &apos;RSET&apos;, 250);
    }

    /**
     * Send a command to an SMTP server and check its return code.
     *
     * @param string    $command       The command name &#45; not sent to the server
     * @param string    $commandstring The actual command to send
     * @param int|array $expect        One or more expected integer success codes
     *
     * @return bool True on success
     */
    protected function sendCommand($command, $commandstring, $expect)
    {
        if (!$this&#45;>connected()) {
            $this&#45;>setError("Called $command without being connected");

            return false;
        }
        //Reject line breaks in all commands
        if (strpos($commandstring, "\n") !== false or strpos($commandstring, "\r") !== false) {
            $this&#45;>setError("Command &apos;$command&apos; contained line breaks");

            return false;
        }
        $this&#45;>client_send($commandstring . static::LE, $command);

        $this&#45;>last_reply = $this&#45;>get_lines();
        // Fetch SMTP code and possible error code explanation
        $matches = [];
        if (preg_match(&apos;/^([0&#45;9]{3})[ &#45;](?:([0&#45;9]\\.[0&#45;9]\\.[0&#45;9]) )?/&apos;, $this&#45;>last_reply, $matches)) {
            $code = $matches[1];
            $code_ex = (count($matches) > 2 ? $matches[2] : null);
            // Cut off error code from each response line
            $detail = preg_replace(
                "/{$code}[ &#45;]" .
                ($code_ex ? str_replace(&apos;.&apos;, &apos;\\.&apos;, $code_ex) . &apos; &apos; : &apos;&apos;) . &apos;/m&apos;,
                &apos;&apos;,
                $this&#45;>last_reply
            );
        } else {
            // Fall back to simple parsing if regex fails
            $code = substr($this&#45;>last_reply, 0, 3);
            $code_ex = null;
            $detail = substr($this&#45;>last_reply, 4);
        }

        $this&#45;>edebug(&apos;SERVER &#45;> CLIENT: &apos; . $this&#45;>last_reply, self::DEBUG_SERVER);

        if (!in_array($code, (array) $expect)) {
            $this&#45;>setError(
                "$command command failed",
                $detail,
                $code,
                $code_ex
            );
            $this&#45;>edebug(
                &apos;SMTP ERROR: &apos; . $this&#45;>error[&apos;error&apos;] . &apos;: &apos; . $this&#45;>last_reply,
                self::DEBUG_CLIENT
            );

            return false;
        }

        $this&#45;>setError(&apos;&apos;);

        return true;
    }

    /**
     * Send an SMTP SAML command.
     * Starts a mail transaction from the email address specified in $from.
     * Returns true if successful or false otherwise. If True
     * the mail transaction is started and then one or more recipient
     * commands may be called followed by a data command. This command
     * will send the message to the users terminal if they are logged
     * in and send them an email.
     * Implements RFC 821: SAML <SP> FROM:<reverse&#45;path> <CRLF>.
     *
     * @param string $from The address the message is from
     *
     * @return bool
     */
    public function sendAndMail($from)
    {
        return $this&#45;>sendCommand(&apos;SAML&apos;, "SAML FROM:$from", 250);
    }

    /**
     * Send an SMTP VRFY command.
     *
     * @param string $name The name to verify
     *
     * @return bool
     */
    public function verify($name)
    {
        return $this&#45;>sendCommand(&apos;VRFY&apos;, "VRFY $name", [250, 251]);
    }

    /**
     * Send an SMTP NOOP command.
     * Used to keep keep&#45;alives alive, doesn&apos;t actually do anything.
     *
     * @return bool
     */
    public function noop()
    {
        return $this&#45;>sendCommand(&apos;NOOP&apos;, &apos;NOOP&apos;, 250);
    }

    /**
     * Send an SMTP TURN command.
     * This is an optional command for SMTP that this class does not support.
     * This method is here to make the RFC821 Definition complete for this class
     * and _may_ be implemented in future.
     * Implements from RFC 821: TURN <CRLF>.
     *
     * @return bool
     */
    public function turn()
    {
        $this&#45;>setError(&apos;The SMTP TURN command is not implemented&apos;);
        $this&#45;>edebug(&apos;SMTP NOTICE: &apos; . $this&#45;>error[&apos;error&apos;], self::DEBUG_CLIENT);

        return false;
    }

    /**
     * Send raw data to the server.
     *
     * @param string $data    The data to send
     * @param string $command Optionally, the command this is part of, used only for controlling debug output
     *
     * @return int|bool The number of bytes sent to the server or false on error
     */
    public function client_send($data, $command = &apos;&apos;)
    {
        //If SMTP transcripts are left enabled, or debug output is posted online
        //it can leak credentials, so hide credentials in all but lowest level
        if (self::DEBUG_LOWLEVEL > $this&#45;>do_debug and
            in_array($command, [&apos;User & Password&apos;, &apos;Username&apos;, &apos;Password&apos;], true)) {
            $this&#45;>edebug(&apos;CLIENT &#45;> SERVER: <credentials hidden>&apos;, self::DEBUG_CLIENT);
        } else {
            $this&#45;>edebug(&apos;CLIENT &#45;> SERVER: &apos; . $data, self::DEBUG_CLIENT);
        }
        set_error_handler([$this, &apos;errorHandler&apos;]);
        $result = fwrite($this&#45;>smtp_conn, $data);
        restore_error_handler();

        return $result;
    }

    /**
     * Get the latest error.
     *
     * @return array
     */
    public function getError()
    {
        return $this&#45;>error;
    }

    /**
     * Get SMTP extensions available on the server.
     *
     * @return array|null
     */
    public function getServerExtList()
    {
        return $this&#45;>server_caps;
    }

    /**
     * Get metadata about the SMTP server from its HELO/EHLO response.
     * The method works in three ways, dependent on argument value and current state:
     *   1. HELO/EHLO has not been sent &#45; returns null and populates $this&#45;>error.
     *   2. HELO has been sent &#45;
     *     $name == &apos;HELO&apos;: returns server name
     *     $name == &apos;EHLO&apos;: returns boolean false
     *     $name == any other string: returns null and populates $this&#45;>error
     *   3. EHLO has been sent &#45;
     *     $name == &apos;HELO&apos;|&apos;EHLO&apos;: returns the server name
     *     $name == any other string: if extension $name exists, returns True
     *       or its options (e.g. AUTH mechanisms supported). Otherwise returns False.
     *
     * @param string $name Name of SMTP extension or &apos;HELO&apos;|&apos;EHLO&apos;
     *
     * @return mixed
     */
    public function getServerExt($name)
    {
        if (!$this&#45;>server_caps) {
            $this&#45;>setError(&apos;No HELO/EHLO was sent&apos;);

            return;
        }

        if (!array_key_exists($name, $this&#45;>server_caps)) {
            if (&apos;HELO&apos; == $name) {
                return $this&#45;>server_caps[&apos;EHLO&apos;];
            }
            if (&apos;EHLO&apos; == $name || array_key_exists(&apos;EHLO&apos;, $this&#45;>server_caps)) {
                return false;
            }
            $this&#45;>setError(&apos;HELO handshake was used; No information about server extensions available&apos;);

            return;
        }

        return $this&#45;>server_caps[$name];
    }

    /**
     * Get the last reply from the server.
     *
     * @return string
     */
    public function getLastReply()
    {
        return $this&#45;>last_reply;
    }

    /**
     * Read the SMTP server&apos;s response.
     * Either before eof or socket timeout occurs on the operation.
     * With SMTP we can tell if we have more lines to read if the
     * 4th character is &apos;&#45;&apos; symbol. If it is a space then we don&apos;t
     * need to read anything else.
     *
     * @return string
     */
    protected function get_lines()
    {
        // If the connection is bad, give up straight away
        if (!is_resource($this&#45;>smtp_conn)) {
            return &apos;&apos;;
        }
        $data = &apos;&apos;;
        $endtime = 0;
        stream_set_timeout($this&#45;>smtp_conn, $this&#45;>Timeout);
        if ($this&#45;>Timelimit > 0) {
            $endtime = time() + $this&#45;>Timelimit;
        }
        $selR = [$this&#45;>smtp_conn];
        $selW = null;
        while (is_resource($this&#45;>smtp_conn) and !feof($this&#45;>smtp_conn)) {
            //Must pass vars in here as params are by reference
            if (!stream_select($selR, $selW, $selW, $this&#45;>Timelimit)) {
                $this&#45;>edebug(
                    &apos;SMTP &#45;> get_lines(): timed&#45;out (&apos; . $this&#45;>Timeout . &apos; sec)&apos;,
                    self::DEBUG_LOWLEVEL
                );
                break;
            }
            //Deliberate noise suppression &#45; errors are handled afterwards
            $str = @fgets($this&#45;>smtp_conn, 515);
            $this&#45;>edebug(&apos;SMTP INBOUND: "&apos; . trim($str) . &apos;"&apos;, self::DEBUG_LOWLEVEL);
            $data .= $str;
            // If response is only 3 chars (not valid, but RFC5321 S4.2 says it must be handled),
            // or 4th character is a space, we are done reading, break the loop,
            // string array access is a micro&#45;optimisation over strlen
            if (!isset($str[3]) or (isset($str[3]) and $str[3] == &apos; &apos;)) {
                break;
            }
            // Timed&#45;out? Log and break
            $info = stream_get_meta_data($this&#45;>smtp_conn);
            if ($info[&apos;timed_out&apos;]) {
                $this&#45;>edebug(
                    &apos;SMTP &#45;> get_lines(): timed&#45;out (&apos; . $this&#45;>Timeout . &apos; sec)&apos;,
                    self::DEBUG_LOWLEVEL
                );
                break;
            }
            // Now check if reads took too long
            if ($endtime and time() > $endtime) {
                $this&#45;>edebug(
                    &apos;SMTP &#45;> get_lines(): timelimit reached (&apos; .
                    $this&#45;>Timelimit . &apos; sec)&apos;,
                    self::DEBUG_LOWLEVEL
                );
                break;
            }
        }

        return $data;
    }

    /**
     * Enable or disable VERP address generation.
     *
     * @param bool $enabled
     */
    public function setVerp($enabled = false)
    {
        $this&#45;>do_verp = $enabled;
    }

    /**
     * Get VERP address generation mode.
     *
     * @return bool
     */
    public function getVerp()
    {
        return $this&#45;>do_verp;
    }

    /**
     * Set error messages and codes.
     *
     * @param string $message      The error message
     * @param string $detail       Further detail on the error
     * @param string $smtp_code    An associated SMTP error code
     * @param string $smtp_code_ex Extended SMTP code
     */
    protected function setError($message, $detail = &apos;&apos;, $smtp_code = &apos;&apos;, $smtp_code_ex = &apos;&apos;)
    {
        $this&#45;>error = [
            &apos;error&apos; => $message,
            &apos;detail&apos; => $detail,
            &apos;smtp_code&apos; => $smtp_code,
            &apos;smtp_code_ex&apos; => $smtp_code_ex,
        ];
    }

    /**
     * Set debug output method.
     *
     * @param string|callable $method The name of the mechanism to use for debugging output, or a callable to handle it
     */
    public function setDebugOutput($method = &apos;echo&apos;)
    {
        $this&#45;>Debugoutput = $method;
    }

    /**
     * Get debug output method.
     *
     * @return string
     */
    public function getDebugOutput()
    {
        return $this&#45;>Debugoutput;
    }

    /**
     * Set debug output level.
     *
     * @param int $level
     */
    public function setDebugLevel($level = 0)
    {
        $this&#45;>do_debug = $level;
    }

    /**
     * Get debug output level.
     *
     * @return int
     */
    public function getDebugLevel()
    {
        return $this&#45;>do_debug;
    }

    /**
     * Set SMTP timeout.
     *
     * @param int $timeout The timeout duration in seconds
     */
    public function setTimeout($timeout = 0)
    {
        $this&#45;>Timeout = $timeout;
    }

    /**
     * Get SMTP timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this&#45;>Timeout;
    }

    /**
     * Reports an error number and string.
     *
     * @param int    $errno   The error number returned by PHP
     * @param string $errmsg  The error message returned by PHP
     * @param string $errfile The file the error occurred in
     * @param int    $errline The line number the error occurred on
     */
    protected function errorHandler($errno, $errmsg, $errfile = &apos;&apos;, $errline = 0)
    {
        $notice = &apos;Connection failed.&apos;;
        $this&#45;>setError(
            $notice,
            $errmsg,
            (string) $errno
        );
        $this&#45;>edebug(
            "$notice Error #$errno: $errmsg [$errfile line $errline]",
            self::DEBUG_CONNECTION
        );
    }

    /**
     * Extract and return the ID of the last SMTP transaction based on
     * a list of patterns provided in SMTP::$smtp_transaction_id_patterns.
     * Relies on the host providing the ID in response to a DATA command.
     * If no reply has been received yet, it will return null.
     * If no pattern was matched, it will return false.
     *
     * @return bool|null|string
     */
    protected function recordLastTransactionID()
    {
        $reply = $this&#45;>getLastReply();

        if (empty($reply)) {
            $this&#45;>last_smtp_transaction_id = null;
        } else {
            $this&#45;>last_smtp_transaction_id = false;
            foreach ($this&#45;>smtp_transaction_id_patterns as $smtp_transaction_id_pattern) {
                if (preg_match($smtp_transaction_id_pattern, $reply, $matches)) {
                    $this&#45;>last_smtp_transaction_id = trim($matches[1]);
                    break;
                }
            }
        }

        return $this&#45;>last_smtp_transaction_id;
    }

    /**
     * Get the queue/transaction ID of the last SMTP transaction
     * If no reply has been received yet, it will return null.
     * If no pattern was matched, it will return false.
     *
     * @return bool|null|string
     *
     * @see recordLastTransactionID()
     */
    public function getLastTransactionID()
    {
        return $this&#45;>last_smtp_transaction_id;
    }
}
