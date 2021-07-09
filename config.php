<?php

namespace ReCalendar;

class Config {
	public const DAY_NAMES_SHORT = 'day_names_short';
	public const DAY_ITINERARY_ITEMS = 'day_itinerary_items';
	public const DAY_ITINERARY_COMMON = 'day_itinerary_common';
	public const DAY_ITINERARY_WEEK_RETRO = 'day_itinerary_week_retro';
	public const DAY_ITINERARY_MONTH_OVERVIEW = 'day_itinerary_month_overview';
	public const FORMAT = 'format';
	public const HABITS = 'habits';
	public const HABITS_TITLE = 'habits_title';
	public const LOCALE = 'locale';
	public const MONTH = 'month';
	public const MONTH_COUNT = 'month_count';
	public const MONTHS = 'months';
	public const WEEK_NAME = 'week_name';
	public const WEEK_NUMBER = 'week_number';
	public const WEEKLY_RETROSPECTIVE_BOOKMARK = 'weekly_retrospective_bookmark';
	public const WEEKLY_RETROSPECTIVE_TITLE = 'weekly_retrospective_title';
	public const WEEKLY_TODOS = 'weekly_todos';
	public const SPECIAL_DATES = 'special_dates';
	public const YEAR = 'year';

	public function get( string $key ) {
		return $this->get_configuration()[ $key ] ?? null;
	}

	protected function get_configuration() : array {
		$configuration = [
			// Used in the small calendar in the upper right corner of most pages
			// Please try to use a 2 character abbreviations to save space on the page
			self::DAY_NAMES_SHORT => [
				'Mo',
				'Tu',
				'We',
				'Th',
				'Fr',
				'Sa',
				'Su',
			],
			// Items for each page type
			// The format is: [ NUMBER OF LINES, NAME (optional) ]
			// You might need to adjust the number of lines depending on your config (locale, font size, etc.)
			self::DAY_ITINERARY_ITEMS => [
				// Common itinerary used if nothing more specific was defined
				self::DAY_ITINERARY_COMMON => [
					[ 23, '', ],
				],
				// Itinerary for the weekly retrospective
				self::DAY_ITINERARY_WEEK_RETRO => [
					[ 24, '' ],
				],
				// Itinerary for the month's overview
				self::DAY_ITINERARY_MONTH_OVERVIEW => [
					[ 16, '' ],
				],
			],
			// A list of habits that triggers generating a table on the month's overview
			// to help tracking those habits
			self::HABITS => [
			],
			// Title for the habits table on month overview
			self::HABITS_TITLE => 'Habits',
			// This is the exact size (in mm) of the ReMarkable 2 screen
			// You might want to adjust it to your device's size
			// See https://mpdf.github.io/reference/mpdf-functions/construct.html for possible values
			self::FORMAT => [ 157, 209 ],
			// Locale to generate the calendar in
			// To check which locale your PHP version supports run:
			// `locale -a` in your terminal (at least on Linux and MacOS)
			// Note that you will still need to override some configuration variables, like `WEEK_NAME`, etc.
			self::LOCALE => 'en_US.UTF-8',
			// The month from which to start the "year"
			// Useful if you want to track your college year, for example.
			// You could then set this to 10 (October) and the calendar
			// would then be generated for 12 months starting from October.
			self::MONTH => 1,
			// The number of months you want this calendar to be for.
			// Useful if you want a calendar for the quarter (3) or a 15 month calendar.
			self::MONTH_COUNT => 12,
			// Title of the Week overview page
			self::WEEK_NAME => 'Week',
			// A short version of "Week Number" used in the header of the small calendar in upper right corner of the page
			self::WEEK_NUMBER => 'W#',
			// Used for the bookmark of the weekly retrospective pages
			self::WEEKLY_RETROSPECTIVE_BOOKMARK => 'Retrospective',
			// Used for the title of the weekly retrospective pages
			self::WEEKLY_RETROSPECTIVE_TITLE => 'Weekly retrospective',
			// A list of items you'd like to be listed in the notes of the weekly overview
			self::WEEKLY_TODOS => [
			],
			// A list of special dates (anniversaries, birthdays, holidays) that will be highlighted throughout the calendar:
			// in the small calendar, on weekly overviews and daily entries.
			self::SPECIAL_DATES => [
				// Example:
				// '01-01' => "New Year!",
				// '01-04' => "April Fools' Day",
			],
			// The year for which to generate this calendar.
			// Defaults to the current year.
			self::YEAR => (int) date( 'Y' ),
		];

		// Get the names of the months in the set locale.
		// This might useful for non-English locales (like Polish), that apparently
		// have their names decilned in the locale provided by the system, while
		// you'd probably want a non-declined version.
		// Example: 'stycznia' instead of 'Styczeń' for January in Polish.
		$configuration[ self::MONTHS ] = $this->generate_month_names( $configuration[ self::LOCALE ] );

		return $configuration;
	}

	private function generate_month_names( string $locale ) : array {
		$old_locale = setlocale( LC_TIME, 0 );
		setlocale( LC_TIME, $locale );

		$start = new \DateTimeImmutable( 'first day of january' );
		$interval = new \DateInterval( 'P1M' );
		$end = new \DateTimeImmutable( 'last day of december' );
		$period = new \DatePeriod( $start, $interval, $end );
		$month_names = [];
		foreach ( $period as $index => $month ) {
			$month_names[ $index + 1 ] = strftime( '%B', $month->getTimestamp() );
		}

		setlocale( LC_TIME, $old_locale );
		return $month_names;
	}
}
