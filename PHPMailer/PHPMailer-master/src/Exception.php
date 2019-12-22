<?php
/**
 * PHPMailer Exception class.
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
 * PHPMailer exception handler.
 *
 * @author  Marcus Bointon <phpmailer@synchromedia.co.uk>
 */
class Exception extends \Exception
{
    /**
     * Prettify error message output.
     *
     * @return string
     */
    public function errorMessage()
    {
        return &apos;<strong>&apos; . htmlspecialchars($this&#45;>getMessage()) . "</strong><br />\n";
    }
}
