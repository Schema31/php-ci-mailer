# php-ci-mailer
======

[Packagist][link-packagist]

Mailer for CodeIgniter.

Install
-------

You can install the library using [composer](https://getcomposer.org/):

```sh
$ composer require schema31/php-ci-mailer
```

How to use
----------

## Configurations

Here the list of all configuration attributes supported:

|Preference       |Default Value       |Options             |Description|
|-----------------|--------------------|--------------------|Description|
|useragent        |CodeIgniter         |None                |The “user agent”.|
|protocol         |mail                |mail|sendmail|smtp  |The mail sending protocol.|
|mailpath         |/usr/sbin/sendmail  |None                |The server path to Sendmail.|
|smtp_host        |No Default          |None                |SMTP Server Address.|
|smtp_user        |No Default          |None                |SMTP Username.|
|smtp_pass        |No Default          |None                |SMTP Password.|
|smtp_port        |25                  |None                |SMTP Port.|
|smtp_timeout     |5                   |None                |SMTP Timeout (in seconds).|
|smtp_keepalive   |FALSE               |TRUE|FALSE          |Persistent SMTP connections.|
|smtp_crypto      |No Default          |tls|ssl             |SMTP Encryption.|
|wordwrap         |TRUE                |TRUE|FALSE          |Word-wrap.|
|wrapchars        |76                  |None                |Character count to wrap at.|
|mailtype         |text                |text|html 	        |Type of mail.|
|charset          |$config['charset']  |Character set       |(utf-8, iso-8859-1, etc.).|
|validate         |FALSE               |TRUE|FALSE          |Whether to validate the email address.|
|priority         |3                   |1|2|3|4|5           |Email Priority. 1 = highest. 5 = lowest.|
|crlf             |\n                  |“\r\n”|“\n”|“\r” 	|Newline character.|
|newline          |\n                  |“\r\n”|“\n”|“\r”    |Newline character.|
|bcc_batch_mode   |FALSE               |TRUE|FALSE          |Enable BCC Batch Mode.|
|bcc_batch_size   |200                 |None                |Number of emails in each BCC batch.|
|dsn              |FALSE               |TRUE|FALSE          |Enable notify message from server.|
|from_email       |No Default          |None                |“From” e-mail address.|
|from_name        |No Default          |None                |“From” display name.|
|prefix_subject   |No Default          |None                |If you want to set a prefix for your subject|

### Internal configuration

You can use an array of configurations: 

```php
$config = [];
$config['protocol'] = 'smtp';// mail, sendmail, smtp
$config['smtp_host'] = 'smtp.schema31.it';// SMTP Server Address.
$config['smtp_user'] = 'noreply@oneprofile.it';// SMTP Username.
$config['smtp_pass'] = 'qw35bb99d';// SMTP Password.
$config['smtp_port'] = '25';// SMTP Port.
$config['mailtype'] = 'html';// text or html
$config['smtp_timeout'] = '30';// SMTP Timeout (in seconds).
$config['from_email'] = 'noreply@example.it';
$config['from_name'] = 'CI MAILER';
$config['prefix_subject'] = 'Mailer - ';

$mailer = new \Schema31\CiMailer\Mailer($config);
```

### Configuration file

You can use the file named "cemail.php" in application/config folder and the library loads it automatically: 

```php
$mailer = new \Schema31\CiMailer\Mailer();
```

## Adding recipients

You can add a single or multiple recipients (like the "to", "cc", "bcc" recipient) with:

```php
$mailer = new \Schema31\CiMailer\Mailer();

$mailer->setSingleTo("foo01@bar.com"); //Single "to" recipient
$mailer->setSingleCc("foo02@bar.com"); //Single "cc" recipient
$mailer->setSingleBcc("foo03@bar.com"); //Single "bcc" recipient
```

Or

```php
$mailer = new \Schema31\CiMailer\Mailer();

$mailer->setMultipleTo(["foo01@bar.com", "foo0101@bar.com"]); //Multiple "to" recipient: "foo01@bar.com" and "foo0101@bar.com"
$mailer->setMultipleCc(["foo02@bar.com", "foo0202@bar.com"]); //Multiple "cc" recipient: "foo02@bar.com" and "foo0202@bar.com"
$mailer->setMultipleBcc(["foo03@bar.com", "foo0303@bar.com"]); //Multiple "bcc" recipient: "foo03@bar.com" and "foo0303@bar.com"
```

Or

```php
$mailer = new \Schema31\CiMailer\Mailer();

$mailer->setSingleTo("foo01@bar.com,foo0101@bar.com"); //Multiple "to" recipient: "foo01@bar.com" and "foo0101@bar.com" comma separated
$mailer->setSingleCc("foo02@bar.com,foo0202@bar.com"); //Multiple "cc" recipient: "foo02@bar.com" and "foo0202@bar.com" comma separated
$mailer->setSingleBcc("foo03@bar.com,foo0303@bar.com"); //Multiple "bcc" recipient: "foo03@bar.com" and "foo0303@bar.com" comma separated
```

## Printing debbuger message

If you want to print debug message after the send of the email:

```php
$mailer = new \Schema31\CiMailer\Mailer();

//...

echo $mailer->printDebugger();
```

## Sending an email

```php
$mailer = new \Schema31\CiMailer\Mailer();

//Method chaining is allowed
$mailer
->setSingleTo("foo01@bar.com")
->setSubject("Email di prova #" . uniqid())
->setMessage("La email di testo")
->send(); //returns true if the email is sent, false otherwise.
```

[link-packagist]: https://packagist.org/packages/schema31/php-ci-mailer