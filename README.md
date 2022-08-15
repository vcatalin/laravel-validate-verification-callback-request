# Validate Callback Request event from Sinch Verification using Laravel

This project validates a Verification Event callback received from the Sinch platform when using the [Sinch Verification](https://dashboard.sinch.com/verification/overview) product with callbacks enabled.

## Requirements

- PHP 8.*
- composer
- ngrok

## Install

- run the server using `php artisan serve`
- start ngrok `ngrok http 8000` (port 8000 is used by default by laravel)
  - copy the ngrok url to the Verification App that you will receive Verification Events from to your [Sinch Dashboard](https://dashboard.sinch.com/verification/apps)
  - make sure to append the following URI at the end of the URL, `/api/verification/events`
  - example `https://ngrok-foobar.com/api/verification/events`
