# Security notices relating to PHPMailer

Please disclose any vulnerabilities found responsibly &#45; report any security problems found to the maintainers privately.

PHPMailer versions prior to 6.0.6 and 5.2.27 are vulnerable to an object injection attack by passing `phar://` paths into `addAttachment()` and other functions that may receive unfiltered local paths, possibly leading to RCE. Recorded as [CVE&#45;2018&#45;19296](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2018&#45;19296). See [this article](https://knasmueller.net/5&#45;answers&#45;about&#45;php&#45;phar&#45;exploitation) for more info on this type of vulnerability. Mitigated by blocking the use of paths containing URL&#45;protocol style prefixes such as `phar://`. Reported by Sehun Oh of cyberone.kr.

PHPMailer versions prior to 5.2.24 (released July 26th 2017) have an XSS vulnerability in one of the code examples, [CVE&#45;2017&#45;11503](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2017&#45;11503). The `code_generator.phps` example did not filter user input prior to output. This file is distributed with a `.phps` extension, so it it not normally executable unless it is explicitly renamed, and the file is not included when PHPMailer is loaded through composer, so it is safe by default. There was also an undisclosed potential XSS vulnerability in the default exception handler (unused by default). Patches for both issues kindly provided by Patrick Monnerat of the Fedora Project.

PHPMailer versions prior to 5.2.22 (released January 9th 2017) have a local file disclosure vulnerability, [CVE&#45;2017&#45;5223](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2017&#45;5223). If content passed into `msgHTML()` is sourced from unfiltered user input, relative paths can map to absolute local file paths and added as attachments. Also note that `addAttachment` (just like `file_get_contents`, `passthru`, `unlink`, etc) should not be passed user&#45;sourced params either! Reported by Yongxiang Li of Asiasecurity.

PHPMailer versions prior to 5.2.20 (released December 28th 2016) are vulnerable to [CVE&#45;2016&#45;10045](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2016&#45;10045) a remote code execution vulnerability, responsibly reported by [Dawid Golunski](https://legalhackers.com/advisories/PHPMailer&#45;Exploit&#45;Remote&#45;Code&#45;Exec&#45;CVE&#45;2016&#45;10045&#45;Vuln&#45;Patch&#45;Bypass.html), and patched by Paul Buonopane (@Zenexer).

PHPMailer versions prior to 5.2.18 (released December 2016) are vulnerable to [CVE&#45;2016&#45;10033](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2016&#45;10033) a remote code execution vulnerability, responsibly reported by [Dawid Golunski](http://legalhackers.com/advisories/PHPMailer&#45;Exploit&#45;Remote&#45;Code&#45;Exec&#45;CVE&#45;2016&#45;10033&#45;Vuln.html).

PHPMailer versions prior to 5.2.14 (released November 2015) are vulnerable to [CVE&#45;2015&#45;8476](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2015&#45;8476) an SMTP CRLF injection bug permitting arbitrary message sending.

PHPMailer versions prior to 5.2.10 (released May 2015) are vulnerable to [CVE&#45;2008&#45;5619](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2008&#45;5619), a remote code execution vulnerability in the bundled html2text library. This file was removed in 5.2.10, so if you are using a version prior to that and make use of the html2text function, it&apos;s vitally important that you upgrade and remove this file.

PHPMailer versions prior to 2.0.7 and 2.2.1 are vulnerable to [CVE&#45;2012&#45;0796](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2012&#45;0796), an email header injection attack.

Joomla 1.6.0 uses PHPMailer in an unsafe way, allowing it to reveal local file paths, reported in [CVE&#45;2011&#45;3747](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2011&#45;3747).

PHPMailer didn&apos;t sanitise the `$lang_path` parameter in `SetLanguage`. This wasn&apos;t a problem in itself, but some apps (PHPClassifieds, ATutor) also failed to sanitise user&#45;provided parameters passed to it, permitting semi&#45;arbitrary local file inclusion, reported in [CVE&#45;2010&#45;4914](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2010&#45;4914), [CVE&#45;2007&#45;2021](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2007&#45;2021) and [CVE&#45;2006&#45;5734](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2006&#45;5734).

PHPMailer 1.7.2 and earlier contained a possible DDoS vulnerability reported in [CVE&#45;2005&#45;1807](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2005&#45;1807).

PHPMailer 1.7 and earlier (June 2003) have a possible vulnerability in the `SendmailSend` method where shell commands may not be sanitised. Reported in [CVE&#45;2007&#45;3215](https://web.nvd.nist.gov/view/vuln/detail?vulnId=CVE&#45;2007&#45;3215).

