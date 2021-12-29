# ReCalendar

## New version -> recalendar.me

Please check out https://recalendar.me/, an online, easy to use version of ReCalendar. It's also on GitHub! https://github.com/klimeryk/recalendar.js/
It's a completly separate project, rewritten by scratch, but with the same principles and overal design.
I'll be most likely deprecating this repository as the new version is much easier to work on _and_ it's easier to use for end-users as well. Some requested features will be much easier to implement there as well.

### Highly customizable calendar for ReMarkable tablets

ReCalendar allows you to generate your own, personalized calendar using PHP and the [mPDF library](https://mpdf.github.io/).

## How does it look?

See it in action: https://youtu.be/Ys2fNQu0v0o and check more [screenshots](SCREENSHOTS.md). Example [generated PDF](https://github.com/klimeryk/recalendar/raw/main/example/ReCalendar.pdf) version and the [`local.config.php`](https://github.com/klimeryk/recalendar/blob/main/example/local.config.php) that generated it.

https://user-images.githubusercontent.com/3392497/124393405-e86ed380-dce9-11eb-93d4-6c19a7770a36.mp4

## Blank version

Don't want to/need to/can customize it? Check out the [ready-to-use blank version](https://github.com/klimeryk/recalendar/raw/main/blank/ReCalendar.pdf) - it still benefits from the quick navigation through links, layout optimized for RM, etc. - just misses your personal touch :)

## Features

 - Optimized for the [ReMarkable 2 tablet](https://remarkable.com/store/remarkable-2) (should work with version 1 as well) to use the full space available and minimize screen refreshes.
 - No hacks needed - the generated PDF is a normal file, with links, etc. that you can simply upload normally to your tablet.
 - Heavy use of links to allow quick and easy navigation.
 - Lots of easy configuration options to tailor the calendar to your needs - plus access to the source code for even more advanced customization.
 - Easily switch to any locale supported by PHP.
 - Add extra pages to all or selected days of the week to suit your needs.
 - Provide a list of special dates (anniversaries, birthdays, etc.) and let ReCalendar embed them into your personalized calendar - on monthly views, weekly overviews and finally, day entries.
 - Track your habits monthly.
 - Start the "year" on arbitrary month (can be useful for tracking academic years, etc.).

## Quickstart

Dependencies:

- PHP >= 7.4 (with `mbstring` and `gd` extensions - [required by mPDF](https://mpdf.github.io/about-mpdf/requirements-v7.html))
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

You can have multiple local config files and explicitly pass the path to the `generate.php` script: `php generate.php -c second.config.php`.

There's also an [example generated PDF](https://github.com/klimeryk/recalendar/raw/main/example/ReCalendar.pdf) you can check out quickly and the [`local.config.php`](https://github.com/klimeryk/recalendar/blob/main/example/local.config.php) that generated it as an inspiration:

```
cp example/local.config.php .
```

## Command line options

For ease of you use, you can make the `generate.php` file executable (`chmod +x generate.php`) and then you can run it simply with `./generate.php`.

Available command line options:

- `-c path/to/file.php`: uses the specified local config file instead of the default `local.config.php`.

## Advanced configuration example

This is an example configuration for the Polish language (my native language), showing how you might want to approach it yourself, for your language.

```php
<?php

namespace ReCalendar;

class LocalConfig extends Config {
  protected function get_configuration() : array {
    return array_merge( parent::get_configuration(), [
      // See `locale -a` if your locale is supported
      self::LOCALE => 'pl_PL.UTF-8',
      // Override the month names, because the Polish locale
      // has them declined, but I prefer them like this
      self::MONTHS => [
        1 => 'Styczeń',
        2 => 'Luty',
        3 => 'Marzec',
        4 => 'Kwiecień',
        5 => 'Maj',
        6 => 'Czerwiec',
        7 => 'Lipiec',
        8 => 'Sierpień',
        9 => 'Wrzesień',
        10 => 'Październik',
        11 => 'Listopad',
        12 => 'Grudzień',
      ],
      // Some habits I want to track
      self::HABITS => [
        'Jogging',
        'Hobby',
        'Książka',
      ],
      // These will be shown on each week overview
      // to help me plan the week.
      self::WEEKLY_TODOS => [
        'Odkurzyć mieszkanie',
        'Zaplanować wycieczkę',
      ],
      self::WEEK_NAME => 'Tydzień',
      self::WEEK_NUMBER => 'T#',
      self::DAY_NAMES_SHORT => [
        'Pn',
        'Wt',
        'Śr',
        'Cz',
        'Pt',
        'So',
        'Nd',
      ],
      self::DAY_ITINERARY_ITEMS => [
        self::DAY_ITINERARY_COMMON => [
          [ 21, '', ],
          [ 2, 'One act of kindness', ],
        ],
        // You can use day of the week numbers (4 being Thursday here)
        // to override the itinerary items for those specific days.
        4 => [
          [ 21, '', ],
          [ 3, 'One act of kindness', ],
          // Adding more than ~24 lines forces a new page to be created
          // You can use that to add an additional page for days you
          // need to write more
          [ 28, 'Notes from session', ],
        ],
        self::DAY_ITINERARY_WEEK_RETRO => [
          // Just give me 24 lines, without any text
          [ 24, '' ],
        ],
        self::DAY_ITINERARY_MONTH_OVERVIEW => [
          [ 3, 'Main goal', ],
          [ 13, 'Notes', ],
        ],
      ],
      self::SPECIAL_DATES => [
        '14-03' => [ 'Pi Day' ],
        '25-12' => [ 'Christmas' ],
	// Note that you can, of course, have more than one item per day:
	'04-05' => [ 'Star Wars Day', "Will Arnett's birthday" ],
      ],
      self::WEEKLY_RETROSPECTIVE_BOOKMARK => 'Podsumowanie',
      self::WEEKLY_RETROSPECTIVE_TITLE => 'Podsumowanie tygodnia',
    ] );
  }
}
```

## Update

Just run `php generate.php` any time you need to regenerate the calendar after config changes. If you want to update the `recalendar` source code, either use `git pull` or download the newest ZIP archive and override all the files (make sure you're using the `local.config.php` approach, as described above).

**NOTE**: The update process is mostly for when you're tweaking your configuration and/or generating a calendar for the next year. Due to how ReMarkble tablet works, you can't easily "migrate" your notes from your previous version of the calendar/file to the new one. To the tablet, it's a new, "empty" file. You can select and copy notes from each page individually and move them over, but that's very cumbersome.

## Known issues

### Links are hard to trigger

Unfortunately, due to the [limitations of mPDF](https://mpdf.github.io/css-stylesheets/supported-css.html), lots of layouting was done using tables (hello webdevelopment in 2000s ;)). As a result, the links usually only take up as much space as their text. So it's easier to target `11` than `1`, because it's "bigger". I've tried different approaches, but it boils down to no block items in tables in mPDF and it's not something I can overcome. I'd have to switch to a different library, but that's something I realized too late in the process.
In practice, I did not find it that disruptive, but, of course, YMMV.

### It's slow to generate

It takes around 15-20 seconds to generate the full file on my laptop - YMMV. I've tried optimizing the script, but the profiling data clearly showed the [bottleneck is in the mPDF library](https://mpdf.github.io/troubleshooting/slow.html). So there was not much I could do (except for trying some configuration options that had marginal impact). In the end, it's not bad since you will only "feel" this when trying out different configuration options. The PDF itself, of course, works like any other PDF files.

### It does not cover the full page on my XYZ tablet/device

I only have ReMarkable 2 to test with and I wanted to take up all the available space on the screen for it. So it's been optimized for RM2's screen size. Try adjusting the [`format` configuration option](https://github.com/klimeryk/recalendar/blob/cbd2d84507eae773cd21b03448ca170b6ee5690a/config.php#L61-L64) and editing the CSS styles.

## License

[GPL-3.0 License](https://github.com/klimeryk/recalendar/blob/main/LICENSE). In particular, this means that you can do what you want with this code, but *you have to publish your changes with the same license*. Please consider submitting a PR, if you have an idea for a great improvement! 🙏 My main motivation was to scratch my own itch, but as a result I might have missed your use case so I'm happy to hear how this generator can be improved 🙇
