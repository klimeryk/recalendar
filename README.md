# ReCalendar
### Highly customizable calendar for ReMarkable tablets

ReCalendar allows you to generate your own, personalized calendar using PHP and the [mPDF library](https://mpdf.github.io/).

Features:
 - Optimized for the [ReMarkable 2 tablet](https://remarkable.com/store/remarkable-2) (should work with version 1 as well) to use the full space available and minimize screen refreshes.
 - Heavy use of links to allow quick and easy navigation.
 - Lots of easy configuration options to tailor the calendar to your needs - plus access to the source code for even more advanced customization.
 - Easily switch to any locale supported by PHP.
 - Add extra pages to all or selected days of the week to suit your needs.
 - Provide a list of special dates (anniversaries, birthdays, etc.) and let ReCalendar embed them into your personalized calendar - on monthly views, weekly overviews and finally, day entries.
 - Track your habits monthly.

## Quickstart

Dependencies:

- PHP
- [Composer](https://getcomposer.org/)

On MacOS, you can quickly install them with [Homebrew](https://brew.sh/): `brew install php composer`.

Then fetch the newest code from this repository - the easiest way is to download the [ZIP archive of it](https://github.com/klimeryk/recalendar/archive/refs/heads/main.zip).

Go to the folder where you unzipped/cloned the repository and run:

```
composer install
php generate.php
```

After a few seconds you should have your very own `ReCalendar.pdf` calendar generated in the same directory.

## Configuration

You can easily override the configuration defaults by creating a new file called `local.config.php`. Here's a short example that changes the year to 3000, adds some weekly TODOs and special dates:

```php
<?php

namespace ReCalendar;

class LocalConfig extends Config {
  protected function get_configuration() : array {
    return array_merge( parent::get_configuration(), [
      self::YEAR => 3000,
      self::SPECIAL_DATES => [
        '04-05' => [ 'May The Fourth Be With You' ],
	'24-12' => [ 'Christmas' ],
      ],
      self::WEEKLY_TODOS => [
        'Plan week',
	'Send the weekly email',
      ],
    ] );
  }
}
```

See the [`config.php`](https://github.com/klimeryk/recalendar/blob/main/config.php) file for the full list of available options and their descriptions. You can modify the options there as well, but it's recommended to use the `local.config.php` instead as then you can easily update the source code in the future and retain your configuration changes.

## Update

Just run `php generate.php` any time you need to regenerate the calendar after config changes. If you want to update the `recalendar` source code, either use `git pull` or download the newest ZIP archive and override all the files (make sure you're using the `local.config.php` approach, as described above).

**NOTE**: The update process is mostly for when you're tweaking your configuration and/or generating a calendar for the next year. Due to how ReMarkble tablet works, you can't easily "migrate" your notes from your previous version of the calendar/file to the new one. To the tablet, it's a new, "empty" file. You can select and copy notes from each page individually and move them over, but that's very cumbersome.
