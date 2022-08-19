# Validate Callback Request event from Sinch Verification using Laravel

This project validates a Verification Event callback received from the Sinch platform when using the [Sinch Verification](https://dashboard.sinch.com/verification/overview) product with callbacks enabled.

## Requirements

- [PHP 8.*](https://www.php.net)
- [composer](https://getcomposer.org/download/)
- [ngrok](https://www.ngrok.com)

## Install

- install the project's dependencies using `composer install`
- replace the required values in the `/routes/api.php` file
- run the server using `php artisan serve`
- start ngrok `ngrok http 8000` (port 8000 is used by default by Laravel)
  - copy the ngrok url to the Verification App that you will receive Verification Events from to your [Sinch Dashboard](https://dashboard.sinch.com/verification/apps)
  - make sure to append the following URI at the end of the URL, `/api/verification/events`
  - example `https://df6a-143-177-206-33.ngrok.io/api/verification/events`
- test using the SMS PIN Verification script found in the project
  - replace the required values in the `test-sms-verification-basic-auth.php` file or in the `test-sms-verification-signed-request.php` file
  - use either of the 2 scripts to start an SMS verification event, e.g. run the command `php -f test-sms-verification-basic-auth.php`

### Notes for Windows Users

- Make sure the `php.ini` file includes `extension=php_fileinfo.dll`, not having the extension will block `composer` to get all the required dependencies
