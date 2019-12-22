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
 * PHPMailer &#45; PHP email creation and transport class.
 *
 * @author  Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author  Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author  Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author  Brent R. Matzelle (original founder)
 */
class PHPMailer
{
    const CHARSET_ISO88591 = &apos;iso&#45;8859&#45;1&apos;;
    const CHARSET_UTF8 = &apos;utf&#45;8&apos;;

    const CONTENT_TYPE_PLAINTEXT = &apos;text/plain&apos;;
    const CONTENT_TYPE_TEXT_CALENDAR = &apos;text/calendar&apos;;
    const CONTENT_TYPE_TEXT_HTML = &apos;text/html&apos;;
    const CONTENT_TYPE_MULTIPART_ALTERNATIVE = &apos;multipart/alternative&apos;;
    const CONTENT_TYPE_MULTIPART_MIXED = &apos;multipart/mixed&apos;;
    const CONTENT_TYPE_MULTIPART_RELATED = &apos;multipart/related&apos;;

    const ENCODING_7BIT = &apos;7bit&apos;;
    const ENCODING_8BIT = &apos;8bit&apos;;
    const ENCODING_BASE64 = &apos;base64&apos;;
    const ENCODING_BINARY = &apos;binary&apos;;
    const ENCODING_QUOTED_PRINTABLE = &apos;quoted&#45;printable&apos;;

    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = low.
     * When null, the header is not set at all.
     *
     * @var int
     */
    public $Priority;

    /**
     * The character set of the message.
     *
     * @var string
     */
    public $CharSet = self::CHARSET_ISO88591;

    /**
     * The MIME Content&#45;type of the message.
     *
     * @var string
     */
    public $ContentType = self::CONTENT_TYPE_PLAINTEXT;

    /**
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted&#45;printable".
     *
     * @var string
     */
    public $Encoding = self::ENCODING_8BIT;

    /**
     * Holds the most recent mailer error message.
     *
     * @var string
     */
    public $ErrorInfo = &apos;&apos;;

    /**
     * The From email address for the message.
     *
     * @var string
     */
    public $From = &apos;root@localhost&apos;;

    /**
     * The From name of the message.
     *
     * @var string
     */
    public $FromName = &apos;Root User&apos;;

    /**
     * The envelope sender of the message.
     * This will usually be turned into a Return&#45;Path header by the receiver,
     * and is the address that bounces will be sent to.
     * If not empty, will be passed via `&#45;f` to sendmail or as the &apos;MAIL FROM&apos; value over SMTP.
     *
     * @var string
     */
    public $Sender = &apos;&apos;;

    /**
     * The Subject of the message.
     *
     * @var string
     */
    public $Subject = &apos;&apos;;

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true).
     *
     * @var string
     */
    public $Body = &apos;&apos;;

    /**
     * The plain&#45;text message body.
     * This body can be read by mail clients that do not have HTML email
     * capability such as mutt & Eudora.
     * Clients that can read HTML will view the normal Body.
     *
     * @var string
     */
    public $AltBody = &apos;&apos;;

    /**
     * An iCal message part body.
     * Only supported in simple alt or alt_inline message types
     * To generate iCal event structures, use classes like EasyPeasyICS or iCalcreator.
     *
     * @see http://sprain.ch/blog/downloads/php&#45;class&#45;easypeasyics&#45;create&#45;ical&#45;files&#45;with&#45;php/
     * @see http://kigkonsult.se/iCalcreator/
     *
     * @var string
     */
    public $Ical = &apos;&apos;;

    /**
     * The complete compiled MIME message body.
     *
     * @var string
     */
    protected $MIMEBody = &apos;&apos;;

    /**
     * The complete compiled MIME message headers.
     *
     * @var string
     */
    protected $MIMEHeader = &apos;&apos;;

    /**
     * Extra headers that createHeader() doesn&apos;t fold in.
     *
     * @var string
     */
    protected $mailHeader = &apos;&apos;;

    /**
     * Word&#45;wrap the message body to this number of chars.
     * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
     *
     * @see static::STD_LINE_LENGTH
     *
     * @var int
     */
    public $WordWrap = 0;

    /**
     * Which method to use to send mail.
     * Options: "mail", "sendmail", or "smtp".
     *
     * @var string
     */
    public $Mailer = &apos;mail&apos;;

    /**
     * The path to the sendmail program.
     *
     * @var string
     */
    public $Sendmail = &apos;/usr/sbin/sendmail&apos;;

    /**
     * Whether mail() uses a fully sendmail&#45;compatible MTA.
     * One which supports sendmail&apos;s "&#45;oi &#45;f" options.
     *
     * @var bool
     */
    public $UseSendmailOptions = true;

    /**
     * The email address that a reading confirmation should be sent to, also known as read receipt.
     *
     * @var string
     */
    public $ConfirmReadingTo = &apos;&apos;;

    /**
     * The hostname to use in the Message&#45;ID header and as default HELO string.
     * If empty, PHPMailer attempts to find one with, in order,
     * $_SERVER[&apos;SERVER_NAME&apos;], gethostname(), php_uname(&apos;n&apos;), or the value
     * &apos;localhost.localdomain&apos;.
     *
     * @var string
     */
    public $Hostname = &apos;&apos;;

    /**
     * An ID to be used in the Message&#45;ID header.
     * If empty, a unique id will be generated.
     * You can set your own, but it must be in the format "<id@domain>",
     * as defined in RFC5322 section 3.6.4 or it will be ignored.
     *
     * @see https://tools.ietf.org/html/rfc5322#section&#45;3.6.4
     *
     * @var string
     */
    public $MessageID = &apos;&apos;;

    /**
     * The message Date to be used in the Date header.
     * If empty, the current date will be added.
     *
     * @var string
     */
    public $MessageDate = &apos;&apos;;

    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon&#45;delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     *
     * @var string
     */
    public $Host = &apos;localhost&apos;;

    /**
     * The default SMTP server port.
     *
     * @var int
     */
    public $Port = 25;

    /**
     * The SMTP HELO of the message.
     * Default is $Hostname. If $Hostname is empty, PHPMailer attempts to find
     * one with the same method described above for $Hostname.
     *
     * @see PHPMailer::$Hostname
     *
     * @var string
     */
    public $Helo = &apos;&apos;;

    /**
     * What kind of encryption to use on the SMTP connection.
     * Options: &apos;&apos;, &apos;ssl&apos; or &apos;tls&apos;.
     *
     * @var string
     */
    public $SMTPSecure = &apos;&apos;;

    /**
     * Whether to enable TLS encryption automatically if a server supports it,
     * even if `SMTPSecure` is not set to &apos;tls&apos;.
     * Be aware that in PHP >= 5.6 this requires that the server&apos;s certificates are valid.
     *
     * @var bool
     */
    public $SMTPAutoTLS = true;

    /**
     * Whether to use SMTP authentication.
     * Uses the Username and Password properties.
     *
     * @see PHPMailer::$Username
     * @see PHPMailer::$Password
     *
     * @var bool
     */
    public $SMTPAuth = false;

    /**
     * Options array passed to stream_context_create when connecting via SMTP.
     *
     * @var array
     */
    public $SMTPOptions = [];

    /**
     * SMTP username.
     *
     * @var string
     */
    public $Username = &apos;&apos;;

    /**
     * SMTP password.
     *
     * @var string
     */
    public $Password = &apos;&apos;;

    /**
     * SMTP auth type.
     * Options are CRAM&#45;MD5, LOGIN, PLAIN, XOAUTH2, attempted in that order if not specified.
     *
     * @var string
     */
    public $AuthType = &apos;&apos;;

    /**
     * An instance of the PHPMailer OAuth class.
     *
     * @var OAuth
     */
    protected $oauth;

    /**
     * The SMTP server timeout in seconds.
     * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2.
     *
     * @var int
     */
    public $Timeout = 300;

    /**
     * SMTP class debug output mode.
     * Debug output level.
     * Options:
     * * `0` No output
     * * `1` Commands
     * * `2` Data and commands
     * * `3` As 2 plus connection status
     * * `4` Low&#45;level data output.
     *
     * @see SMTP::$do_debug
     *
     * @var int
     */
    public $SMTPDebug = 0;

    /**
     * How to handle debug output.
     * Options:
     * * `echo` Output plain&#45;text as&#45;is, appropriate for CLI
     * * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
     * * `error_log` Output to error log as configured in php.ini
     * By default PHPMailer will use `echo` if run from a `cli` or `cli&#45;server` SAPI, `html` otherwise.
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     *
     * ```php
     * $mail&#45;>Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * ```
     *
     * Alternatively, you can pass in an instance of a PSR&#45;3 compatible logger, though only `debug`
     * level output is used:
     *
     * ```php
     * $mail&#45;>Debugoutput = new myPsr3Logger;
     * ```
     *
     * @see SMTP::$Debugoutput
     *
     * @var string|callable|\Psr\Log\LoggerInterface
     */
    public $Debugoutput = &apos;echo&apos;;

    /**
     * Whether to keep SMTP connection open after each message.
     * If this is set to true then to close the connection
     * requires an explicit call to smtpClose().
     *
     * @var bool
     */
    public $SMTPKeepAlive = false;

    /**
     * Whether to split multiple to addresses into multiple messages
     * or send them all in one message.
     * Only supported in `mail` and `sendmail` transports, not in SMTP.
     *
     * @var bool
     */
    public $SingleTo = false;

    /**
     * Storage for addresses when SingleTo is enabled.
     *
     * @var array
     */
    protected $SingleToArray = [];

    /**
     * Whether to generate VERP addresses on send.
     * Only applicable when sending via SMTP.
     *
     * @see https://en.wikipedia.org/wiki/Variable_envelope_return_path
     * @see http://www.postfix.org/VERP_README.html Postfix VERP info
     *
     * @var bool
     */
    public $do_verp = false;

    /**
     * Whether to allow sending messages with an empty body.
     *
     * @var bool
     */
    public $AllowEmpty = false;

    /**
     * DKIM selector.
     *
     * @var string
     */
    public $DKIM_selector = &apos;&apos;;

    /**
     * DKIM Identity.
     * Usually the email address used as the source of the email.
     *
     * @var string
     */
    public $DKIM_identity = &apos;&apos;;

    /**
     * DKIM passphrase.
     * Used if your key is encrypted.
     *
     * @var string
     */
    public $DKIM_passphrase = &apos;&apos;;

    /**
     * DKIM signing domain name.
     *
     * @example &apos;example.com&apos;
     *
     * @var string
     */
    public $DKIM_domain = &apos;&apos;;

    /**
     * DKIM Copy header field values for diagnostic use.
     *
     * @var bool
     */
    public $DKIM_copyHeaderFields = true;

    /**
     * DKIM Extra signing headers.
     *
     * @example [&apos;List&#45;Unsubscribe&apos;, &apos;List&#45;Help&apos;]
     *
     * @var array
     */
    public $DKIM_extraHeaders = [];

    /**
     * DKIM private key file path.
     *
     * @var string
     */
    public $DKIM_private = &apos;&apos;;

    /**
     * DKIM private key string.
     *
     * If set, takes precedence over `$DKIM_private`.
     *
     * @var string
     */
    public $DKIM_private_string = &apos;&apos;;

    /**
     * Callback Action function name.
     *
     * The function that handles the result of the send email action.
     * It is called out by send() for each email sent.
     *
     * Value can be any php callable: http://www.php.net/is_callable
     *
     * Parameters:
     *   bool $result        result of the send action
     *   array   $to            email addresses of the recipients
     *   array   $cc            cc email addresses
     *   array   $bcc           bcc email addresses
     *   string  $subject       the subject
     *   string  $body          the email body
     *   string  $from          email address of sender
     *   string  $extra         extra information of possible use
     *                          "smtp_transaction_id&apos; => last smtp transaction id
     *
     * @var string
     */
    public $action_function = &apos;&apos;;

    /**
     * What to put in the X&#45;Mailer header.
     * Options: An empty string for PHPMailer default, whitespace for none, or a string to use.
     *
     * @var string
     */
    public $XMailer = &apos;&apos;;

    /**
     * Which validator to use by default when validating email addresses.
     * May be a callable to inject your own validator, but there are several built&#45;in validators.
     * The default validator uses PHP&apos;s FILTER_VALIDATE_EMAIL filter_var option.
     *
     * @see PHPMailer::validateAddress()
     *
     * @var string|callable
     */
    public static $validator = &apos;php&apos;;

    /**
     * An instance of the SMTP sender class.
     *
     * @var SMTP
     */
    protected $smtp;

    /**
     * The array of &apos;to&apos; names and addresses.
     *
     * @var array
     */
    protected $to = [];

    /**
     * The array of &apos;cc&apos; names and addresses.
     *
     * @var array
     */
    protected $cc = [];

    /**
     * The array of &apos;bcc&apos; names and addresses.
     *
     * @var array
     */
    protected $bcc = [];

    /**
     * The array of reply&#45;to names and addresses.
     *
     * @var array
     */
    protected $ReplyTo = [];

    /**
     * An array of all kinds of addresses.
     * Includes all of $to, $cc, $bcc.
     *
     * @see PHPMailer::$to
     * @see PHPMailer::$cc
     * @see PHPMailer::$bcc
     *
     * @var array
     */
    protected $all_recipients = [];

    /**
     * An array of names and addresses queued for validation.
     * In send(), valid and non duplicate entries are moved to $all_recipients
     * and one of $to, $cc, or $bcc.
     * This array is used only for addresses with IDN.
     *
     * @see PHPMailer::$to
     * @see PHPMailer::$cc
     * @see PHPMailer::$bcc
     * @see PHPMailer::$all_recipients
     *
     * @var array
     */
    protected $RecipientsQueue = [];

    /**
     * An array of reply&#45;to names and addresses queued for validation.
     * In send(), valid and non duplicate entries are moved to $ReplyTo.
     * This array is used only for addresses with IDN.
     *
     * @see PHPMailer::$ReplyTo
     *
     * @var array
     */
    protected $ReplyToQueue = [];

    /**
     * The array of attachments.
     *
     * @var array
     */
    protected $attachment = [];

    /**
     * The array of custom headers.
     *
     * @var array
     */
    protected $CustomHeader = [];

    /**
     * The most recent Message&#45;ID (including angular brackets).
     *
     * @var string
     */
    protected $lastMessageID = &apos;&apos;;

    /**
     * The message&apos;s MIME type.
     *
     * @var string
     */
    protected $message_type = &apos;&apos;;

    /**
     * The array of MIME boundary strings.
     *
     * @var array
     */
    protected $boundary = [];

    /**
     * The array of available languages.
     *
     * @var array
     */
    protected $language = [];

    /**
     * The number of errors encountered.
     *
     * @var int
     */
    protected $error_count = 0;

    /**
     * The S/MIME certificate file path.
     *
     * @var string
     */
    protected $sign_cert_file = &apos;&apos;;

    /**
     * The S/MIME key file path.
     *
     * @var string
     */
    protected $sign_key_file = &apos;&apos;;

    /**
     * The optional S/MIME extra certificates ("CA Chain") file path.
     *
     * @var string
     */
    protected $sign_extracerts_file = &apos;&apos;;

    /**
     * The S/MIME password for the key.
     * Used only if the key is encrypted.
     *
     * @var string
     */
    protected $sign_key_pass = &apos;&apos;;

    /**
     * Whether to throw exceptions for errors.
     *
     * @var bool
     */
    protected $exceptions = false;

    /**
     * Unique ID used for message ID and boundaries.
     *
     * @var string
     */
    protected $uniqueid = &apos;&apos;;

    /**
     * The PHPMailer Version number.
     *
     * @var string
     */
    const VERSION = &apos;6.0.7&apos;;

    /**
     * Error severity: message only, continue processing.
     *
     * @var int
     */
    const STOP_MESSAGE = 0;

    /**
     * Error severity: message, likely ok to continue processing.
     *
     * @var int
     */
    const STOP_CONTINUE = 1;

    /**
     * Error severity: message, plus full stop, critical error reached.
     *
     * @var int
     */
    const STOP_CRITICAL = 2;

    /**
     * SMTP RFC standard line ending.
     *
     * @var string
     */
    protected static $LE = "\r\n";

    /**
     * The maximum line length allowed by RFC 2822 section 2.1.1.
     *
     * @var int
     */
    const MAX_LINE_LENGTH = 998;

    /**
     * The lower maximum line length allowed by RFC 2822 section 2.1.1.
     * This length does NOT include the line break
     * 76 means that lines will be 77 or 78 chars depending on whether
     * the line break format is LF or CRLF; both are valid.
     *
     * @var int
     */
    const STD_LINE_LENGTH = 76;

    /**
     * Constructor.
     *
     * @param bool $exceptions Should we throw external exceptions?
     */
    public function __construct($exceptions = null)
    {
        if (null !== $exceptions) {
            $this&#45;>exceptions = (bool) $exceptions;
        }
        //Pick an appropriate debug output format automatically
        $this&#45;>Debugoutput = (strpos(PHP_SAPI, &apos;cli&apos;) !== false ? &apos;echo&apos; : &apos;html&apos;);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        //Close any open SMTP connection nicely
        $this&#45;>smtpClose();
    }

    /**
     * Call mail() in a safe_mode&#45;aware fashion.
     * Also, unless sendmail_path points to sendmail (or something that
     * claims to be sendmail), don&apos;t pass params (not a perfect fix,
     * but it will do).
     *
     * @param string      $to      To
     * @param string      $subject Subject
     * @param string      $body    Message Body
     * @param string      $header  Additional Header(s)
     * @param string|null $params  Params
     *
     * @return bool
     */
    private function mailPassthru($to, $subject, $body, $header, $params)
    {
        //Check overloading of mail function to avoid double&#45;encoding
        if (ini_get(&apos;mbstring.func_overload&apos;) & 1) {
            $subject = $this&#45;>secureHeader($subject);
        } else {
            $subject = $this&#45;>encodeHeader($this&#45;>secureHeader($subject));
        }
        //Calling mail() with null params breaks
        if (!$this&#45;>UseSendmailOptions or null === $params) {
            $result = @mail($to, $subject, $body, $header);
        } else {
            $result = @mail($to, $subject, $body, $header, $params);
        }

        return $result;
    }

    /**
     * Output debugging info via user&#45;defined method.
     * Only generates output if SMTP debug output is enabled (@see SMTP::$do_debug).
     *
     * @see PHPMailer::$Debugoutput
     * @see PHPMailer::$SMTPDebug
     *
     * @param string $str
     */
    protected function edebug($str)
    {
        if ($this&#45;>SMTPDebug <= 0) {
            return;
        }
        //Is this a PSR&#45;3 logger?
        if ($this&#45;>Debugoutput instanceof \Psr\Log\LoggerInterface) {
            $this&#45;>Debugoutput&#45;>debug($str);

            return;
        }
        //Avoid clash with built&#45;in function names
        if (!in_array($this&#45;>Debugoutput, [&apos;error_log&apos;, &apos;html&apos;, &apos;echo&apos;]) and is_callable($this&#45;>Debugoutput)) {
            call_user_func($this&#45;>Debugoutput, $str, $this&#45;>SMTPDebug);

            return;
        }
        switch ($this&#45;>Debugoutput) {
            case &apos;error_log&apos;:
                //Don&apos;t output, just log
                error_log($str);
                break;
            case &apos;html&apos;:
                //Cleans up output a bit for a better looking, HTML&#45;safe output
                echo htmlentities(
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
     * Sets message type to HTML or plain.
     *
     * @param bool $isHtml True for HTML mode
     */
    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this&#45;>ContentType = static::CONTENT_TYPE_TEXT_HTML;
        } else {
            $this&#45;>ContentType = static::CONTENT_TYPE_PLAINTEXT;
        }
    }

    /**
     * Send messages using SMTP.
     */
    public function isSMTP()
    {
        $this&#45;>Mailer = &apos;smtp&apos;;
    }

    /**
     * Send messages using PHP&apos;s mail() function.
     */
    public function isMail()
    {
        $this&#45;>Mailer = &apos;mail&apos;;
    }

    /**
     * Send messages using $Sendmail.
     */
    public function isSendmail()
    {
        $ini_sendmail_path = ini_get(&apos;sendmail_path&apos;);

        if (false === stripos($ini_sendmail_path, &apos;sendmail&apos;)) {
            $this&#45;>Sendmail = &apos;/usr/sbin/sendmail&apos;;
        } else {
            $this&#45;>Sendmail = $ini_sendmail_path;
        }
        $this&#45;>Mailer = &apos;sendmail&apos;;
    }

    /**
     * Send messages using qmail.
     */
    public function isQmail()
    {
        $ini_sendmail_path = ini_get(&apos;sendmail_path&apos;);

        if (false === stripos($ini_sendmail_path, &apos;qmail&apos;)) {
            $this&#45;>Sendmail = &apos;/var/qmail/bin/qmail&#45;inject&apos;;
        } else {
            $this&#45;>Sendmail = $ini_sendmail_path;
        }
        $this&#45;>Mailer = &apos;qmail&apos;;
    }

    /**
     * Add a "To" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = &apos;&apos;)
    {
        return $this&#45;>addOrEnqueueAnAddress(&apos;to&apos;, $address, $name);
    }

    /**
     * Add a "CC" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addCC($address, $name = &apos;&apos;)
    {
        return $this&#45;>addOrEnqueueAnAddress(&apos;cc&apos;, $address, $name);
    }

    /**
     * Add a "BCC" address.
     *
     * @param string $address The email address to send to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addBCC($address, $name = &apos;&apos;)
    {
        return $this&#45;>addOrEnqueueAnAddress(&apos;bcc&apos;, $address, $name);
    }

    /**
     * Add a "Reply&#45;To" address.
     *
     * @param string $address The email address to reply to
     * @param string $name
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    public function addReplyTo($address, $name = &apos;&apos;)
    {
        return $this&#45;>addOrEnqueueAnAddress(&apos;Reply&#45;To&apos;, $address, $name);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
     * can&apos;t validate addresses with an IDN without knowing the PHPMailer::$CharSet (that can still
     * be modified after calling this function), addition of such addresses is delayed until send().
     * Addresses that have been added already return false, but do not throw exceptions.
     *
     * @param string $kind    One of &apos;to&apos;, &apos;cc&apos;, &apos;bcc&apos;, or &apos;ReplyTo&apos;
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        $address = trim($address);
        $name = trim(preg_replace(&apos;/[\r\n]+/&apos;, &apos;&apos;, $name)); //Strip breaks and trim
        $pos = strrpos($address, &apos;@&apos;);
        if (false === $pos) {
            // At&#45;sign is missing.
            $error_message = sprintf(&apos;%s (%s): %s&apos;,
                $this&#45;>lang(&apos;invalid_address&apos;),
                $kind,
                $address);
            $this&#45;>setError($error_message);
            $this&#45;>edebug($error_message);
            if ($this&#45;>exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $params = [$kind, $address, $name];
        // Enqueue addresses with IDN until we know the PHPMailer::$CharSet.
        if ($this&#45;>has8bitChars(substr($address, ++$pos)) and static::idnSupported()) {
            if (&apos;Reply&#45;To&apos; != $kind) {
                if (!array_key_exists($address, $this&#45;>RecipientsQueue)) {
                    $this&#45;>RecipientsQueue[$address] = $params;

                    return true;
                }
            } else {
                if (!array_key_exists($address, $this&#45;>ReplyToQueue)) {
                    $this&#45;>ReplyToQueue[$address] = $params;

                    return true;
                }
            }

            return false;
        }

        // Immediately add standard addresses without IDN.
        return call_user_func_array([$this, &apos;addAnAddress&apos;], $params);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array.
     * Addresses that have been added already return false, but do not throw exceptions.
     *
     * @param string $kind    One of &apos;to&apos;, &apos;cc&apos;, &apos;bcc&apos;, or &apos;ReplyTo&apos;
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     *
     * @throws Exception
     *
     * @return bool true on success, false if address already used or invalid in some way
     */
    protected function addAnAddress($kind, $address, $name = &apos;&apos;)
    {
        if (!in_array($kind, [&apos;to&apos;, &apos;cc&apos;, &apos;bcc&apos;, &apos;Reply&#45;To&apos;])) {
            $error_message = sprintf(&apos;%s: %s&apos;,
                $this&#45;>lang(&apos;Invalid recipient kind&apos;),
                $kind);
            $this&#45;>setError($error_message);
            $this&#45;>edebug($error_message);
            if ($this&#45;>exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if (!static::validateAddress($address)) {
            $error_message = sprintf(&apos;%s (%s): %s&apos;,
                $this&#45;>lang(&apos;invalid_address&apos;),
                $kind,
                $address);
            $this&#45;>setError($error_message);
            $this&#45;>edebug($error_message);
            if ($this&#45;>exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        if (&apos;Reply&#45;To&apos; != $kind) {
            if (!array_key_exists(strtolower($address), $this&#45;>all_recipients)) {
                $this&#45;>{$kind}[] = [$address, $name];
                $this&#45;>all_recipients[strtolower($address)] = true;

                return true;
            }
        } else {
            if (!array_key_exists(strtolower($address), $this&#45;>ReplyTo)) {
                $this&#45;>ReplyTo[strtolower($address)] = [$address, $name];

                return true;
            }
        }

        return false;
    }

    /**
     * Parse and validate a string containing one or more RFC822&#45;style comma&#45;separated email addresses
     * of the form "display name <address>" into an array of name/address pairs.
     * Uses the imap_rfc822_parse_adrlist function if the IMAP extension is available.
     * Note that quotes in the name part are removed.
     *
     * @see    http://www.andrew.cmu.edu/user/agreen1/testing/mrbs/web/Mail/RFC822.php A more careful implementation
     *
     * @param string $addrstr The address list string
     * @param bool   $useimap Whether to use the IMAP extension to parse the list
     *
     * @return array
     */
    public static function parseAddresses($addrstr, $useimap = true)
    {
        $addresses = [];
        if ($useimap and function_exists(&apos;imap_rfc822_parse_adrlist&apos;)) {
            //Use this built&#45;in parser if it&apos;s available
            $list = imap_rfc822_parse_adrlist($addrstr, &apos;&apos;);
            foreach ($list as $address) {
                if (&apos;.SYNTAX&#45;ERROR.&apos; != $address&#45;>host) {
                    if (static::validateAddress($address&#45;>mailbox . &apos;@&apos; . $address&#45;>host)) {
                        $addresses[] = [
                            &apos;name&apos; => (property_exists($address, &apos;personal&apos;) ? $address&#45;>personal : &apos;&apos;),
                            &apos;address&apos; => $address&#45;>mailbox . &apos;@&apos; . $address&#45;>host,
                        ];
                    }
                }
            }
        } else {
            //Use this simpler parser
            $list = explode(&apos;,&apos;, $addrstr);
            foreach ($list as $address) {
                $address = trim($address);
                //Is there a separate name part?
                if (strpos($address, &apos;<&apos;) === false) {
                    //No separate name, just use the whole thing
                    if (static::validateAddress($address)) {
                        $addresses[] = [
                            &apos;name&apos; => &apos;&apos;,
                            &apos;address&apos; => $address,
                        ];
                    }
                } else {
                    list($name, $email) = explode(&apos;<&apos;, $address);
                    $email = trim(str_replace(&apos;>&apos;, &apos;&apos;, $email));
                    if (static::validateAddress($email)) {
                        $addresses[] = [
                            &apos;name&apos; => trim(str_replace([&apos;"&apos;, "&apos;"], &apos;&apos;, $name)),
                            &apos;address&apos; => $email,
                        ];
                    }
                }
            }
        }

        return $addresses;
    }

    /**
     * Set the From and FromName properties.
     *
     * @param string $address
     * @param string $name
     * @param bool   $auto    Whether to also set the Sender address, defaults to true
     *
     * @throws Exception
     *
     * @return bool
     */
    public function setFrom($address, $name = &apos;&apos;, $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace(&apos;/[\r\n]+/&apos;, &apos;&apos;, $name)); //Strip breaks and trim
        // Don&apos;t validate now addresses with IDN. Will be done in send().
        $pos = strrpos($address, &apos;@&apos;);
        if (false === $pos or
            (!$this&#45;>has8bitChars(substr($address, ++$pos)) or !static::idnSupported()) and
            !static::validateAddress($address)) {
            $error_message = sprintf(&apos;%s (From): %s&apos;,
                $this&#45;>lang(&apos;invalid_address&apos;),
                $address);
            $this&#45;>setError($error_message);
            $this&#45;>edebug($error_message);
            if ($this&#45;>exceptions) {
                throw new Exception($error_message);
            }

            return false;
        }
        $this&#45;>From = $address;
        $this&#45;>FromName = $name;
        if ($auto) {
            if (empty($this&#45;>Sender)) {
                $this&#45;>Sender = $address;
            }
        }

        return true;
    }

    /**
     * Return the Message&#45;ID header of the last email.
     * Technically this is the value from the last time the headers were created,
     * but it&apos;s also the message ID of the last sent message except in
     * pathological cases.
     *
     * @return string
     */
    public function getLastMessageID()
    {
        return $this&#45;>lastMessageID;
    }

    /**
     * Check that a string looks like an email address.
     * Validation patterns supported:
     * * `auto` Pick best pattern automatically;
     * * `pcre8` Use the squiloople.com pattern, requires PCRE > 8.0;
     * * `pcre` Use old PCRE implementation;
     * * `php` Use PHP built&#45;in FILTER_VALIDATE_EMAIL;
     * * `html5` Use the pattern given by the HTML5 spec for &apos;email&apos; type form input elements.
     * * `noregex` Don&apos;t use a regex: super fast, really dumb.
     * Alternatively you may pass in a callable to inject your own validator, for example:
     *
     * ```php
     * PHPMailer::validateAddress(&apos;user@example.com&apos;, function($address) {
     *     return (strpos($address, &apos;@&apos;) !== false);
     * });
     * ```
     *
     * You can also set the PHPMailer::$validator static to a callable, allowing built&#45;in methods to use your validator.
     *
     * @param string          $address       The email address to check
     * @param string|callable $patternselect Which pattern to use
     *
     * @return bool
     */
    public static function validateAddress($address, $patternselect = null)
    {
        if (null === $patternselect) {
            $patternselect = static::$validator;
        }
        if (is_callable($patternselect)) {
            return call_user_func($patternselect, $address);
        }
        //Reject line breaks in addresses; it&apos;s valid RFC5322, but not RFC5321
        if (strpos($address, "\n") !== false or strpos($address, "\r") !== false) {
            return false;
        }
        switch ($patternselect) {
            case &apos;pcre&apos;: //Kept for BC
            case &apos;pcre8&apos;:
                /*
                 * A more complex and more permissive version of the RFC5322 regex on which FILTER_VALIDATE_EMAIL
                 * is based.
                 * In addition to the addresses allowed by filter_var, also permits:
                 *  * dotless domains: `a@b`
                 *  * comments: `1234 @ local(blah) .machine .example`
                 *  * quoted elements: `&apos;"test blah"@example.org&apos;`
                 *  * numeric TLDs: `a@b.123`
                 *  * unbracketed IPv4 literals: `a@192.168.0.1`
                 *  * IPv6 literals: &apos;first.last@[IPv6:a1::]&apos;
                 * Not all of these will necessarily work for sending!
                 *
                 * @see       http://squiloople.com/2009/12/20/email&#45;address&#45;validation/
                 * @copyright 2009&#45;2010 Michael Rushton
                 * Feel free to use and redistribute this code. But please keep this copyright notice.
                 */
                return (bool) preg_match(
                    &apos;/^(?!(?>(?1)"?(?>\\\[ &#45;~]|[^"])"?(?1)){255,})(?!(?>(?1)"?(?>\\\[ &#45;~]|[^"])"?(?1)){65,}@)&apos; .
                    &apos;((?>(?>(?>((?>(?>(?>\x0D\x0A)?[\t ])+|(?>[\t ]*\x0D\x0A)?[\t ]+)?)(\((?>(?2)&apos; .
                    &apos;(?>[\x01&#45;\x08\x0B\x0C\x0E&#45;\&apos;*&#45;\[\]&#45;\x7F]|\\\[\x00&#45;\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)&apos; .
                    &apos;([!#&#45;\&apos;*+\/&#45;9=?^&#45;~&#45;]+|"(?>(?2)(?>[\x01&#45;\x08\x0B\x0C\x0E&#45;!#&#45;\[\]&#45;\x7F]|\\\[\x00&#45;\x7F]))*&apos; .
                    &apos;(?2)")(?>(?1)\.(?1)(?4))*(?1)@(?!(?1)[a&#45;z0&#45;9&#45;]{64,})(?1)(?>([a&#45;z0&#45;9](?>[a&#45;z0&#45;9&#45;]*[a&#45;z0&#45;9])?)&apos; .
                    &apos;(?>(?1)\.(?!(?1)[a&#45;z0&#45;9&#45;]{64,})(?1)(?5)){0,126}|\[(?:(?>IPv6:(?>([a&#45;f0&#45;9]{1,4})(?>:(?6)){7}&apos; .
                    &apos;|(?!(?:.*[a&#45;f0&#45;9][:\]]){8,})((?6)(?>:(?6)){0,6})?::(?7)?))|(?>(?>IPv6:(?>(?6)(?>:(?6)){5}:&apos; .
                    &apos;|(?!(?:.*[a&#45;f0&#45;9]:){6,})(?8)?::(?>((?6)(?>:(?6)){0,4}):)?))?(25[0&#45;5]|2[0&#45;4][0&#45;9]|1[0&#45;9]{2}&apos; .
                    &apos;|[1&#45;9]?[0&#45;9])(?>\.(?9)){3}))\])(?1)$/isD&apos;,
                    $address
                );
            case &apos;html5&apos;:
                /*
                 * This is the pattern used in the HTML5 spec for validation of &apos;email&apos; type form input elements.
                 *
                 * @see http://www.whatwg.org/specs/web&#45;apps/current&#45;work/#e&#45;mail&#45;state&#45;(type=email)
                 */
                return (bool) preg_match(
                    &apos;/^[a&#45;zA&#45;Z0&#45;9.!#$%&\&apos;*+\/=?^_`{|}~&#45;]+@[a&#45;zA&#45;Z0&#45;9](?:[a&#45;zA&#45;Z0&#45;9&#45;]{0,61}&apos; .
                    &apos;[a&#45;zA&#45;Z0&#45;9])?(?:\.[a&#45;zA&#45;Z0&#45;9](?:[a&#45;zA&#45;Z0&#45;9&#45;]{0,61}[a&#45;zA&#45;Z0&#45;9])?)*$/sD&apos;,
                    $address
                );
            case &apos;php&apos;:
            default:
                return (bool) filter_var($address, FILTER_VALIDATE_EMAIL);
        }
    }

    /**
     * Tells whether IDNs (Internationalized Domain Names) are supported or not. This requires the
     * `intl` and `mbstring` PHP extensions.
     *
     * @return bool `true` if required functions for IDN support are present
     */
    public static function idnSupported()
    {
        return function_exists(&apos;idn_to_ascii&apos;) and function_exists(&apos;mb_convert_encoding&apos;);
    }

    /**
     * Converts IDN in given email address to its ASCII form, also known as punycode, if possible.
     * Important: Address must be passed in same encoding as currently set in PHPMailer::$CharSet.
     * This function silently returns unmodified address if:
     * &#45; No conversion is necessary (i.e. domain name is not an IDN, or is already in ASCII form)
     * &#45; Conversion to punycode is impossible (e.g. required PHP functions are not available)
     *   or fails for any reason (e.g. domain contains characters not allowed in an IDN).
     *
     * @see    PHPMailer::$CharSet
     *
     * @param string $address The email address to convert
     *
     * @return string The encoded address in ASCII form
     */
    public function punyencodeAddress($address)
    {
        // Verify we have required functions, CharSet, and at&#45;sign.
        $pos = strrpos($address, &apos;@&apos;);
        if (static::idnSupported() and
            !empty($this&#45;>CharSet) and
            false !== $pos
        ) {
            $domain = substr($address, ++$pos);
            // Verify CharSet string is a valid one, and domain properly encoded in this CharSet.
            if ($this&#45;>has8bitChars($domain) and @mb_check_encoding($domain, $this&#45;>CharSet)) {
                $domain = mb_convert_encoding($domain, &apos;UTF&#45;8&apos;, $this&#45;>CharSet);
                //Ignore IDE complaints about this line &#45; method signature changed in PHP 5.4
                $errorcode = 0;
                $punycode = idn_to_ascii($domain, $errorcode, INTL_IDNA_VARIANT_UTS46);
                if (false !== $punycode) {
                    return substr($address, 0, $pos) . $punycode;
                }
            }
        }

        return $address;
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     *
     * @throws Exception
     *
     * @return bool false on error &#45; See the ErrorInfo property for details of the error
     */
    public function send()
    {
        try {
            if (!$this&#45;>preSend()) {
                return false;
            }

            return $this&#45;>postSend();
        } catch (Exception $exc) {
            $this&#45;>mailHeader = &apos;&apos;;
            $this&#45;>setError($exc&#45;>getMessage());
            if ($this&#45;>exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Prepare a message for sending.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function preSend()
    {
        if (&apos;smtp&apos; == $this&#45;>Mailer or
            (&apos;mail&apos; == $this&#45;>Mailer and stripos(PHP_OS, &apos;WIN&apos;) === 0)
        ) {
            //SMTP mandates RFC&#45;compliant line endings
            //and it&apos;s also used with mail() on Windows
            static::setLE("\r\n");
        } else {
            //Maintain backward compatibility with legacy Linux command line mailers
            static::setLE(PHP_EOL);
        }
        //Check for buggy PHP versions that add a header with an incorrect line break
        if (ini_get(&apos;mail.add_x_header&apos;) == 1
            and &apos;mail&apos; == $this&#45;>Mailer
            and stripos(PHP_OS, &apos;WIN&apos;) === 0
            and ((version_compare(PHP_VERSION, &apos;7.0.0&apos;, &apos;>=&apos;)
                    and version_compare(PHP_VERSION, &apos;7.0.17&apos;, &apos;<&apos;))
                or (version_compare(PHP_VERSION, &apos;7.1.0&apos;, &apos;>=&apos;)
                    and version_compare(PHP_VERSION, &apos;7.1.3&apos;, &apos;<&apos;)))
        ) {
            trigger_error(
                &apos;Your version of PHP is affected by a bug that may result in corrupted messages.&apos; .
                &apos; To fix it, switch to sending using SMTP, disable the mail.add_x_header option in&apos; .
                &apos; your php.ini, switch to MacOS or Linux, or upgrade your PHP to version 7.0.17+ or 7.1.3+.&apos;,
                E_USER_WARNING
            );
        }

        try {
            $this&#45;>error_count = 0; // Reset errors
            $this&#45;>mailHeader = &apos;&apos;;

            // Dequeue recipient and Reply&#45;To addresses with IDN
            foreach (array_merge($this&#45;>RecipientsQueue, $this&#45;>ReplyToQueue) as $params) {
                $params[1] = $this&#45;>punyencodeAddress($params[1]);
                call_user_func_array([$this, &apos;addAnAddress&apos;], $params);
            }
            if (count($this&#45;>to) + count($this&#45;>cc) + count($this&#45;>bcc) < 1) {
                throw new Exception($this&#45;>lang(&apos;provide_address&apos;), self::STOP_CRITICAL);
            }

            // Validate From, Sender, and ConfirmReadingTo addresses
            foreach ([&apos;From&apos;, &apos;Sender&apos;, &apos;ConfirmReadingTo&apos;] as $address_kind) {
                $this&#45;>$address_kind = trim($this&#45;>$address_kind);
                if (empty($this&#45;>$address_kind)) {
                    continue;
                }
                $this&#45;>$address_kind = $this&#45;>punyencodeAddress($this&#45;>$address_kind);
                if (!static::validateAddress($this&#45;>$address_kind)) {
                    $error_message = sprintf(&apos;%s (%s): %s&apos;,
                        $this&#45;>lang(&apos;invalid_address&apos;),
                        $address_kind,
                        $this&#45;>$address_kind);
                    $this&#45;>setError($error_message);
                    $this&#45;>edebug($error_message);
                    if ($this&#45;>exceptions) {
                        throw new Exception($error_message);
                    }

                    return false;
                }
            }

            // Set whether the message is multipart/alternative
            if ($this&#45;>alternativeExists()) {
                $this&#45;>ContentType = static::CONTENT_TYPE_MULTIPART_ALTERNATIVE;
            }

            $this&#45;>setMessageType();
            // Refuse to send an empty message unless we are specifically allowing it
            if (!$this&#45;>AllowEmpty and empty($this&#45;>Body)) {
                throw new Exception($this&#45;>lang(&apos;empty_message&apos;), self::STOP_CRITICAL);
            }

            //Trim subject consistently
            $this&#45;>Subject = trim($this&#45;>Subject);
            // Create body before headers in case body makes changes to headers (e.g. altering transfer encoding)
            $this&#45;>MIMEHeader = &apos;&apos;;
            $this&#45;>MIMEBody = $this&#45;>createBody();
            // createBody may have added some headers, so retain them
            $tempheaders = $this&#45;>MIMEHeader;
            $this&#45;>MIMEHeader = $this&#45;>createHeader();
            $this&#45;>MIMEHeader .= $tempheaders;

            // To capture the complete message when using mail(), create
            // an extra header list which createHeader() doesn&apos;t fold in
            if (&apos;mail&apos; == $this&#45;>Mailer) {
                if (count($this&#45;>to) > 0) {
                    $this&#45;>mailHeader .= $this&#45;>addrAppend(&apos;To&apos;, $this&#45;>to);
                } else {
                    $this&#45;>mailHeader .= $this&#45;>headerLine(&apos;To&apos;, &apos;undisclosed&#45;recipients:;&apos;);
                }
                $this&#45;>mailHeader .= $this&#45;>headerLine(
                    &apos;Subject&apos;,
                    $this&#45;>encodeHeader($this&#45;>secureHeader($this&#45;>Subject))
                );
            }

            // Sign with DKIM if enabled
            if (!empty($this&#45;>DKIM_domain)
                and !empty($this&#45;>DKIM_selector)
                and (!empty($this&#45;>DKIM_private_string)
                    or (!empty($this&#45;>DKIM_private)
                        and static::isPermittedPath($this&#45;>DKIM_private)
                        and file_exists($this&#45;>DKIM_private)
                    )
                )
            ) {
                $header_dkim = $this&#45;>DKIM_Add(
                    $this&#45;>MIMEHeader . $this&#45;>mailHeader,
                    $this&#45;>encodeHeader($this&#45;>secureHeader($this&#45;>Subject)),
                    $this&#45;>MIMEBody
                );
                $this&#45;>MIMEHeader = rtrim($this&#45;>MIMEHeader, "\r\n ") . static::$LE .
                    static::normalizeBreaks($header_dkim) . static::$LE;
            }

            return true;
        } catch (Exception $exc) {
            $this&#45;>setError($exc&#45;>getMessage());
            if ($this&#45;>exceptions) {
                throw $exc;
            }

            return false;
        }
    }

    /**
     * Actually send a message via the selected mechanism.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function postSend()
    {
        try {
            // Choose the mailer and send through it
            switch ($this&#45;>Mailer) {
                case &apos;sendmail&apos;:
                case &apos;qmail&apos;:
                    return $this&#45;>sendmailSend($this&#45;>MIMEHeader, $this&#45;>MIMEBody);
                case &apos;smtp&apos;:
                    return $this&#45;>smtpSend($this&#45;>MIMEHeader, $this&#45;>MIMEBody);
                case &apos;mail&apos;:
                    return $this&#45;>mailSend($this&#45;>MIMEHeader, $this&#45;>MIMEBody);
                default:
                    $sendMethod = $this&#45;>Mailer . &apos;Send&apos;;
                    if (method_exists($this, $sendMethod)) {
                        return $this&#45;>$sendMethod($this&#45;>MIMEHeader, $this&#45;>MIMEBody);
                    }

                    return $this&#45;>mailSend($this&#45;>MIMEHeader, $this&#45;>MIMEBody);
            }
        } catch (Exception $exc) {
            $this&#45;>setError($exc&#45;>getMessage());
            $this&#45;>edebug($exc&#45;>getMessage());
            if ($this&#45;>exceptions) {
                throw $exc;
            }
        }

        return false;
    }

    /**
     * Send mail using the $Sendmail program.
     *
     * @see    PHPMailer::$Sendmail
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function sendmailSend($header, $body)
    {
        // CVE&#45;2016&#45;10033, CVE&#45;2016&#45;10045: Don&apos;t pass &#45;f if characters will be escaped.
        if (!empty($this&#45;>Sender) and self::isShellSafe($this&#45;>Sender)) {
            if (&apos;qmail&apos; == $this&#45;>Mailer) {
                $sendmailFmt = &apos;%s &#45;f%s&apos;;
            } else {
                $sendmailFmt = &apos;%s &#45;oi &#45;f%s &#45;t&apos;;
            }
        } else {
            if (&apos;qmail&apos; == $this&#45;>Mailer) {
                $sendmailFmt = &apos;%s&apos;;
            } else {
                $sendmailFmt = &apos;%s &#45;oi &#45;t&apos;;
            }
        }

        $sendmail = sprintf($sendmailFmt, escapeshellcmd($this&#45;>Sendmail), $this&#45;>Sender);

        if ($this&#45;>SingleTo) {
            foreach ($this&#45;>SingleToArray as $toAddr) {
                $mail = @popen($sendmail, &apos;w&apos;);
                if (!$mail) {
                    throw new Exception($this&#45;>lang(&apos;execute&apos;) . $this&#45;>Sendmail, self::STOP_CRITICAL);
                }
                fwrite($mail, &apos;To: &apos; . $toAddr . "\n");
                fwrite($mail, $header);
                fwrite($mail, $body);
                $result = pclose($mail);
                $this&#45;>doCallback(
                    ($result == 0),
                    [$toAddr],
                    $this&#45;>cc,
                    $this&#45;>bcc,
                    $this&#45;>Subject,
                    $body,
                    $this&#45;>From,
                    []
                );
                if (0 !== $result) {
                    throw new Exception($this&#45;>lang(&apos;execute&apos;) . $this&#45;>Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            $mail = @popen($sendmail, &apos;w&apos;);
            if (!$mail) {
                throw new Exception($this&#45;>lang(&apos;execute&apos;) . $this&#45;>Sendmail, self::STOP_CRITICAL);
            }
            fwrite($mail, $header);
            fwrite($mail, $body);
            $result = pclose($mail);
            $this&#45;>doCallback(
                ($result == 0),
                $this&#45;>to,
                $this&#45;>cc,
                $this&#45;>bcc,
                $this&#45;>Subject,
                $body,
                $this&#45;>From,
                []
            );
            if (0 !== $result) {
                throw new Exception($this&#45;>lang(&apos;execute&apos;) . $this&#45;>Sendmail, self::STOP_CRITICAL);
            }
        }

        return true;
    }

    /**
     * Fix CVE&#45;2016&#45;10033 and CVE&#45;2016&#45;10045 by disallowing potentially unsafe shell characters.
     * Note that escapeshellarg and escapeshellcmd are inadequate for our purposes, especially on Windows.
     *
     * @see https://github.com/PHPMailer/PHPMailer/issues/924 CVE&#45;2016&#45;10045 bug report
     *
     * @param string $string The string to be validated
     *
     * @return bool
     */
    protected static function isShellSafe($string)
    {
        // Future&#45;proof
        if (escapeshellcmd($string) !== $string
            or !in_array(escapeshellarg($string), ["&apos;$string&apos;", "\"$string\""])
        ) {
            return false;
        }

        $length = strlen($string);

        for ($i = 0; $i < $length; ++$i) {
            $c = $string[$i];

            // All other characters have a special meaning in at least one common shell, including = and +.
            // Full stop (.) has a special meaning in cmd.exe, but its impact should be negligible here.
            // Note that this does permit non&#45;Latin alphanumeric characters based on the current locale.
            if (!ctype_alnum($c) && strpos(&apos;@_&#45;.&apos;, $c) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether a file path is of a permitted type.
     * Used to reject URLs and phar files from functions that access local file paths,
     * such as addAttachment.
     *
     * @param string $path A relative or absolute path to a file
     *
     * @return bool
     */
    protected static function isPermittedPath($path)
    {
        return !preg_match(&apos;#^[a&#45;z]+://#i&apos;, $path);
    }

    /**
     * Send mail using the PHP mail() function.
     *
     * @see    http://www.php.net/manual/en/book.mail.php
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function mailSend($header, $body)
    {
        $toArr = [];
        foreach ($this&#45;>to as $toaddr) {
            $toArr[] = $this&#45;>addrFormat($toaddr);
        }
        $to = implode(&apos;, &apos;, $toArr);

        $params = null;
        //This sets the SMTP envelope sender which gets turned into a return&#45;path header by the receiver
        if (!empty($this&#45;>Sender) and static::validateAddress($this&#45;>Sender)) {
            //A space after `&#45;f` is optional, but there is a long history of its presence
            //causing problems, so we don&apos;t use one
            //Exim docs: http://www.exim.org/exim&#45;html&#45;current/doc/html/spec_html/ch&#45;the_exim_command_line.html
            //Sendmail docs: http://www.sendmail.org/~ca/email/man/sendmail.html
            //Qmail docs: http://www.qmail.org/man/man8/qmail&#45;inject.html
            //Example problem: https://www.drupal.org/node/1057954
            // CVE&#45;2016&#45;10033, CVE&#45;2016&#45;10045: Don&apos;t pass &#45;f if characters will be escaped.
            if (self::isShellSafe($this&#45;>Sender)) {
                $params = sprintf(&apos;&#45;f%s&apos;, $this&#45;>Sender);
            }
        }
        if (!empty($this&#45;>Sender) and static::validateAddress($this&#45;>Sender)) {
            $old_from = ini_get(&apos;sendmail_from&apos;);
            ini_set(&apos;sendmail_from&apos;, $this&#45;>Sender);
        }
        $result = false;
        if ($this&#45;>SingleTo and count($toArr) > 1) {
            foreach ($toArr as $toAddr) {
                $result = $this&#45;>mailPassthru($toAddr, $this&#45;>Subject, $body, $header, $params);
                $this&#45;>doCallback($result, [$toAddr], $this&#45;>cc, $this&#45;>bcc, $this&#45;>Subject, $body, $this&#45;>From, []);
            }
        } else {
            $result = $this&#45;>mailPassthru($to, $this&#45;>Subject, $body, $header, $params);
            $this&#45;>doCallback($result, $this&#45;>to, $this&#45;>cc, $this&#45;>bcc, $this&#45;>Subject, $body, $this&#45;>From, []);
        }
        if (isset($old_from)) {
            ini_set(&apos;sendmail_from&apos;, $old_from);
        }
        if (!$result) {
            throw new Exception($this&#45;>lang(&apos;instantiate&apos;), self::STOP_CRITICAL);
        }

        return true;
    }

    /**
     * Get an instance to use for SMTP operations.
     * Override this function to load your own SMTP implementation,
     * or set one with setSMTPInstance.
     *
     * @return SMTP
     */
    public function getSMTPInstance()
    {
        if (!is_object($this&#45;>smtp)) {
            $this&#45;>smtp = new SMTP();
        }

        return $this&#45;>smtp;
    }

    /**
     * Provide an instance to use for SMTP operations.
     *
     * @param SMTP $smtp
     *
     * @return SMTP
     */
    public function setSMTPInstance(SMTP $smtp)
    {
        $this&#45;>smtp = $smtp;

        return $this&#45;>smtp;
    }

    /**
     * Send mail via SMTP.
     * Returns false if there is a bad MAIL FROM, RCPT, or DATA input.
     *
     * @see PHPMailer::setSMTPInstance() to use a different class.
     *
     * @uses \PHPMailer\PHPMailer\SMTP
     *
     * @param string $header The message headers
     * @param string $body   The message body
     *
     * @throws Exception
     *
     * @return bool
     */
    protected function smtpSend($header, $body)
    {
        $bad_rcpt = [];
        if (!$this&#45;>smtpConnect($this&#45;>SMTPOptions)) {
            throw new Exception($this&#45;>lang(&apos;smtp_connect_failed&apos;), self::STOP_CRITICAL);
        }
        //Sender already validated in preSend()
        if (&apos;&apos; == $this&#45;>Sender) {
            $smtp_from = $this&#45;>From;
        } else {
            $smtp_from = $this&#45;>Sender;
        }
        if (!$this&#45;>smtp&#45;>mail($smtp_from)) {
            $this&#45;>setError($this&#45;>lang(&apos;from_failed&apos;) . $smtp_from . &apos; : &apos; . implode(&apos;,&apos;, $this&#45;>smtp&#45;>getError()));
            throw new Exception($this&#45;>ErrorInfo, self::STOP_CRITICAL);
        }

        $callbacks = [];
        // Attempt to send to all recipients
        foreach ([$this&#45;>to, $this&#45;>cc, $this&#45;>bcc] as $togroup) {
            foreach ($togroup as $to) {
                if (!$this&#45;>smtp&#45;>recipient($to[0])) {
                    $error = $this&#45;>smtp&#45;>getError();
                    $bad_rcpt[] = [&apos;to&apos; => $to[0], &apos;error&apos; => $error[&apos;detail&apos;]];
                    $isSent = false;
                } else {
                    $isSent = true;
                }

                $callbacks[] = [&apos;issent&apos;=>$isSent, &apos;to&apos;=>$to[0]];
            }
        }

        // Only send the DATA command if we have viable recipients
        if ((count($this&#45;>all_recipients) > count($bad_rcpt)) and !$this&#45;>smtp&#45;>data($header . $body)) {
            throw new Exception($this&#45;>lang(&apos;data_not_accepted&apos;), self::STOP_CRITICAL);
        }

        $smtp_transaction_id = $this&#45;>smtp&#45;>getLastTransactionID();

        if ($this&#45;>SMTPKeepAlive) {
            $this&#45;>smtp&#45;>reset();
        } else {
            $this&#45;>smtp&#45;>quit();
            $this&#45;>smtp&#45;>close();
        }

        foreach ($callbacks as $cb) {
            $this&#45;>doCallback(
                $cb[&apos;issent&apos;],
                [$cb[&apos;to&apos;]],
                [],
                [],
                $this&#45;>Subject,
                $body,
                $this&#45;>From,
                [&apos;smtp_transaction_id&apos; => $smtp_transaction_id]
            );
        }

        //Create error message for any bad addresses
        if (count($bad_rcpt) > 0) {
            $errstr = &apos;&apos;;
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad[&apos;to&apos;] . &apos;: &apos; . $bad[&apos;error&apos;];
            }
            throw new Exception(
                $this&#45;>lang(&apos;recipients_failed&apos;) . $errstr,
                self::STOP_CONTINUE
            );
        }

        return true;
    }

    /**
     * Initiate a connection to an SMTP server.
     * Returns false if the operation failed.
     *
     * @param array $options An array of options compatible with stream_context_create()
     *
     * @throws Exception
     *
     * @uses \PHPMailer\PHPMailer\SMTP
     *
     * @return bool
     */
    public function smtpConnect($options = null)
    {
        if (null === $this&#45;>smtp) {
            $this&#45;>smtp = $this&#45;>getSMTPInstance();
        }

        //If no options are provided, use whatever is set in the instance
        if (null === $options) {
            $options = $this&#45;>SMTPOptions;
        }

        // Already connected?
        if ($this&#45;>smtp&#45;>connected()) {
            return true;
        }

        $this&#45;>smtp&#45;>setTimeout($this&#45;>Timeout);
        $this&#45;>smtp&#45;>setDebugLevel($this&#45;>SMTPDebug);
        $this&#45;>smtp&#45;>setDebugOutput($this&#45;>Debugoutput);
        $this&#45;>smtp&#45;>setVerp($this&#45;>do_verp);
        $hosts = explode(&apos;;&apos;, $this&#45;>Host);
        $lastexception = null;

        foreach ($hosts as $hostentry) {
            $hostinfo = [];
            if (!preg_match(
                &apos;/^((ssl|tls):\/\/)*([a&#45;zA&#45;Z0&#45;9\.&#45;]*|\[[a&#45;fA&#45;F0&#45;9:]+\]):?([0&#45;9]*)$/&apos;,
                trim($hostentry),
                $hostinfo
            )) {
                static::edebug($this&#45;>lang(&apos;connect_host&apos;) . &apos; &apos; . $hostentry);
                // Not a valid host entry
                continue;
            }
            // $hostinfo[2]: optional ssl or tls prefix
            // $hostinfo[3]: the hostname
            // $hostinfo[4]: optional port number
            // The host string prefix can temporarily override the current setting for SMTPSecure
            // If it&apos;s not specified, the default value is used

            //Check the host name is a valid name or IP address before trying to use it
            if (!static::isValidHost($hostinfo[3])) {
                static::edebug($this&#45;>lang(&apos;connect_host&apos;) . &apos; &apos; . $hostentry);
                continue;
            }
            $prefix = &apos;&apos;;
            $secure = $this&#45;>SMTPSecure;
            $tls = (&apos;tls&apos; == $this&#45;>SMTPSecure);
            if (&apos;ssl&apos; == $hostinfo[2] or (&apos;&apos; == $hostinfo[2] and &apos;ssl&apos; == $this&#45;>SMTPSecure)) {
                $prefix = &apos;ssl://&apos;;
                $tls = false; // Can&apos;t have SSL and TLS at the same time
                $secure = &apos;ssl&apos;;
            } elseif (&apos;tls&apos; == $hostinfo[2]) {
                $tls = true;
                // tls doesn&apos;t use a prefix
                $secure = &apos;tls&apos;;
            }
            //Do we need the OpenSSL extension?
            $sslext = defined(&apos;OPENSSL_ALGO_SHA256&apos;);
            if (&apos;tls&apos; === $secure or &apos;ssl&apos; === $secure) {
                //Check for an OpenSSL constant rather than using extension_loaded, which is sometimes disabled
                if (!$sslext) {
                    throw new Exception($this&#45;>lang(&apos;extension_missing&apos;) . &apos;openssl&apos;, self::STOP_CRITICAL);
                }
            }
            $host = $hostinfo[3];
            $port = $this&#45;>Port;
            $tport = (int) $hostinfo[4];
            if ($tport > 0 and $tport < 65536) {
                $port = $tport;
            }
            if ($this&#45;>smtp&#45;>connect($prefix . $host, $port, $this&#45;>Timeout, $options)) {
                try {
                    if ($this&#45;>Helo) {
                        $hello = $this&#45;>Helo;
                    } else {
                        $hello = $this&#45;>serverHostname();
                    }
                    $this&#45;>smtp&#45;>hello($hello);
                    //Automatically enable TLS encryption if:
                    // * it&apos;s not disabled
                    // * we have openssl extension
                    // * we are not already using SSL
                    // * the server offers STARTTLS
                    if ($this&#45;>SMTPAutoTLS and $sslext and &apos;ssl&apos; != $secure and $this&#45;>smtp&#45;>getServerExt(&apos;STARTTLS&apos;)) {
                        $tls = true;
                    }
                    if ($tls) {
                        if (!$this&#45;>smtp&#45;>startTLS()) {
                            throw new Exception($this&#45;>lang(&apos;connect_host&apos;));
                        }
                        // We must resend EHLO after TLS negotiation
                        $this&#45;>smtp&#45;>hello($hello);
                    }
                    if ($this&#45;>SMTPAuth) {
                        if (!$this&#45;>smtp&#45;>authenticate(
                            $this&#45;>Username,
                            $this&#45;>Password,
                            $this&#45;>AuthType,
                            $this&#45;>oauth
                        )
                        ) {
                            throw new Exception($this&#45;>lang(&apos;authenticate&apos;));
                        }
                    }

                    return true;
                } catch (Exception $exc) {
                    $lastexception = $exc;
                    $this&#45;>edebug($exc&#45;>getMessage());
                    // We must have connected, but then failed TLS or Auth, so close connection nicely
                    $this&#45;>smtp&#45;>quit();
                }
            }
        }
        // If we get here, all connection attempts have failed, so close connection hard
        $this&#45;>smtp&#45;>close();
        // As we&apos;ve caught all exceptions, just report whatever the last one was
        if ($this&#45;>exceptions and null !== $lastexception) {
            throw $lastexception;
        }

        return false;
    }

    /**
     * Close the active SMTP session if one exists.
     */
    public function smtpClose()
    {
        if (null !== $this&#45;>smtp) {
            if ($this&#45;>smtp&#45;>connected()) {
                $this&#45;>smtp&#45;>quit();
                $this&#45;>smtp&#45;>close();
            }
        }
    }

    /**
     * Set the language for error messages.
     * Returns false if it cannot load the language file.
     * The default language is English.
     *
     * @param string $langcode  ISO 639&#45;1 2&#45;character language code (e.g. French is "fr")
     * @param string $lang_path Path to the language file directory, with trailing separator (slash)
     *
     * @return bool
     */
    public function setLanguage($langcode = &apos;en&apos;, $lang_path = &apos;&apos;)
    {
        // Backwards compatibility for renamed language codes
        $renamed_langcodes = [
            &apos;br&apos; => &apos;pt_br&apos;,
            &apos;cz&apos; => &apos;cs&apos;,
            &apos;dk&apos; => &apos;da&apos;,
            &apos;no&apos; => &apos;nb&apos;,
            &apos;se&apos; => &apos;sv&apos;,
            &apos;rs&apos; => &apos;sr&apos;,
            &apos;tg&apos; => &apos;tl&apos;,
        ];

        if (isset($renamed_langcodes[$langcode])) {
            $langcode = $renamed_langcodes[$langcode];
        }

        // Define full set of translatable strings in English
        $PHPMAILER_LANG = [
            &apos;authenticate&apos; => &apos;SMTP Error: Could not authenticate.&apos;,
            &apos;connect_host&apos; => &apos;SMTP Error: Could not connect to SMTP host.&apos;,
            &apos;data_not_accepted&apos; => &apos;SMTP Error: data not accepted.&apos;,
            &apos;empty_message&apos; => &apos;Message body empty&apos;,
            &apos;encoding&apos; => &apos;Unknown encoding: &apos;,
            &apos;execute&apos; => &apos;Could not execute: &apos;,
            &apos;file_access&apos; => &apos;Could not access file: &apos;,
            &apos;file_open&apos; => &apos;File Error: Could not open file: &apos;,
            &apos;from_failed&apos; => &apos;The following From address failed: &apos;,
            &apos;instantiate&apos; => &apos;Could not instantiate mail function.&apos;,
            &apos;invalid_address&apos; => &apos;Invalid address: &apos;,
            &apos;mailer_not_supported&apos; => &apos; mailer is not supported.&apos;,
            &apos;provide_address&apos; => &apos;You must provide at least one recipient email address.&apos;,
            &apos;recipients_failed&apos; => &apos;SMTP Error: The following recipients failed: &apos;,
            &apos;signing&apos; => &apos;Signing Error: &apos;,
            &apos;smtp_connect_failed&apos; => &apos;SMTP connect() failed.&apos;,
            &apos;smtp_error&apos; => &apos;SMTP server error: &apos;,
            &apos;variable_set&apos; => &apos;Cannot set or reset variable: &apos;,
            &apos;extension_missing&apos; => &apos;Extension missing: &apos;,
        ];
        if (empty($lang_path)) {
            // Calculate an absolute path so it can work if CWD is not here
            $lang_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . &apos;language&apos; . DIRECTORY_SEPARATOR;
        }
        //Validate $langcode
        if (!preg_match(&apos;/^[a&#45;z]{2}(?:_[a&#45;zA&#45;Z]{2})?$/&apos;, $langcode)) {
            $langcode = &apos;en&apos;;
        }
        $foundlang = true;
        $lang_file = $lang_path . &apos;phpmailer.lang&#45;&apos; . $langcode . &apos;.php&apos;;
        // There is no English translation file
        if (&apos;en&apos; != $langcode) {
            // Make sure language file path is readable
            if (!static::isPermittedPath($lang_file) || !file_exists($lang_file)) {
                $foundlang = false;
            } else {
                // Overwrite language&#45;specific strings.
                // This way we&apos;ll never have missing translation keys.
                $foundlang = include $lang_file;
            }
        }
        $this&#45;>language = $PHPMAILER_LANG;

        return (bool) $foundlang; // Returns false if language not found
    }

    /**
     * Get the array of strings for the current language.
     *
     * @return array
     */
    public function getTranslations()
    {
        return $this&#45;>language;
    }

    /**
     * Create recipient headers.
     *
     * @param string $type
     * @param array  $addr An array of recipients,
     *                     where each recipient is a 2&#45;element indexed array with element 0 containing an address
     *                     and element 1 containing a name, like:
     *                     [[&apos;joe@example.com&apos;, &apos;Joe User&apos;], [&apos;zoe@example.com&apos;, &apos;Zoe User&apos;]]
     *
     * @return string
     */
    public function addrAppend($type, $addr)
    {
        $addresses = [];
        foreach ($addr as $address) {
            $addresses[] = $this&#45;>addrFormat($address);
        }

        return $type . &apos;: &apos; . implode(&apos;, &apos;, $addresses) . static::$LE;
    }

    /**
     * Format an address for use in a message header.
     *
     * @param array $addr A 2&#45;element indexed array, element 0 containing an address, element 1 containing a name like
     *                    [&apos;joe@example.com&apos;, &apos;Joe User&apos;]
     *
     * @return string
     */
    public function addrFormat($addr)
    {
        if (empty($addr[1])) { // No name provided
            return $this&#45;>secureHeader($addr[0]);
        }

        return $this&#45;>encodeHeader($this&#45;>secureHeader($addr[1]), &apos;phrase&apos;) . &apos; <&apos; . $this&#45;>secureHeader(
                $addr[0]
            ) . &apos;>&apos;;
    }

    /**
     * Word&#45;wrap message.
     * For use with mailers that do not automatically perform wrapping
     * and for quoted&#45;printable encoded messages.
     * Original written by philippe.
     *
     * @param string $message The message to wrap
     * @param int    $length  The line length to wrap to
     * @param bool   $qp_mode Whether to run in Quoted&#45;Printable mode
     *
     * @return string
     */
    public function wrapText($message, $length, $qp_mode = false)
    {
        if ($qp_mode) {
            $soft_break = sprintf(&apos; =%s&apos;, static::$LE);
        } else {
            $soft_break = static::$LE;
        }
        // If utf&#45;8 encoding is used, we will need to make sure we don&apos;t
        // split multibyte characters when we wrap
        $is_utf8 = static::CHARSET_UTF8 === strtolower($this&#45;>CharSet);
        $lelen = strlen(static::$LE);
        $crlflen = strlen(static::$LE);

        $message = static::normalizeBreaks($message);
        //Remove a trailing line break
        if (substr($message, &#45;$lelen) == static::$LE) {
            $message = substr($message, 0, &#45;$lelen);
        }

        //Split message into lines
        $lines = explode(static::$LE, $message);
        //Message will be rebuilt in here
        $message = &apos;&apos;;
        foreach ($lines as $line) {
            $words = explode(&apos; &apos;, $line);
            $buf = &apos;&apos;;
            $firstword = true;
            foreach ($words as $word) {
                if ($qp_mode and (strlen($word) > $length)) {
                    $space_left = $length &#45; strlen($buf) &#45; $crlflen;
                    if (!$firstword) {
                        if ($space_left > 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this&#45;>utf8CharBoundary($word, $len);
                            } elseif (&apos;=&apos; == substr($word, $len &#45; 1, 1)) {
                                &#45;&#45;$len;
                            } elseif (&apos;=&apos; == substr($word, $len &#45; 2, 1)) {
                                $len &#45;= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= &apos; &apos; . $part;
                            $message .= $buf . sprintf(&apos;=%s&apos;, static::$LE);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = &apos;&apos;;
                    }
                    while (strlen($word) > 0) {
                        if ($length <= 0) {
                            break;
                        }
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this&#45;>utf8CharBoundary($word, $len);
                        } elseif (&apos;=&apos; == substr($word, $len &#45; 1, 1)) {
                            &#45;&#45;$len;
                        } elseif (&apos;=&apos; == substr($word, $len &#45; 2, 1)) {
                            $len &#45;= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) > 0) {
                            $message .= $part . sprintf(&apos;=%s&apos;, static::$LE);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    if (!$firstword) {
                        $buf .= &apos; &apos;;
                    }
                    $buf .= $word;

                    if (strlen($buf) > $length and &apos;&apos; != $buf_o) {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
                $firstword = false;
            }
            $message .= $buf . static::$LE;
        }

        return $message;
    }

    /**
     * Find the last character boundary prior to $maxLength in a utf&#45;8
     * quoted&#45;printable encoded string.
     * Original written by Colin Brown.
     *
     * @param string $encodedText utf&#45;8 QP text
     * @param int    $maxLength   Find the last character boundary prior to this length
     *
     * @return int
     */
    public function utf8CharBoundary($encodedText, $maxLength)
    {
        $foundSplitPos = false;
        $lookBack = 3;
        while (!$foundSplitPos) {
            $lastChunk = substr($encodedText, $maxLength &#45; $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, &apos;=&apos;);
            if (false !== $encodedCharPos) {
                // Found start of encoded character byte within $lookBack block.
                // Check the encoded byte value (the 2 chars after the &apos;=&apos;)
                $hex = substr($encodedText, $maxLength &#45; $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec < 128) {
                    // Single byte character.
                    // If the encoded char was found at pos 0, it will fit
                    // otherwise reduce maxLength to start of the encoded char
                    if ($encodedCharPos > 0) {
                        $maxLength &#45;= $lookBack &#45; $encodedCharPos;
                    }
                    $foundSplitPos = true;
                } elseif ($dec >= 192) {
                    // First byte of a multi byte character
                    // Reduce maxLength to split at start of character
                    $maxLength &#45;= $lookBack &#45; $encodedCharPos;
                    $foundSplitPos = true;
                } elseif ($dec < 192) {
                    // Middle byte of a multi byte character, look further back
                    $lookBack += 3;
                }
            } else {
                // No encoded character found
                $foundSplitPos = true;
            }
        }

        return $maxLength;
    }

    /**
     * Apply word wrapping to the message body.
     * Wraps the message body to the number of chars set in the WordWrap property.
     * You should only do this to plain&#45;text bodies as wrapping HTML tags may break them.
     * This is called automatically by createBody(), so you don&apos;t need to call it yourself.
     */
    public function setWordWrap()
    {
        if ($this&#45;>WordWrap < 1) {
            return;
        }

        switch ($this&#45;>message_type) {
            case &apos;alt&apos;:
            case &apos;alt_inline&apos;:
            case &apos;alt_attach&apos;:
            case &apos;alt_inline_attach&apos;:
                $this&#45;>AltBody = $this&#45;>wrapText($this&#45;>AltBody, $this&#45;>WordWrap);
                break;
            default:
                $this&#45;>Body = $this&#45;>wrapText($this&#45;>Body, $this&#45;>WordWrap);
                break;
        }
    }

    /**
     * Assemble message headers.
     *
     * @return string The assembled headers
     */
    public function createHeader()
    {
        $result = &apos;&apos;;

        $result .= $this&#45;>headerLine(&apos;Date&apos;, &apos;&apos; == $this&#45;>MessageDate ? self::rfcDate() : $this&#45;>MessageDate);

        // To be created automatically by mail()
        if ($this&#45;>SingleTo) {
            if (&apos;mail&apos; != $this&#45;>Mailer) {
                foreach ($this&#45;>to as $toaddr) {
                    $this&#45;>SingleToArray[] = $this&#45;>addrFormat($toaddr);
                }
            }
        } else {
            if (count($this&#45;>to) > 0) {
                if (&apos;mail&apos; != $this&#45;>Mailer) {
                    $result .= $this&#45;>addrAppend(&apos;To&apos;, $this&#45;>to);
                }
            } elseif (count($this&#45;>cc) == 0) {
                $result .= $this&#45;>headerLine(&apos;To&apos;, &apos;undisclosed&#45;recipients:;&apos;);
            }
        }

        $result .= $this&#45;>addrAppend(&apos;From&apos;, [[trim($this&#45;>From), $this&#45;>FromName]]);

        // sendmail and mail() extract Cc from the header before sending
        if (count($this&#45;>cc) > 0) {
            $result .= $this&#45;>addrAppend(&apos;Cc&apos;, $this&#45;>cc);
        }

        // sendmail and mail() extract Bcc from the header before sending
        if ((
                &apos;sendmail&apos; == $this&#45;>Mailer or &apos;qmail&apos; == $this&#45;>Mailer or &apos;mail&apos; == $this&#45;>Mailer
            )
            and count($this&#45;>bcc) > 0
        ) {
            $result .= $this&#45;>addrAppend(&apos;Bcc&apos;, $this&#45;>bcc);
        }

        if (count($this&#45;>ReplyTo) > 0) {
            $result .= $this&#45;>addrAppend(&apos;Reply&#45;To&apos;, $this&#45;>ReplyTo);
        }

        // mail() sets the subject itself
        if (&apos;mail&apos; != $this&#45;>Mailer) {
            $result .= $this&#45;>headerLine(&apos;Subject&apos;, $this&#45;>encodeHeader($this&#45;>secureHeader($this&#45;>Subject)));
        }

        // Only allow a custom message ID if it conforms to RFC 5322 section 3.6.4
        // https://tools.ietf.org/html/rfc5322#section&#45;3.6.4
        if (&apos;&apos; != $this&#45;>MessageID and preg_match(&apos;/^<.*@.*>$/&apos;, $this&#45;>MessageID)) {
            $this&#45;>lastMessageID = $this&#45;>MessageID;
        } else {
            $this&#45;>lastMessageID = sprintf(&apos;<%s@%s>&apos;, $this&#45;>uniqueid, $this&#45;>serverHostname());
        }
        $result .= $this&#45;>headerLine(&apos;Message&#45;ID&apos;, $this&#45;>lastMessageID);
        if (null !== $this&#45;>Priority) {
            $result .= $this&#45;>headerLine(&apos;X&#45;Priority&apos;, $this&#45;>Priority);
        }
        if (&apos;&apos; == $this&#45;>XMailer) {
            $result .= $this&#45;>headerLine(
                &apos;X&#45;Mailer&apos;,
                &apos;PHPMailer &apos; . self::VERSION . &apos; (https://github.com/PHPMailer/PHPMailer)&apos;
            );
        } else {
            $myXmailer = trim($this&#45;>XMailer);
            if ($myXmailer) {
                $result .= $this&#45;>headerLine(&apos;X&#45;Mailer&apos;, $myXmailer);
            }
        }

        if (&apos;&apos; != $this&#45;>ConfirmReadingTo) {
            $result .= $this&#45;>headerLine(&apos;Disposition&#45;Notification&#45;To&apos;, &apos;<&apos; . $this&#45;>ConfirmReadingTo . &apos;>&apos;);
        }

        // Add custom headers
        foreach ($this&#45;>CustomHeader as $header) {
            $result .= $this&#45;>headerLine(
                trim($header[0]),
                $this&#45;>encodeHeader(trim($header[1]))
            );
        }
        if (!$this&#45;>sign_key_file) {
            $result .= $this&#45;>headerLine(&apos;MIME&#45;Version&apos;, &apos;1.0&apos;);
            $result .= $this&#45;>getMailMIME();
        }

        return $result;
    }

    /**
     * Get the message MIME type headers.
     *
     * @return string
     */
    public function getMailMIME()
    {
        $result = &apos;&apos;;
        $ismultipart = true;
        switch ($this&#45;>message_type) {
            case &apos;inline&apos;:
                $result .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_RELATED . &apos;;&apos;);
                $result .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[1] . &apos;"&apos;);
                break;
            case &apos;attach&apos;:
            case &apos;inline_attach&apos;:
            case &apos;alt_attach&apos;:
            case &apos;alt_inline_attach&apos;:
                $result .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_MIXED . &apos;;&apos;);
                $result .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[1] . &apos;"&apos;);
                break;
            case &apos;alt&apos;:
            case &apos;alt_inline&apos;:
                $result .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . &apos;;&apos;);
                $result .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[1] . &apos;"&apos;);
                break;
            default:
                // Catches case &apos;plain&apos;: and case &apos;&apos;:
                $result .= $this&#45;>textLine(&apos;Content&#45;Type: &apos; . $this&#45;>ContentType . &apos;; charset=&apos; . $this&#45;>CharSet);
                $ismultipart = false;
                break;
        }
        // RFC1341 part 5 says 7bit is assumed if not specified
        if (static::ENCODING_7BIT != $this&#45;>Encoding) {
            // RFC 2045 section 6.4 says multipart MIME parts may only use 7bit, 8bit or binary CTE
            if ($ismultipart) {
                if (static::ENCODING_8BIT == $this&#45;>Encoding) {
                    $result .= $this&#45;>headerLine(&apos;Content&#45;Transfer&#45;Encoding&apos;, static::ENCODING_8BIT);
                }
                // The only remaining alternatives are quoted&#45;printable and base64, which are both 7bit compatible
            } else {
                $result .= $this&#45;>headerLine(&apos;Content&#45;Transfer&#45;Encoding&apos;, $this&#45;>Encoding);
            }
        }

        if (&apos;mail&apos; != $this&#45;>Mailer) {
            $result .= static::$LE;
        }

        return $result;
    }

    /**
     * Returns the whole MIME message.
     * Includes complete headers and body.
     * Only valid post preSend().
     *
     * @see PHPMailer::preSend()
     *
     * @return string
     */
    public function getSentMIMEMessage()
    {
        return rtrim($this&#45;>MIMEHeader . $this&#45;>mailHeader, "\n\r") . static::$LE . static::$LE . $this&#45;>MIMEBody;
    }

    /**
     * Create a unique ID to use for boundaries.
     *
     * @return string
     */
    protected function generateId()
    {
        $len = 32; //32 bytes = 256 bits
        if (function_exists(&apos;random_bytes&apos;)) {
            $bytes = random_bytes($len);
        } elseif (function_exists(&apos;openssl_random_pseudo_bytes&apos;)) {
            $bytes = openssl_random_pseudo_bytes($len);
        } else {
            //Use a hash to force the length to the same as the other methods
            $bytes = hash(&apos;sha256&apos;, uniqid((string) mt_rand(), true), true);
        }

        //We don&apos;t care about messing up base64 format here, just want a random string
        return str_replace([&apos;=&apos;, &apos;+&apos;, &apos;/&apos;], &apos;&apos;, base64_encode(hash(&apos;sha256&apos;, $bytes, true)));
    }

    /**
     * Assemble the message body.
     * Returns an empty string on failure.
     *
     * @throws Exception
     *
     * @return string The assembled message body
     */
    public function createBody()
    {
        $body = &apos;&apos;;
        //Create unique IDs and preset boundaries
        $this&#45;>uniqueid = $this&#45;>generateId();
        $this&#45;>boundary[1] = &apos;b1_&apos; . $this&#45;>uniqueid;
        $this&#45;>boundary[2] = &apos;b2_&apos; . $this&#45;>uniqueid;
        $this&#45;>boundary[3] = &apos;b3_&apos; . $this&#45;>uniqueid;

        if ($this&#45;>sign_key_file) {
            $body .= $this&#45;>getMailMIME() . static::$LE;
        }

        $this&#45;>setWordWrap();

        $bodyEncoding = $this&#45;>Encoding;
        $bodyCharSet = $this&#45;>CharSet;
        //Can we do a 7&#45;bit downgrade?
        if (static::ENCODING_8BIT == $bodyEncoding and !$this&#45;>has8bitChars($this&#45;>Body)) {
            $bodyEncoding = static::ENCODING_7BIT;
            //All ISO 8859, Windows codepage and UTF&#45;8 charsets are ascii compatible up to 7&#45;bit
            $bodyCharSet = &apos;us&#45;ascii&apos;;
        }
        //If lines are too long, and we&apos;re not already using an encoding that will shorten them,
        //change to quoted&#45;printable transfer encoding for the body part only
        if (static::ENCODING_BASE64 != $this&#45;>Encoding and static::hasLineLongerThanMax($this&#45;>Body)) {
            $bodyEncoding = static::ENCODING_QUOTED_PRINTABLE;
        }

        $altBodyEncoding = $this&#45;>Encoding;
        $altBodyCharSet = $this&#45;>CharSet;
        //Can we do a 7&#45;bit downgrade?
        if (static::ENCODING_8BIT == $altBodyEncoding and !$this&#45;>has8bitChars($this&#45;>AltBody)) {
            $altBodyEncoding = static::ENCODING_7BIT;
            //All ISO 8859, Windows codepage and UTF&#45;8 charsets are ascii compatible up to 7&#45;bit
            $altBodyCharSet = &apos;us&#45;ascii&apos;;
        }
        //If lines are too long, and we&apos;re not already using an encoding that will shorten them,
        //change to quoted&#45;printable transfer encoding for the alt body part only
        if (static::ENCODING_BASE64 != $altBodyEncoding and static::hasLineLongerThanMax($this&#45;>AltBody)) {
            $altBodyEncoding = static::ENCODING_QUOTED_PRINTABLE;
        }
        //Use this as a preamble in all multipart message types
        $mimepre = &apos;This is a multi&#45;part message in MIME format.&apos; . static::$LE;
        switch ($this&#45;>message_type) {
            case &apos;inline&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[1], $bodyCharSet, &apos;&apos;, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;inline&apos;, $this&#45;>boundary[1]);
                break;
            case &apos;attach&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[1], $bodyCharSet, &apos;&apos;, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;attachment&apos;, $this&#45;>boundary[1]);
                break;
            case &apos;inline_attach&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $this&#45;>boundary[1]);
                $body .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_RELATED . &apos;;&apos;);
                $body .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[2] . &apos;"&apos;);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[2], $bodyCharSet, &apos;&apos;, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;inline&apos;, $this&#45;>boundary[2]);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;attachment&apos;, $this&#45;>boundary[1]);
                break;
            case &apos;alt&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[1], $altBodyCharSet, static::CONTENT_TYPE_PLAINTEXT, $altBodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[1], $bodyCharSet, static::CONTENT_TYPE_TEXT_HTML, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                if (!empty($this&#45;>Ical)) {
                    $body .= $this&#45;>getBoundary($this&#45;>boundary[1], &apos;&apos;, static::CONTENT_TYPE_TEXT_CALENDAR . &apos;; method=REQUEST&apos;, &apos;&apos;);
                    $body .= $this&#45;>encodeString($this&#45;>Ical, $this&#45;>Encoding);
                    $body .= static::$LE;
                }
                $body .= $this&#45;>endBoundary($this&#45;>boundary[1]);
                break;
            case &apos;alt_inline&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[1], $altBodyCharSet, static::CONTENT_TYPE_PLAINTEXT, $altBodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $this&#45;>boundary[1]);
                $body .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_RELATED . &apos;;&apos;);
                $body .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[2] . &apos;"&apos;);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[2], $bodyCharSet, static::CONTENT_TYPE_TEXT_HTML, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;inline&apos;, $this&#45;>boundary[2]);
                $body .= static::$LE;
                $body .= $this&#45;>endBoundary($this&#45;>boundary[1]);
                break;
            case &apos;alt_attach&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $this&#45;>boundary[1]);
                $body .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . &apos;;&apos;);
                $body .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[2] . &apos;"&apos;);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[2], $altBodyCharSet, static::CONTENT_TYPE_PLAINTEXT, $altBodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[2], $bodyCharSet, static::CONTENT_TYPE_TEXT_HTML, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                if (!empty($this&#45;>Ical)) {
                    $body .= $this&#45;>getBoundary($this&#45;>boundary[2], &apos;&apos;, static::CONTENT_TYPE_TEXT_CALENDAR . &apos;; method=REQUEST&apos;, &apos;&apos;);
                    $body .= $this&#45;>encodeString($this&#45;>Ical, $this&#45;>Encoding);
                }
                $body .= $this&#45;>endBoundary($this&#45;>boundary[2]);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;attachment&apos;, $this&#45;>boundary[1]);
                break;
            case &apos;alt_inline_attach&apos;:
                $body .= $mimepre;
                $body .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $this&#45;>boundary[1]);
                $body .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_ALTERNATIVE . &apos;;&apos;);
                $body .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[2] . &apos;"&apos;);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[2], $altBodyCharSet, static::CONTENT_TYPE_PLAINTEXT, $altBodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>AltBody, $altBodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $this&#45;>boundary[2]);
                $body .= $this&#45;>headerLine(&apos;Content&#45;Type&apos;, static::CONTENT_TYPE_MULTIPART_RELATED . &apos;;&apos;);
                $body .= $this&#45;>textLine("\tboundary=\"" . $this&#45;>boundary[3] . &apos;"&apos;);
                $body .= static::$LE;
                $body .= $this&#45;>getBoundary($this&#45;>boundary[3], $bodyCharSet, static::CONTENT_TYPE_TEXT_HTML, $bodyEncoding);
                $body .= $this&#45;>encodeString($this&#45;>Body, $bodyEncoding);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;inline&apos;, $this&#45;>boundary[3]);
                $body .= static::$LE;
                $body .= $this&#45;>endBoundary($this&#45;>boundary[2]);
                $body .= static::$LE;
                $body .= $this&#45;>attachAll(&apos;attachment&apos;, $this&#45;>boundary[1]);
                break;
            default:
                // Catch case &apos;plain&apos; and case &apos;&apos;, applies to simple `text/plain` and `text/html` body content types
                //Reset the `Encoding` property in case we changed it for line length reasons
                $this&#45;>Encoding = $bodyEncoding;
                $body .= $this&#45;>encodeString($this&#45;>Body, $this&#45;>Encoding);
                break;
        }

        if ($this&#45;>isError()) {
            $body = &apos;&apos;;
            if ($this&#45;>exceptions) {
                throw new Exception($this&#45;>lang(&apos;empty_message&apos;), self::STOP_CRITICAL);
            }
        } elseif ($this&#45;>sign_key_file) {
            try {
                if (!defined(&apos;PKCS7_TEXT&apos;)) {
                    throw new Exception($this&#45;>lang(&apos;extension_missing&apos;) . &apos;openssl&apos;);
                }
                // @TODO would be nice to use php://temp streams here
                $file = tempnam(sys_get_temp_dir(), &apos;mail&apos;);
                if (false === file_put_contents($file, $body)) {
                    throw new Exception($this&#45;>lang(&apos;signing&apos;) . &apos; Could not write temp file&apos;);
                }
                $signed = tempnam(sys_get_temp_dir(), &apos;signed&apos;);
                //Workaround for PHP bug https://bugs.php.net/bug.php?id=69197
                if (empty($this&#45;>sign_extracerts_file)) {
                    $sign = @openssl_pkcs7_sign(
                        $file,
                        $signed,
                        &apos;file://&apos; . realpath($this&#45;>sign_cert_file),
                        [&apos;file://&apos; . realpath($this&#45;>sign_key_file), $this&#45;>sign_key_pass],
                        []
                    );
                } else {
                    $sign = @openssl_pkcs7_sign(
                        $file,
                        $signed,
                        &apos;file://&apos; . realpath($this&#45;>sign_cert_file),
                        [&apos;file://&apos; . realpath($this&#45;>sign_key_file), $this&#45;>sign_key_pass],
                        [],
                        PKCS7_DETACHED,
                        $this&#45;>sign_extracerts_file
                    );
                }
                @unlink($file);
                if ($sign) {
                    $body = file_get_contents($signed);
                    @unlink($signed);
                    //The message returned by openssl contains both headers and body, so need to split them up
                    $parts = explode("\n\n", $body, 2);
                    $this&#45;>MIMEHeader .= $parts[0] . static::$LE . static::$LE;
                    $body = $parts[1];
                } else {
                    @unlink($signed);
                    throw new Exception($this&#45;>lang(&apos;signing&apos;) . openssl_error_string());
                }
            } catch (Exception $exc) {
                $body = &apos;&apos;;
                if ($this&#45;>exceptions) {
                    throw $exc;
                }
            }
        }

        return $body;
    }

    /**
     * Return the start of a message boundary.
     *
     * @param string $boundary
     * @param string $charSet
     * @param string $contentType
     * @param string $encoding
     *
     * @return string
     */
    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = &apos;&apos;;
        if (&apos;&apos; == $charSet) {
            $charSet = $this&#45;>CharSet;
        }
        if (&apos;&apos; == $contentType) {
            $contentType = $this&#45;>ContentType;
        }
        if (&apos;&apos; == $encoding) {
            $encoding = $this&#45;>Encoding;
        }
        $result .= $this&#45;>textLine(&apos;&#45;&#45;&apos; . $boundary);
        $result .= sprintf(&apos;Content&#45;Type: %s; charset=%s&apos;, $contentType, $charSet);
        $result .= static::$LE;
        // RFC1341 part 5 says 7bit is assumed if not specified
        if (static::ENCODING_7BIT != $encoding) {
            $result .= $this&#45;>headerLine(&apos;Content&#45;Transfer&#45;Encoding&apos;, $encoding);
        }
        $result .= static::$LE;

        return $result;
    }

    /**
     * Return the end of a message boundary.
     *
     * @param string $boundary
     *
     * @return string
     */
    protected function endBoundary($boundary)
    {
        return static::$LE . &apos;&#45;&#45;&apos; . $boundary . &apos;&#45;&#45;&apos; . static::$LE;
    }

    /**
     * Set the message type.
     * PHPMailer only supports some preset message types, not arbitrary MIME structures.
     */
    protected function setMessageType()
    {
        $type = [];
        if ($this&#45;>alternativeExists()) {
            $type[] = &apos;alt&apos;;
        }
        if ($this&#45;>inlineImageExists()) {
            $type[] = &apos;inline&apos;;
        }
        if ($this&#45;>attachmentExists()) {
            $type[] = &apos;attach&apos;;
        }
        $this&#45;>message_type = implode(&apos;_&apos;, $type);
        if (&apos;&apos; == $this&#45;>message_type) {
            //The &apos;plain&apos; message_type refers to the message having a single body element, not that it is plain&#45;text
            $this&#45;>message_type = &apos;plain&apos;;
        }
    }

    /**
     * Format a header line.
     *
     * @param string     $name
     * @param string|int $value
     *
     * @return string
     */
    public function headerLine($name, $value)
    {
        return $name . &apos;: &apos; . $value . static::$LE;
    }

    /**
     * Return a formatted mail line.
     *
     * @param string $value
     *
     * @return string
     */
    public function textLine($value)
    {
        return $value . static::$LE;
    }

    /**
     * Add an attachment from a path on the filesystem.
     * Never use a user&#45;supplied path to a file!
     * Returns false if the file could not be found or read.
     * Explicitly *does not* support passing URLs; PHPMailer is not an HTTP client.
     * If you need to do that, fetch the resource yourself and pass it in via a local file or string.
     *
     * @param string $path        Path to the attachment
     * @param string $name        Overrides the attachment name
     * @param string $encoding    File encoding (see $Encoding)
     * @param string $type        File extension (MIME) type
     * @param string $disposition Disposition to use
     *
     * @throws Exception
     *
     * @return bool
     */
    public function addAttachment($path, $name = &apos;&apos;, $encoding = self::ENCODING_BASE64, $type = &apos;&apos;, $disposition = &apos;attachment&apos;)
    {
        try {
            if (!static::isPermittedPath($path) || !@is_file($path)) {
                throw new Exception($this&#45;>lang(&apos;file_access&apos;) . $path, self::STOP_CONTINUE);
            }

            // If a MIME type is not specified, try to work it out from the file name
            if (&apos;&apos; == $type) {
                $type = static::filenameToType($path);
            }

            $filename = basename($path);
            if (&apos;&apos; == $name) {
                $name = $filename;
            }

            $this&#45;>attachment[] = [
                0 => $path,
                1 => $filename,
                2 => $name,
                3 => $encoding,
                4 => $type,
                5 => false, // isStringAttachment
                6 => $disposition,
                7 => $name,
            ];
        } catch (Exception $exc) {
            $this&#45;>setError($exc&#45;>getMessage());
            $this&#45;>edebug($exc&#45;>getMessage());
            if ($this&#45;>exceptions) {
                throw $exc;
            }

            return false;
        }

        return true;
    }

    /**
     * Return the array of attachments.
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this&#45;>attachment;
    }

    /**
     * Attach all file, string, and binary attachments to the message.
     * Returns an empty string on failure.
     *
     * @param string $disposition_type
     * @param string $boundary
     *
     * @return string
     */
    protected function attachAll($disposition_type, $boundary)
    {
        // Return text of body
        $mime = [];
        $cidUniq = [];
        $incl = [];

        // Add all attachments
        foreach ($this&#45;>attachment as $attachment) {
            // Check if it is a valid disposition_filter
            if ($attachment[6] == $disposition_type) {
                // Check for string attachment
                $string = &apos;&apos;;
                $path = &apos;&apos;;
                $bString = $attachment[5];
                if ($bString) {
                    $string = $attachment[0];
                } else {
                    $path = $attachment[0];
                }

                $inclhash = hash(&apos;sha256&apos;, serialize($attachment));
                if (in_array($inclhash, $incl)) {
                    continue;
                }
                $incl[] = $inclhash;
                $name = $attachment[2];
                $encoding = $attachment[3];
                $type = $attachment[4];
                $disposition = $attachment[6];
                $cid = $attachment[7];
                if (&apos;inline&apos; == $disposition and array_key_exists($cid, $cidUniq)) {
                    continue;
                }
                $cidUniq[$cid] = true;

                $mime[] = sprintf(&apos;&#45;&#45;%s%s&apos;, $boundary, static::$LE);
                //Only include a filename property if we have one
                if (!empty($name)) {
                    $mime[] = sprintf(
                        &apos;Content&#45;Type: %s; name="%s"%s&apos;,
                        $type,
                        $this&#45;>encodeHeader($this&#45;>secureHeader($name)),
                        static::$LE
                    );
                } else {
                    $mime[] = sprintf(
                        &apos;Content&#45;Type: %s%s&apos;,
                        $type,
                        static::$LE
                    );
                }
                // RFC1341 part 5 says 7bit is assumed if not specified
                if (static::ENCODING_7BIT != $encoding) {
                    $mime[] = sprintf(&apos;Content&#45;Transfer&#45;Encoding: %s%s&apos;, $encoding, static::$LE);
                }

                if (!empty($cid)) {
                    $mime[] = sprintf(&apos;Content&#45;ID: <%s>%s&apos;, $cid, static::$LE);
                }

                // If a filename contains any of these chars, it should be quoted,
                // but not otherwise: RFC2183 & RFC2045 5.1
                // Fixes a warning in IETF&apos;s msglint MIME checker
                // Allow for bypassing the Content&#45;Disposition header totally
                if (!(empty($disposition))) {
                    $encoded_name = $this&#45;>encodeHeader($this&#45;>secureHeader($name));
                    if (preg_match(&apos;/[ \(\)<>@,;:\\"\/\[\]\?=]/&apos;, $encoded_name)) {
                        $mime[] = sprintf(
                            &apos;Content&#45;Disposition: %s; filename="%s"%s&apos;,
                            $disposition,
                            $encoded_name,
                            static::$LE . static::$LE
                        );
                    } else {
                        if (!empty($encoded_name)) {
                            $mime[] = sprintf(
                                &apos;Content&#45;Disposition: %s; filename=%s%s&apos;,
                                $disposition,
                                $encoded_name,
                                static::$LE . static::$LE
                            );
                        } else {
                            $mime[] = sprintf(
                                &apos;Content&#45;Disposition: %s%s&apos;,
                                $disposition,
                                static::$LE . static::$LE
                            );
                        }
                    }
                } else {
                    $mime[] = static::$LE;
                }

                // Encode as string attachment
                if ($bString) {
                    $mime[] = $this&#45;>encodeString($string, $encoding);
                } else {
                    $mime[] = $this&#45;>encodeFile($path, $encoding);
                }
                if ($this&#45;>isError()) {
                    return &apos;&apos;;
                }
                $mime[] = static::$LE;
            }
        }

        $mime[] = sprintf(&apos;&#45;&#45;%s&#45;&#45;%s&apos;, $boundary, static::$LE);

        return implode(&apos;&apos;, $mime);
    }

    /**
     * Encode a file attachment in requested format.
     * Returns an empty string on failure.
     *
     * @param string $path     The full path to the file
     * @param string $encoding The encoding to use; one of &apos;base64&apos;, &apos;7bit&apos;, &apos;8bit&apos;, &apos;binary&apos;, &apos;quoted&#45;printable&apos;
     *
     * @throws Exception
     *
     * @return string
     */
    protected function encodeFile($path, $encoding = self::ENCODING_BASE64)
    {
        try {
            if (!static::isPermittedPath($path) || !file_exists($path)) {
                throw new Exception($this&#45;>lang(&apos;file_open&apos;) . $path, self::STOP_CONTINUE);
            }
            $file_buffer = file_get_contents($path);
            if (false === $file_buffer) {
                throw new Exception($this&#45;>lang(&apos;file_open&apos;) . $path, self::STOP_CONTINUE);
            }
            $file_buffer = $this&#45;>encodeString($file_buffer, $encoding);

            return $file_buffer;
        } catch (Exception $exc) {
            $this&#45;>setError($exc&#45;>getMessage());

            return &apos;&apos;;
        }
    }

    /**
     * Encode a string in requested format.
     * Returns an empty string on failure.
     *
     * @param string $str      The text to encode
     * @param string $encoding The encoding to use; one of &apos;base64&apos;, &apos;7bit&apos;, &apos;8bit&apos;, &apos;binary&apos;, &apos;quoted&#45;printable&apos;
     *
     * @return string
     */
    public function encodeString($str, $encoding = self::ENCODING_BASE64)
    {
        $encoded = &apos;&apos;;
        switch (strtolower($encoding)) {
            case static::ENCODING_BASE64:
                $encoded = chunk_split(
                    base64_encode($str),
                    static::STD_LINE_LENGTH,
                    static::$LE
                );
                break;
            case static::ENCODING_7BIT:
            case static::ENCODING_8BIT:
                $encoded = static::normalizeBreaks($str);
                // Make sure it ends with a line break
                if (substr($encoded, &#45;(strlen(static::$LE))) != static::$LE) {
                    $encoded .= static::$LE;
                }
                break;
            case static::ENCODING_BINARY:
                $encoded = $str;
                break;
            case static::ENCODING_QUOTED_PRINTABLE:
                $encoded = $this&#45;>encodeQP($str);
                break;
            default:
                $this&#45;>setError($this&#45;>lang(&apos;encoding&apos;) . $encoding);
                break;
        }

        return $encoded;
    }

    /**
     * Encode a header value (not including its label) optimally.
     * Picks shortest of Q, B, or none. Result includes folding if needed.
     * See RFC822 definitions for phrase, comment and text positions.
     *
     * @param string $str      The header value to encode
     * @param string $position What context the string will be used in
     *
     * @return string
     */
    public function encodeHeader($str, $position = &apos;text&apos;)
    {
        $matchcount = 0;
        switch (strtolower($position)) {
            case &apos;phrase&apos;:
                if (!preg_match(&apos;/[\200&#45;\377]/&apos;, $str)) {
                    // Can&apos;t use addslashes as we don&apos;t know the value of magic_quotes_sybase
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str == $encoded) and !preg_match(&apos;/[^A&#45;Za&#45;z0&#45;9!#$%&\&apos;*+\/=?^_`{|}~ &#45;]/&apos;, $str)) {
                        return $encoded;
                    }

                    return "\"$encoded\"";
                }
                $matchcount = preg_match_all(&apos;/[^\040\041\043&#45;\133\135&#45;\176]/&apos;, $str, $matches);
                break;
            /* @noinspection PhpMissingBreakStatementInspection */
            case &apos;comment&apos;:
                $matchcount = preg_match_all(&apos;/[()"]/&apos;, $str, $matches);
            //fallthrough
            case &apos;text&apos;:
            default:
                $matchcount += preg_match_all(&apos;/[\000&#45;\010\013\014\016&#45;\037\177&#45;\377]/&apos;, $str, $matches);
                break;
        }

        //RFCs specify a maximum line length of 78 chars, however mail() will sometimes
        //corrupt messages with headers longer than 65 chars. See #818
        $lengthsub = &apos;mail&apos; == $this&#45;>Mailer ? 13 : 0;
        $maxlen = static::STD_LINE_LENGTH &#45; $lengthsub;
        // Try to select the encoding which should produce the shortest output
        if ($matchcount > strlen($str) / 3) {
            // More than a third of the content will need encoding, so B encoding will be most efficient
            $encoding = &apos;B&apos;;
            //This calculation is:
            // max line length
            // &#45; shorten to avoid mail() corruption
            // &#45; Q/B encoding char overhead ("` =?<charset>?[QB]?<content>?=`")
            // &#45; charset name length
            $maxlen = static::STD_LINE_LENGTH &#45; $lengthsub &#45; 8 &#45; strlen($this&#45;>CharSet);
            if ($this&#45;>hasMultiBytes($str)) {
                // Use a custom function which correctly encodes and wraps long
                // multibyte strings without breaking lines within a character
                $encoded = $this&#45;>base64EncodeWrapMB($str, "\n");
            } else {
                $encoded = base64_encode($str);
                $maxlen &#45;= $maxlen % 4;
                $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
            }
            $encoded = preg_replace(&apos;/^(.*)$/m&apos;, &apos; =?&apos; . $this&#45;>CharSet . "?$encoding?\\1?=", $encoded);
        } elseif ($matchcount > 0) {
            //1 or more chars need encoding, use Q&#45;encode
            $encoding = &apos;Q&apos;;
            //Recalc max line length for Q encoding &#45; see comments on B encode
            $maxlen = static::STD_LINE_LENGTH &#45; $lengthsub &#45; 8 &#45; strlen($this&#45;>CharSet);
            $encoded = $this&#45;>encodeQ($str, $position);
            $encoded = $this&#45;>wrapText($encoded, $maxlen, true);
            $encoded = str_replace(&apos;=&apos; . static::$LE, "\n", trim($encoded));
            $encoded = preg_replace(&apos;/^(.*)$/m&apos;, &apos; =?&apos; . $this&#45;>CharSet . "?$encoding?\\1?=", $encoded);
        } elseif (strlen($str) > $maxlen) {
            //No chars need encoding, but line is too long, so fold it
            $encoded = trim($this&#45;>wrapText($str, $maxlen, false));
            if ($str == $encoded) {
                //Wrapping nicely didn&apos;t work, wrap hard instead
                $encoded = trim(chunk_split($str, static::STD_LINE_LENGTH, static::$LE));
            }
            $encoded = str_replace(static::$LE, "\n", trim($encoded));
            $encoded = preg_replace(&apos;/^(.*)$/m&apos;, &apos; \\1&apos;, $encoded);
        } else {
            //No reformatting needed
            return $str;
        }

        return trim(static::normalizeBreaks($encoded));
    }

    /**
     * Check if a string contains multi&#45;byte characters.
     *
     * @param string $str multi&#45;byte text to wrap encode
     *
     * @return bool
     */
    public function hasMultiBytes($str)
    {
        if (function_exists(&apos;mb_strlen&apos;)) {
            return strlen($str) > mb_strlen($str, $this&#45;>CharSet);
        }

        // Assume no multibytes (we can&apos;t handle without mbstring functions anyway)
        return false;
    }

    /**
     * Does a string contain any 8&#45;bit chars (in any charset)?
     *
     * @param string $text
     *
     * @return bool
     */
    public function has8bitChars($text)
    {
        return (bool) preg_match(&apos;/[\x80&#45;\xFF]/&apos;, $text);
    }

    /**
     * Encode and wrap long multibyte strings for mail headers
     * without breaking lines within a character.
     * Adapted from a function by paravoid.
     *
     * @see http://www.php.net/manual/en/function.mb&#45;encode&#45;mimeheader.php#60283
     *
     * @param string $str       multi&#45;byte text to wrap encode
     * @param string $linebreak string to use as linefeed/end&#45;of&#45;line
     *
     * @return string
     */
    public function base64EncodeWrapMB($str, $linebreak = null)
    {
        $start = &apos;=?&apos; . $this&#45;>CharSet . &apos;?B?&apos;;
        $end = &apos;?=&apos;;
        $encoded = &apos;&apos;;
        if (null === $linebreak) {
            $linebreak = static::$LE;
        }

        $mb_length = mb_strlen($str, $this&#45;>CharSet);
        // Each line must have length <= 75, including $start and $end
        $length = 75 &#45; strlen($start) &#45; strlen($end);
        // Average multi&#45;byte ratio
        $ratio = $mb_length / strlen($str);
        // Base64 has a 4:3 ratio
        $avgLength = floor($length * $ratio * .75);

        for ($i = 0; $i < $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength &#45; $lookBack;
                $chunk = mb_substr($str, $i, $offset, $this&#45;>CharSet);
                $chunk = base64_encode($chunk);
                ++$lookBack;
            } while (strlen($chunk) > $length);
            $encoded .= $chunk . $linebreak;
        }

        // Chomp the last linefeed
        return substr($encoded, 0, &#45;strlen($linebreak));
    }

    /**
     * Encode a string in quoted&#45;printable format.
     * According to RFC2045 section 6.7.
     *
     * @param string $string The text to encode
     *
     * @return string
     */
    public function encodeQP($string)
    {
        return static::normalizeBreaks(quoted_printable_encode($string));
    }

    /**
     * Encode a string using Q encoding.
     *
     * @see http://tools.ietf.org/html/rfc2047#section&#45;4.2
     *
     * @param string $str      the text to encode
     * @param string $position Where the text is going to be used, see the RFC for what that means
     *
     * @return string
     */
    public function encodeQ($str, $position = &apos;text&apos;)
    {
        // There should not be any EOL in the string
        $pattern = &apos;&apos;;
        $encoded = str_replace(["\r", "\n"], &apos;&apos;, $str);
        switch (strtolower($position)) {
            case &apos;phrase&apos;:
                // RFC 2047 section 5.3
                $pattern = &apos;^A&#45;Za&#45;z0&#45;9!*+\/ &#45;&apos;;
                break;
            /*
             * RFC 2047 section 5.2.
             * Build $pattern without including delimiters and []
             */
            /* @noinspection PhpMissingBreakStatementInspection */
            case &apos;comment&apos;:
                $pattern = &apos;\(\)"&apos;;
            /* Intentional fall through */
            case &apos;text&apos;:
            default:
                // RFC 2047 section 5.1
                // Replace every high ascii, control, =, ? and _ characters
                /** @noinspection SuspiciousAssignmentsInspection */
                $pattern = &apos;\000&#45;\011\013\014\016&#45;\037\075\077\137\177&#45;\377&apos; . $pattern;
                break;
        }
        $matches = [];
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            // If the string contains an &apos;=&apos;, make sure it&apos;s the first thing we replace
            // so as to avoid double&#45;encoding
            $eqkey = array_search(&apos;=&apos;, $matches[0]);
            if (false !== $eqkey) {
                unset($matches[0][$eqkey]);
                array_unshift($matches[0], &apos;=&apos;);
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, &apos;=&apos; . sprintf(&apos;%02X&apos;, ord($char)), $encoded);
            }
        }
        // Replace spaces with _ (more readable than =20)
        // RFC 2047 section 4.2(2)
        return str_replace(&apos; &apos;, &apos;_&apos;, $encoded);
    }

    /**
     * Add a string or binary attachment (non&#45;filesystem).
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     *
     * @param string $string      String attachment data
     * @param string $filename    Name of the attachment
     * @param string $encoding    File encoding (see $Encoding)
     * @param string $type        File extension (MIME) type
     * @param string $disposition Disposition to use
     */
    public function addStringAttachment(
        $string,
        $filename,
        $encoding = self::ENCODING_BASE64,
        $type = &apos;&apos;,
        $disposition = &apos;attachment&apos;
    ) {
        // If a MIME type is not specified, try to work it out from the file name
        if (&apos;&apos; == $type) {
            $type = static::filenameToType($filename);
        }
        // Append to $attachment array
        $this&#45;>attachment[] = [
            0 => $string,
            1 => $filename,
            2 => basename($filename),
            3 => $encoding,
            4 => $type,
            5 => true, // isStringAttachment
            6 => $disposition,
            7 => 0,
        ];
    }

    /**
     * Add an embedded (inline) attachment from a file.
     * This can include images, sounds, and just about any other document type.
     * These differ from &apos;regular&apos; attachments in that they are intended to be
     * displayed inline with the message, not just attached for download.
     * This is used in HTML messages that embed the images
     * the HTML refers to using the $cid value.
     * Never use a user&#45;supplied path to a file!
     *
     * @param string $path        Path to the attachment
     * @param string $cid         Content ID of the attachment; Use this to reference
     *                            the content when using an embedded image in HTML
     * @param string $name        Overrides the attachment name
     * @param string $encoding    File encoding (see $Encoding)
     * @param string $type        File MIME type
     * @param string $disposition Disposition to use
     *
     * @return bool True on successfully adding an attachment
     */
    public function addEmbeddedImage($path, $cid, $name = &apos;&apos;, $encoding = self::ENCODING_BASE64, $type = &apos;&apos;, $disposition = &apos;inline&apos;)
    {
        if (!static::isPermittedPath($path) || !@is_file($path)) {
            $this&#45;>setError($this&#45;>lang(&apos;file_access&apos;) . $path);

            return false;
        }

        // If a MIME type is not specified, try to work it out from the file name
        if (&apos;&apos; == $type) {
            $type = static::filenameToType($path);
        }

        $filename = basename($path);
        if (&apos;&apos; == $name) {
            $name = $filename;
        }

        // Append to $attachment array
        $this&#45;>attachment[] = [
            0 => $path,
            1 => $filename,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => false, // isStringAttachment
            6 => $disposition,
            7 => $cid,
        ];

        return true;
    }

    /**
     * Add an embedded stringified attachment.
     * This can include images, sounds, and just about any other document type.
     * If your filename doesn&apos;t contain an extension, be sure to set the $type to an appropriate MIME type.
     *
     * @param string $string      The attachment binary data
     * @param string $cid         Content ID of the attachment; Use this to reference
     *                            the content when using an embedded image in HTML
     * @param string $name        A filename for the attachment. If this contains an extension,
     *                            PHPMailer will attempt to set a MIME type for the attachment.
     *                            For example &apos;file.jpg&apos; would get an &apos;image/jpeg&apos; MIME type.
     * @param string $encoding    File encoding (see $Encoding), defaults to &apos;base64&apos;
     * @param string $type        MIME type &#45; will be used in preference to any automatically derived type
     * @param string $disposition Disposition to use
     *
     * @return bool True on successfully adding an attachment
     */
    public function addStringEmbeddedImage(
        $string,
        $cid,
        $name = &apos;&apos;,
        $encoding = self::ENCODING_BASE64,
        $type = &apos;&apos;,
        $disposition = &apos;inline&apos;
    ) {
        // If a MIME type is not specified, try to work it out from the name
        if (&apos;&apos; == $type and !empty($name)) {
            $type = static::filenameToType($name);
        }

        // Append to $attachment array
        $this&#45;>attachment[] = [
            0 => $string,
            1 => $name,
            2 => $name,
            3 => $encoding,
            4 => $type,
            5 => true, // isStringAttachment
            6 => $disposition,
            7 => $cid,
        ];

        return true;
    }

    /**
     * Check if an embedded attachment is present with this cid.
     *
     * @param string $cid
     *
     * @return bool
     */
    protected function cidExists($cid)
    {
        foreach ($this&#45;>attachment as $attachment) {
            if (&apos;inline&apos; == $attachment[6] and $cid == $attachment[7]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an inline attachment is present.
     *
     * @return bool
     */
    public function inlineImageExists()
    {
        foreach ($this&#45;>attachment as $attachment) {
            if (&apos;inline&apos; == $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an attachment (non&#45;inline) is present.
     *
     * @return bool
     */
    public function attachmentExists()
    {
        foreach ($this&#45;>attachment as $attachment) {
            if (&apos;attachment&apos; == $attachment[6]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if this message has an alternative body set.
     *
     * @return bool
     */
    public function alternativeExists()
    {
        return !empty($this&#45;>AltBody);
    }

    /**
     * Clear queued addresses of given kind.
     *
     * @param string $kind &apos;to&apos;, &apos;cc&apos;, or &apos;bcc&apos;
     */
    public function clearQueuedAddresses($kind)
    {
        $this&#45;>RecipientsQueue = array_filter(
            $this&#45;>RecipientsQueue,
            function ($params) use ($kind) {
                return $params[0] != $kind;
            }
        );
    }

    /**
     * Clear all To recipients.
     */
    public function clearAddresses()
    {
        foreach ($this&#45;>to as $to) {
            unset($this&#45;>all_recipients[strtolower($to[0])]);
        }
        $this&#45;>to = [];
        $this&#45;>clearQueuedAddresses(&apos;to&apos;);
    }

    /**
     * Clear all CC recipients.
     */
    public function clearCCs()
    {
        foreach ($this&#45;>cc as $cc) {
            unset($this&#45;>all_recipients[strtolower($cc[0])]);
        }
        $this&#45;>cc = [];
        $this&#45;>clearQueuedAddresses(&apos;cc&apos;);
    }

    /**
     * Clear all BCC recipients.
     */
    public function clearBCCs()
    {
        foreach ($this&#45;>bcc as $bcc) {
            unset($this&#45;>all_recipients[strtolower($bcc[0])]);
        }
        $this&#45;>bcc = [];
        $this&#45;>clearQueuedAddresses(&apos;bcc&apos;);
    }

    /**
     * Clear all ReplyTo recipients.
     */
    public function clearReplyTos()
    {
        $this&#45;>ReplyTo = [];
        $this&#45;>ReplyToQueue = [];
    }

    /**
     * Clear all recipient types.
     */
    public function clearAllRecipients()
    {
        $this&#45;>to = [];
        $this&#45;>cc = [];
        $this&#45;>bcc = [];
        $this&#45;>all_recipients = [];
        $this&#45;>RecipientsQueue = [];
    }

    /**
     * Clear all filesystem, string, and binary attachments.
     */
    public function clearAttachments()
    {
        $this&#45;>attachment = [];
    }

    /**
     * Clear all custom headers.
     */
    public function clearCustomHeaders()
    {
        $this&#45;>CustomHeader = [];
    }

    /**
     * Add an error message to the error container.
     *
     * @param string $msg
     */
    protected function setError($msg)
    {
        ++$this&#45;>error_count;
        if (&apos;smtp&apos; == $this&#45;>Mailer and null !== $this&#45;>smtp) {
            $lasterror = $this&#45;>smtp&#45;>getError();
            if (!empty($lasterror[&apos;error&apos;])) {
                $msg .= $this&#45;>lang(&apos;smtp_error&apos;) . $lasterror[&apos;error&apos;];
                if (!empty($lasterror[&apos;detail&apos;])) {
                    $msg .= &apos; Detail: &apos; . $lasterror[&apos;detail&apos;];
                }
                if (!empty($lasterror[&apos;smtp_code&apos;])) {
                    $msg .= &apos; SMTP code: &apos; . $lasterror[&apos;smtp_code&apos;];
                }
                if (!empty($lasterror[&apos;smtp_code_ex&apos;])) {
                    $msg .= &apos; Additional SMTP info: &apos; . $lasterror[&apos;smtp_code_ex&apos;];
                }
            }
        }
        $this&#45;>ErrorInfo = $msg;
    }

    /**
     * Return an RFC 822 formatted date.
     *
     * @return string
     */
    public static function rfcDate()
    {
        // Set the time zone to whatever the default is to avoid 500 errors
        // Will default to UTC if it&apos;s not set properly in php.ini
        date_default_timezone_set(@date_default_timezone_get());

        return date(&apos;D, j M Y H:i:s O&apos;);
    }

    /**
     * Get the server hostname.
     * Returns &apos;localhost.localdomain&apos; if unknown.
     *
     * @return string
     */
    protected function serverHostname()
    {
        $result = &apos;&apos;;
        if (!empty($this&#45;>Hostname)) {
            $result = $this&#45;>Hostname;
        } elseif (isset($_SERVER) and array_key_exists(&apos;SERVER_NAME&apos;, $_SERVER)) {
            $result = $_SERVER[&apos;SERVER_NAME&apos;];
        } elseif (function_exists(&apos;gethostname&apos;) and gethostname() !== false) {
            $result = gethostname();
        } elseif (php_uname(&apos;n&apos;) !== false) {
            $result = php_uname(&apos;n&apos;);
        }
        if (!static::isValidHost($result)) {
            return &apos;localhost.localdomain&apos;;
        }

        return $result;
    }

    /**
     * Validate whether a string contains a valid value to use as a hostname or IP address.
     * IPv6 addresses must include [], e.g. `[::1]`, not just `::1`.
     *
     * @param string $host The host name or IP address to check
     *
     * @return bool
     */
    public static function isValidHost($host)
    {
        //Simple syntax limits
        if (empty($host)
            or !is_string($host)
            or strlen($host) > 256
        ) {
            return false;
        }
        //Looks like a bracketed IPv6 address
        if (trim($host, &apos;[]&apos;) != $host) {
            return (bool) filter_var(trim($host, &apos;[]&apos;), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }
        //If removing all the dots results in a numeric string, it must be an IPv4 address.
        //Need to check this first because otherwise things like `999.0.0.0` are considered valid host names
        if (is_numeric(str_replace(&apos;.&apos;, &apos;&apos;, $host))) {
            //Is it a valid IPv4 address?
            return (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        if (filter_var(&apos;http://&apos; . $host, FILTER_VALIDATE_URL)) {
            //Is it a syntactically valid hostname?
            return true;
        }

        return false;
    }

    /**
     * Get an error message in the current language.
     *
     * @param string $key
     *
     * @return string
     */
    protected function lang($key)
    {
        if (count($this&#45;>language) < 1) {
            $this&#45;>setLanguage(&apos;en&apos;); // set the default language
        }

        if (array_key_exists($key, $this&#45;>language)) {
            if (&apos;smtp_connect_failed&apos; == $key) {
                //Include a link to troubleshooting docs on SMTP connection failure
                //this is by far the biggest cause of support questions
                //but it&apos;s usually not PHPMailer&apos;s fault.
                return $this&#45;>language[$key] . &apos; https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting&apos;;
            }

            return $this&#45;>language[$key];
        }

        //Return the key as a fallback
        return $key;
    }

    /**
     * Check if an error occurred.
     *
     * @return bool True if an error did occur
     */
    public function isError()
    {
        return $this&#45;>error_count > 0;
    }

    /**
     * Add a custom header.
     * $name value can be overloaded to contain
     * both header name and value (name:value).
     *
     * @param string      $name  Custom header name
     * @param string|null $value Header value
     */
    public function addCustomHeader($name, $value = null)
    {
        if (null === $value) {
            // Value passed in as name:value
            $this&#45;>CustomHeader[] = explode(&apos;:&apos;, $name, 2);
        } else {
            $this&#45;>CustomHeader[] = [$name, $value];
        }
    }

    /**
     * Returns all custom headers.
     *
     * @return array
     */
    public function getCustomHeaders()
    {
        return $this&#45;>CustomHeader;
    }

    /**
     * Create a message body from an HTML string.
     * Automatically inlines images and creates a plain&#45;text version by converting the HTML,
     * overwriting any existing values in Body and AltBody.
     * Do not source $message content from user input!
     * $basedir is prepended when handling relative URLs, e.g. <img src="/images/a.png"> and must not be empty
     * will look for an image file in $basedir/images/a.png and convert it to inline.
     * If you don&apos;t provide a $basedir, relative paths will be left untouched (and thus probably break in email)
     * Converts data&#45;uri images into embedded attachments.
     * If you don&apos;t want to apply these transformations to your HTML, just set Body and AltBody directly.
     *
     * @param string        $message  HTML message string
     * @param string        $basedir  Absolute path to a base directory to prepend to relative paths to images
     * @param bool|callable $advanced Whether to use the internal HTML to text converter
     *                                or your own custom converter @see PHPMailer::html2text()
     *
     * @return string $message The transformed message Body
     */
    public function msgHTML($message, $basedir = &apos;&apos;, $advanced = false)
    {
        preg_match_all(&apos;/(src|background)=["\&apos;](.*)["\&apos;]/Ui&apos;, $message, $images);
        if (array_key_exists(2, $images)) {
            if (strlen($basedir) > 1 && &apos;/&apos; != substr($basedir, &#45;1)) {
                // Ensure $basedir has a trailing /
                $basedir .= &apos;/&apos;;
            }
            foreach ($images[2] as $imgindex => $url) {
                // Convert data URIs into embedded images
                //e.g. "data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="
                if (preg_match(&apos;#^data:(image/(?:jpe?g|gif|png));?(base64)?,(.+)#&apos;, $url, $match)) {
                    if (count($match) == 4 and static::ENCODING_BASE64 == $match[2]) {
                        $data = base64_decode($match[3]);
                    } elseif (&apos;&apos; == $match[2]) {
                        $data = rawurldecode($match[3]);
                    } else {
                        //Not recognised so leave it alone
                        continue;
                    }
                    //Hash the decoded data, not the URL so that the same data&#45;URI image used in multiple places
                    //will only be embedded once, even if it used a different encoding
                    $cid = hash(&apos;sha256&apos;, $data) . &apos;@phpmailer.0&apos;; // RFC2392 S 2

                    if (!$this&#45;>cidExists($cid)) {
                        $this&#45;>addStringEmbeddedImage($data, $cid, &apos;embed&apos; . $imgindex, static::ENCODING_BASE64, $match[1]);
                    }
                    $message = str_replace(
                        $images[0][$imgindex],
                        $images[1][$imgindex] . &apos;="cid:&apos; . $cid . &apos;"&apos;,
                        $message
                    );
                    continue;
                }
                if (// Only process relative URLs if a basedir is provided (i.e. no absolute local paths)
                    !empty($basedir)
                    // Ignore URLs containing parent dir traversal (..)
                    and (strpos($url, &apos;..&apos;) === false)
                    // Do not change urls that are already inline images
                    and 0 !== strpos($url, &apos;cid:&apos;)
                    // Do not change absolute URLs, including anonymous protocol
                    and !preg_match(&apos;#^[a&#45;z][a&#45;z0&#45;9+.&#45;]*:?//#i&apos;, $url)
                ) {
                    $filename = basename($url);
                    $directory = dirname($url);
                    if (&apos;.&apos; == $directory) {
                        $directory = &apos;&apos;;
                    }
                    $cid = hash(&apos;sha256&apos;, $url) . &apos;@phpmailer.0&apos;; // RFC2392 S 2
                    if (strlen($basedir) > 1 and &apos;/&apos; != substr($basedir, &#45;1)) {
                        $basedir .= &apos;/&apos;;
                    }
                    if (strlen($directory) > 1 and &apos;/&apos; != substr($directory, &#45;1)) {
                        $directory .= &apos;/&apos;;
                    }
                    if ($this&#45;>addEmbeddedImage(
                        $basedir . $directory . $filename,
                        $cid,
                        $filename,
                        static::ENCODING_BASE64,
                        static::_mime_types((string) static::mb_pathinfo($filename, PATHINFO_EXTENSION))
                    )
                    ) {
                        $message = preg_replace(
                            &apos;/&apos; . $images[1][$imgindex] . &apos;=["\&apos;]&apos; . preg_quote($url, &apos;/&apos;) . &apos;["\&apos;]/Ui&apos;,
                            $images[1][$imgindex] . &apos;="cid:&apos; . $cid . &apos;"&apos;,
                            $message
                        );
                    }
                }
            }
        }
        $this&#45;>isHTML(true);
        // Convert all message body line breaks to LE, makes quoted&#45;printable encoding work much better
        $this&#45;>Body = static::normalizeBreaks($message);
        $this&#45;>AltBody = static::normalizeBreaks($this&#45;>html2text($message, $advanced));
        if (!$this&#45;>alternativeExists()) {
            $this&#45;>AltBody = &apos;This is an HTML&#45;only message. To view it, activate HTML in your email application.&apos;
                . static::$LE;
        }

        return $this&#45;>Body;
    }

    /**
     * Convert an HTML string into plain text.
     * This is used by msgHTML().
     * Note &#45; older versions of this function used a bundled advanced converter
     * which was removed for license reasons in #232.
     * Example usage:
     *
     * ```php
     * // Use default conversion
     * $plain = $mail&#45;>html2text($html);
     * // Use your own custom converter
     * $plain = $mail&#45;>html2text($html, function($html) {
     *     $converter = new MyHtml2text($html);
     *     return $converter&#45;>get_text();
     * });
     * ```
     *
     * @param string        $html     The HTML text to convert
     * @param bool|callable $advanced Any boolean value to use the internal converter,
     *                                or provide your own callable for custom conversion
     *
     * @return string
     */
    public function html2text($html, $advanced = false)
    {
        if (is_callable($advanced)) {
            return call_user_func($advanced, $html);
        }

        return html_entity_decode(
            trim(strip_tags(preg_replace(&apos;/<(head|title|style|script)[^>]*>.*?<\/\\1>/si&apos;, &apos;&apos;, $html))),
            ENT_QUOTES,
            $this&#45;>CharSet
        );
    }

    /**
     * Get the MIME type for a file extension.
     *
     * @param string $ext File extension
     *
     * @return string MIME type of file
     */
    public static function _mime_types($ext = &apos;&apos;)
    {
        $mimes = [
            &apos;xl&apos; => &apos;application/excel&apos;,
            &apos;js&apos; => &apos;application/javascript&apos;,
            &apos;hqx&apos; => &apos;application/mac&#45;binhex40&apos;,
            &apos;cpt&apos; => &apos;application/mac&#45;compactpro&apos;,
            &apos;bin&apos; => &apos;application/macbinary&apos;,
            &apos;doc&apos; => &apos;application/msword&apos;,
            &apos;word&apos; => &apos;application/msword&apos;,
            &apos;xlsx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.spreadsheetml.sheet&apos;,
            &apos;xltx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.spreadsheetml.template&apos;,
            &apos;potx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.presentationml.template&apos;,
            &apos;ppsx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.presentationml.slideshow&apos;,
            &apos;pptx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.presentationml.presentation&apos;,
            &apos;sldx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.presentationml.slide&apos;,
            &apos;docx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.wordprocessingml.document&apos;,
            &apos;dotx&apos; => &apos;application/vnd.openxmlformats&#45;officedocument.wordprocessingml.template&apos;,
            &apos;xlam&apos; => &apos;application/vnd.ms&#45;excel.addin.macroEnabled.12&apos;,
            &apos;xlsb&apos; => &apos;application/vnd.ms&#45;excel.sheet.binary.macroEnabled.12&apos;,
            &apos;class&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;dll&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;dms&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;exe&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;lha&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;lzh&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;psd&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;sea&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;so&apos; => &apos;application/octet&#45;stream&apos;,
            &apos;oda&apos; => &apos;application/oda&apos;,
            &apos;pdf&apos; => &apos;application/pdf&apos;,
            &apos;ai&apos; => &apos;application/postscript&apos;,
            &apos;eps&apos; => &apos;application/postscript&apos;,
            &apos;ps&apos; => &apos;application/postscript&apos;,
            &apos;smi&apos; => &apos;application/smil&apos;,
            &apos;smil&apos; => &apos;application/smil&apos;,
            &apos;mif&apos; => &apos;application/vnd.mif&apos;,
            &apos;xls&apos; => &apos;application/vnd.ms&#45;excel&apos;,
            &apos;ppt&apos; => &apos;application/vnd.ms&#45;powerpoint&apos;,
            &apos;wbxml&apos; => &apos;application/vnd.wap.wbxml&apos;,
            &apos;wmlc&apos; => &apos;application/vnd.wap.wmlc&apos;,
            &apos;dcr&apos; => &apos;application/x&#45;director&apos;,
            &apos;dir&apos; => &apos;application/x&#45;director&apos;,
            &apos;dxr&apos; => &apos;application/x&#45;director&apos;,
            &apos;dvi&apos; => &apos;application/x&#45;dvi&apos;,
            &apos;gtar&apos; => &apos;application/x&#45;gtar&apos;,
            &apos;php3&apos; => &apos;application/x&#45;httpd&#45;php&apos;,
            &apos;php4&apos; => &apos;application/x&#45;httpd&#45;php&apos;,
            &apos;php&apos; => &apos;application/x&#45;httpd&#45;php&apos;,
            &apos;phtml&apos; => &apos;application/x&#45;httpd&#45;php&apos;,
            &apos;phps&apos; => &apos;application/x&#45;httpd&#45;php&#45;source&apos;,
            &apos;swf&apos; => &apos;application/x&#45;shockwave&#45;flash&apos;,
            &apos;sit&apos; => &apos;application/x&#45;stuffit&apos;,
            &apos;tar&apos; => &apos;application/x&#45;tar&apos;,
            &apos;tgz&apos; => &apos;application/x&#45;tar&apos;,
            &apos;xht&apos; => &apos;application/xhtml+xml&apos;,
            &apos;xhtml&apos; => &apos;application/xhtml+xml&apos;,
            &apos;zip&apos; => &apos;application/zip&apos;,
            &apos;mid&apos; => &apos;audio/midi&apos;,
            &apos;midi&apos; => &apos;audio/midi&apos;,
            &apos;mp2&apos; => &apos;audio/mpeg&apos;,
            &apos;mp3&apos; => &apos;audio/mpeg&apos;,
            &apos;m4a&apos; => &apos;audio/mp4&apos;,
            &apos;mpga&apos; => &apos;audio/mpeg&apos;,
            &apos;aif&apos; => &apos;audio/x&#45;aiff&apos;,
            &apos;aifc&apos; => &apos;audio/x&#45;aiff&apos;,
            &apos;aiff&apos; => &apos;audio/x&#45;aiff&apos;,
            &apos;ram&apos; => &apos;audio/x&#45;pn&#45;realaudio&apos;,
            &apos;rm&apos; => &apos;audio/x&#45;pn&#45;realaudio&apos;,
            &apos;rpm&apos; => &apos;audio/x&#45;pn&#45;realaudio&#45;plugin&apos;,
            &apos;ra&apos; => &apos;audio/x&#45;realaudio&apos;,
            &apos;wav&apos; => &apos;audio/x&#45;wav&apos;,
            &apos;mka&apos; => &apos;audio/x&#45;matroska&apos;,
            &apos;bmp&apos; => &apos;image/bmp&apos;,
            &apos;gif&apos; => &apos;image/gif&apos;,
            &apos;jpeg&apos; => &apos;image/jpeg&apos;,
            &apos;jpe&apos; => &apos;image/jpeg&apos;,
            &apos;jpg&apos; => &apos;image/jpeg&apos;,
            &apos;png&apos; => &apos;image/png&apos;,
            &apos;tiff&apos; => &apos;image/tiff&apos;,
            &apos;tif&apos; => &apos;image/tiff&apos;,
            &apos;webp&apos; => &apos;image/webp&apos;,
            &apos;heif&apos; => &apos;image/heif&apos;,
            &apos;heifs&apos; => &apos;image/heif&#45;sequence&apos;,
            &apos;heic&apos; => &apos;image/heic&apos;,
            &apos;heics&apos; => &apos;image/heic&#45;sequence&apos;,
            &apos;eml&apos; => &apos;message/rfc822&apos;,
            &apos;css&apos; => &apos;text/css&apos;,
            &apos;html&apos; => &apos;text/html&apos;,
            &apos;htm&apos; => &apos;text/html&apos;,
            &apos;shtml&apos; => &apos;text/html&apos;,
            &apos;log&apos; => &apos;text/plain&apos;,
            &apos;text&apos; => &apos;text/plain&apos;,
            &apos;txt&apos; => &apos;text/plain&apos;,
            &apos;rtx&apos; => &apos;text/richtext&apos;,
            &apos;rtf&apos; => &apos;text/rtf&apos;,
            &apos;vcf&apos; => &apos;text/vcard&apos;,
            &apos;vcard&apos; => &apos;text/vcard&apos;,
            &apos;ics&apos; => &apos;text/calendar&apos;,
            &apos;xml&apos; => &apos;text/xml&apos;,
            &apos;xsl&apos; => &apos;text/xml&apos;,
            &apos;wmv&apos; => &apos;video/x&#45;ms&#45;wmv&apos;,
            &apos;mpeg&apos; => &apos;video/mpeg&apos;,
            &apos;mpe&apos; => &apos;video/mpeg&apos;,
            &apos;mpg&apos; => &apos;video/mpeg&apos;,
            &apos;mp4&apos; => &apos;video/mp4&apos;,
            &apos;m4v&apos; => &apos;video/mp4&apos;,
            &apos;mov&apos; => &apos;video/quicktime&apos;,
            &apos;qt&apos; => &apos;video/quicktime&apos;,
            &apos;rv&apos; => &apos;video/vnd.rn&#45;realvideo&apos;,
            &apos;avi&apos; => &apos;video/x&#45;msvideo&apos;,
            &apos;movie&apos; => &apos;video/x&#45;sgi&#45;movie&apos;,
            &apos;webm&apos; => &apos;video/webm&apos;,
            &apos;mkv&apos; => &apos;video/x&#45;matroska&apos;,
        ];
        $ext = strtolower($ext);
        if (array_key_exists($ext, $mimes)) {
            return $mimes[$ext];
        }

        return &apos;application/octet&#45;stream&apos;;
    }

    /**
     * Map a file name to a MIME type.
     * Defaults to &apos;application/octet&#45;stream&apos;, i.e.. arbitrary binary data.
     *
     * @param string $filename A file name or full path, does not need to exist as a file
     *
     * @return string
     */
    public static function filenameToType($filename)
    {
        // In case the path is a URL, strip any query string before getting extension
        $qpos = strpos($filename, &apos;?&apos;);
        if (false !== $qpos) {
            $filename = substr($filename, 0, $qpos);
        }
        $ext = static::mb_pathinfo($filename, PATHINFO_EXTENSION);

        return static::_mime_types($ext);
    }

    /**
     * Multi&#45;byte&#45;safe pathinfo replacement.
     * Drop&#45;in replacement for pathinfo(), but multibyte&#45; and cross&#45;platform&#45;safe.
     *
     * @see    http://www.php.net/manual/en/function.pathinfo.php#107461
     *
     * @param string     $path    A filename or path, does not need to exist as a file
     * @param int|string $options Either a PATHINFO_* constant,
     *                            or a string name to return only the specified piece
     *
     * @return string|array
     */
    public static function mb_pathinfo($path, $options = null)
    {
        $ret = [&apos;dirname&apos; => &apos;&apos;, &apos;basename&apos; => &apos;&apos;, &apos;extension&apos; => &apos;&apos;, &apos;filename&apos; => &apos;&apos;];
        $pathinfo = [];
        if (preg_match(&apos;#^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$#im&apos;, $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo)) {
                $ret[&apos;dirname&apos;] = $pathinfo[1];
            }
            if (array_key_exists(2, $pathinfo)) {
                $ret[&apos;basename&apos;] = $pathinfo[2];
            }
            if (array_key_exists(5, $pathinfo)) {
                $ret[&apos;extension&apos;] = $pathinfo[5];
            }
            if (array_key_exists(3, $pathinfo)) {
                $ret[&apos;filename&apos;] = $pathinfo[3];
            }
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case &apos;dirname&apos;:
                return $ret[&apos;dirname&apos;];
            case PATHINFO_BASENAME:
            case &apos;basename&apos;:
                return $ret[&apos;basename&apos;];
            case PATHINFO_EXTENSION:
            case &apos;extension&apos;:
                return $ret[&apos;extension&apos;];
            case PATHINFO_FILENAME:
            case &apos;filename&apos;:
                return $ret[&apos;filename&apos;];
            default:
                return $ret;
        }
    }

    /**
     * Set or reset instance properties.
     * You should avoid this function &#45; it&apos;s more verbose, less efficient, more error&#45;prone and
     * harder to debug than setting properties directly.
     * Usage Example:
     * `$mail&#45;>set(&apos;SMTPSecure&apos;, &apos;tls&apos;);`
     *   is the same as:
     * `$mail&#45;>SMTPSecure = &apos;tls&apos;;`.
     *
     * @param string $name  The property name to set
     * @param mixed  $value The value to set the property to
     *
     * @return bool
     */
    public function set($name, $value = &apos;&apos;)
    {
        if (property_exists($this, $name)) {
            $this&#45;>$name = $value;

            return true;
        }
        $this&#45;>setError($this&#45;>lang(&apos;variable_set&apos;) . $name);

        return false;
    }

    /**
     * Strip newlines to prevent header injection.
     *
     * @param string $str
     *
     * @return string
     */
    public function secureHeader($str)
    {
        return trim(str_replace(["\r", "\n"], &apos;&apos;, $str));
    }

    /**
     * Normalize line breaks in a string.
     * Converts UNIX LF, Mac CR and Windows CRLF line breaks into a single line break format.
     * Defaults to CRLF (for message bodies) and preserves consecutive breaks.
     *
     * @param string $text
     * @param string $breaktype What kind of line break to use; defaults to static::$LE
     *
     * @return string
     */
    public static function normalizeBreaks($text, $breaktype = null)
    {
        if (null === $breaktype) {
            $breaktype = static::$LE;
        }
        // Normalise to \n
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // Now convert LE as needed
        if ("\n" !== $breaktype) {
            $text = str_replace("\n", $breaktype, $text);
        }

        return $text;
    }

    /**
     * Return the current line break format string.
     *
     * @return string
     */
    public static function getLE()
    {
        return static::$LE;
    }

    /**
     * Set the line break format string, e.g. "\r\n".
     *
     * @param string $le
     */
    protected static function setLE($le)
    {
        static::$LE = $le;
    }

    /**
     * Set the public and private key files and password for S/MIME signing.
     *
     * @param string $cert_filename
     * @param string $key_filename
     * @param string $key_pass            Password for private key
     * @param string $extracerts_filename Optional path to chain certificate
     */
    public function sign($cert_filename, $key_filename, $key_pass, $extracerts_filename = &apos;&apos;)
    {
        $this&#45;>sign_cert_file = $cert_filename;
        $this&#45;>sign_key_file = $key_filename;
        $this&#45;>sign_key_pass = $key_pass;
        $this&#45;>sign_extracerts_file = $extracerts_filename;
    }

    /**
     * Quoted&#45;Printable&#45;encode a DKIM header.
     *
     * @param string $txt
     *
     * @return string
     */
    public function DKIM_QP($txt)
    {
        $line = &apos;&apos;;
        $len = strlen($txt);
        for ($i = 0; $i < $len; ++$i) {
            $ord = ord($txt[$i]);
            if (((0x21 <= $ord) and ($ord <= 0x3A)) or $ord == 0x3C or ((0x3E <= $ord) and ($ord <= 0x7E))) {
                $line .= $txt[$i];
            } else {
                $line .= &apos;=&apos; . sprintf(&apos;%02X&apos;, $ord);
            }
        }

        return $line;
    }

    /**
     * Generate a DKIM signature.
     *
     * @param string $signHeader
     *
     * @throws Exception
     *
     * @return string The DKIM signature value
     */
    public function DKIM_Sign($signHeader)
    {
        if (!defined(&apos;PKCS7_TEXT&apos;)) {
            if ($this&#45;>exceptions) {
                throw new Exception($this&#45;>lang(&apos;extension_missing&apos;) . &apos;openssl&apos;);
            }

            return &apos;&apos;;
        }
        $privKeyStr = !empty($this&#45;>DKIM_private_string) ?
            $this&#45;>DKIM_private_string :
            file_get_contents($this&#45;>DKIM_private);
        if (&apos;&apos; != $this&#45;>DKIM_passphrase) {
            $privKey = openssl_pkey_get_private($privKeyStr, $this&#45;>DKIM_passphrase);
        } else {
            $privKey = openssl_pkey_get_private($privKeyStr);
        }
        if (openssl_sign($signHeader, $signature, $privKey, &apos;sha256WithRSAEncryption&apos;)) {
            openssl_pkey_free($privKey);

            return base64_encode($signature);
        }
        openssl_pkey_free($privKey);

        return &apos;&apos;;
    }

    /**
     * Generate a DKIM canonicalization header.
     * Uses the &apos;relaxed&apos; algorithm from RFC6376 section 3.4.2.
     * Canonicalized headers should *always* use CRLF, regardless of mailer setting.
     *
     * @see    https://tools.ietf.org/html/rfc6376#section&#45;3.4.2
     *
     * @param string $signHeader Header
     *
     * @return string
     */
    public function DKIM_HeaderC($signHeader)
    {
        //Unfold all header continuation lines
        //Also collapses folded whitespace.
        //Note PCRE \s is too broad a definition of whitespace; RFC5322 defines it as `[ \t]`
        //@see https://tools.ietf.org/html/rfc5322#section&#45;2.2
        //That means this may break if you do something daft like put vertical tabs in your headers.
        $signHeader = preg_replace(&apos;/\r\n[ \t]+/&apos;, &apos; &apos;, $signHeader);
        $lines = explode("\r\n", $signHeader);
        foreach ($lines as $key => $line) {
            //If the header is missing a :, skip it as it&apos;s invalid
            //This is likely to happen because the explode() above will also split
            //on the trailing LE, leaving an empty line
            if (strpos($line, &apos;:&apos;) === false) {
                continue;
            }
            list($heading, $value) = explode(&apos;:&apos;, $line, 2);
            //Lower&#45;case header name
            $heading = strtolower($heading);
            //Collapse white space within the value
            $value = preg_replace(&apos;/[ \t]{2,}/&apos;, &apos; &apos;, $value);
            //RFC6376 is slightly unclear here &#45; it says to delete space at the *end* of each value
            //But then says to delete space before and after the colon.
            //Net result is the same as trimming both ends of the value.
            //by elimination, the same applies to the field name
            $lines[$key] = trim($heading, " \t") . &apos;:&apos; . trim($value, " \t");
        }

        return implode("\r\n", $lines);
    }

    /**
     * Generate a DKIM canonicalization body.
     * Uses the &apos;simple&apos; algorithm from RFC6376 section 3.4.3.
     * Canonicalized bodies should *always* use CRLF, regardless of mailer setting.
     *
     * @see    https://tools.ietf.org/html/rfc6376#section&#45;3.4.3
     *
     * @param string $body Message Body
     *
     * @return string
     */
    public function DKIM_BodyC($body)
    {
        if (empty($body)) {
            return "\r\n";
        }
        // Normalize line endings to CRLF
        $body = static::normalizeBreaks($body, "\r\n");

        //Reduce multiple trailing line breaks to a single one
        return rtrim($body, "\r\n") . "\r\n";
    }

    /**
     * Create the DKIM header and body in a new message header.
     *
     * @param string $headers_line Header lines
     * @param string $subject      Subject
     * @param string $body         Body
     *
     * @return string
     */
    public function DKIM_Add($headers_line, $subject, $body)
    {
        $DKIMsignatureType = &apos;rsa&#45;sha256&apos;; // Signature & hash algorithms
        $DKIMcanonicalization = &apos;relaxed/simple&apos;; // Canonicalization of header/body
        $DKIMquery = &apos;dns/txt&apos;; // Query method
        $DKIMtime = time(); // Signature Timestamp = seconds since 00:00:00 &#45; Jan 1, 1970 (UTC time zone)
        $subject_header = "Subject: $subject";
        $headers = explode(static::$LE, $headers_line);
        $from_header = &apos;&apos;;
        $to_header = &apos;&apos;;
        $date_header = &apos;&apos;;
        $current = &apos;&apos;;
        $copiedHeaderFields = &apos;&apos;;
        $foundExtraHeaders = [];
        $extraHeaderKeys = &apos;&apos;;
        $extraHeaderValues = &apos;&apos;;
        $extraCopyHeaderFields = &apos;&apos;;
        foreach ($headers as $header) {
            if (strpos($header, &apos;From:&apos;) === 0) {
                $from_header = $header;
                $current = &apos;from_header&apos;;
            } elseif (strpos($header, &apos;To:&apos;) === 0) {
                $to_header = $header;
                $current = &apos;to_header&apos;;
            } elseif (strpos($header, &apos;Date:&apos;) === 0) {
                $date_header = $header;
                $current = &apos;date_header&apos;;
            } elseif (!empty($this&#45;>DKIM_extraHeaders)) {
                foreach ($this&#45;>DKIM_extraHeaders as $extraHeader) {
                    if (strpos($header, $extraHeader . &apos;:&apos;) === 0) {
                        $headerValue = $header;
                        foreach ($this&#45;>CustomHeader as $customHeader) {
                            if ($customHeader[0] === $extraHeader) {
                                $headerValue = trim($customHeader[0]) .
                                               &apos;: &apos; .
                                               $this&#45;>encodeHeader(trim($customHeader[1]));
                                break;
                            }
                        }
                        $foundExtraHeaders[$extraHeader] = $headerValue;
                        $current = &apos;&apos;;
                        break;
                    }
                }
            } else {
                if (!empty($$current) and strpos($header, &apos; =?&apos;) === 0) {
                    $$current .= $header;
                } else {
                    $current = &apos;&apos;;
                }
            }
        }
        foreach ($foundExtraHeaders as $key => $value) {
            $extraHeaderKeys .= &apos;:&apos; . $key;
            $extraHeaderValues .= $value . "\r\n";
            if ($this&#45;>DKIM_copyHeaderFields) {
                $extraCopyHeaderFields .= "\t|" . str_replace(&apos;|&apos;, &apos;=7C&apos;, $this&#45;>DKIM_QP($value)) . ";\r\n";
            }
        }
        if ($this&#45;>DKIM_copyHeaderFields) {
            $from = str_replace(&apos;|&apos;, &apos;=7C&apos;, $this&#45;>DKIM_QP($from_header));
            $to = str_replace(&apos;|&apos;, &apos;=7C&apos;, $this&#45;>DKIM_QP($to_header));
            $date = str_replace(&apos;|&apos;, &apos;=7C&apos;, $this&#45;>DKIM_QP($date_header));
            $subject = str_replace(&apos;|&apos;, &apos;=7C&apos;, $this&#45;>DKIM_QP($subject_header));
            $copiedHeaderFields = "\tz=$from\r\n" .
                                  "\t|$to\r\n" .
                                  "\t|$date\r\n" .
                                  "\t|$subject;\r\n" .
                                  $extraCopyHeaderFields;
        }
        $body = $this&#45;>DKIM_BodyC($body);
        $DKIMlen = strlen($body); // Length of body
        $DKIMb64 = base64_encode(pack(&apos;H*&apos;, hash(&apos;sha256&apos;, $body))); // Base64 of packed binary SHA&#45;256 hash of body
        if (&apos;&apos; == $this&#45;>DKIM_identity) {
            $ident = &apos;&apos;;
        } else {
            $ident = &apos; i=&apos; . $this&#45;>DKIM_identity . &apos;;&apos;;
        }
        $dkimhdrs = &apos;DKIM&#45;Signature: v=1; a=&apos; .
            $DKIMsignatureType . &apos;; q=&apos; .
            $DKIMquery . &apos;; l=&apos; .
            $DKIMlen . &apos;; s=&apos; .
            $this&#45;>DKIM_selector .
            ";\r\n" .
            "\tt=" . $DKIMtime . &apos;; c=&apos; . $DKIMcanonicalization . ";\r\n" .
            "\th=From:To:Date:Subject" . $extraHeaderKeys . ";\r\n" .
            "\td=" . $this&#45;>DKIM_domain . &apos;;&apos; . $ident . "\r\n" .
            $copiedHeaderFields .
            "\tbh=" . $DKIMb64 . ";\r\n" .
            "\tb=";
        $toSign = $this&#45;>DKIM_HeaderC(
            $from_header . "\r\n" .
            $to_header . "\r\n" .
            $date_header . "\r\n" .
            $subject_header . "\r\n" .
            $extraHeaderValues .
            $dkimhdrs
        );
        $signed = $this&#45;>DKIM_Sign($toSign);

        return static::normalizeBreaks($dkimhdrs . $signed) . static::$LE;
    }

    /**
     * Detect if a string contains a line longer than the maximum line length
     * allowed by RFC 2822 section 2.1.1.
     *
     * @param string $str
     *
     * @return bool
     */
    public static function hasLineLongerThanMax($str)
    {
        return (bool) preg_match(&apos;/^(.{&apos; . (self::MAX_LINE_LENGTH + strlen(static::$LE)) . &apos;,})/m&apos;, $str);
    }

    /**
     * Allows for public read access to &apos;to&apos; property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getToAddresses()
    {
        return $this&#45;>to;
    }

    /**
     * Allows for public read access to &apos;cc&apos; property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getCcAddresses()
    {
        return $this&#45;>cc;
    }

    /**
     * Allows for public read access to &apos;bcc&apos; property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getBccAddresses()
    {
        return $this&#45;>bcc;
    }

    /**
     * Allows for public read access to &apos;ReplyTo&apos; property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getReplyToAddresses()
    {
        return $this&#45;>ReplyTo;
    }

    /**
     * Allows for public read access to &apos;all_recipients&apos; property.
     * Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     *
     * @return array
     */
    public function getAllRecipientAddresses()
    {
        return $this&#45;>all_recipients;
    }

    /**
     * Perform a callback.
     *
     * @param bool   $isSent
     * @param array  $to
     * @param array  $cc
     * @param array  $bcc
     * @param string $subject
     * @param string $body
     * @param string $from
     * @param array  $extra
     */
    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from, $extra)
    {
        if (!empty($this&#45;>action_function) and is_callable($this&#45;>action_function)) {
            call_user_func($this&#45;>action_function, $isSent, $to, $cc, $bcc, $subject, $body, $from, $extra);
        }
    }

    /**
     * Get the OAuth instance.
     *
     * @return OAuth
     */
    public function getOAuth()
    {
        return $this&#45;>oauth;
    }

    /**
     * Set an OAuth instance.
     *
     * @param OAuth $oauth
     */
    public function setOAuth(OAuth $oauth)
    {
        $this&#45;>oauth = $oauth;
    }
}
