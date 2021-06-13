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
	public const LOCALE = 'locale';
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
			self::DAY_NAMES_SHORT => [
				'Mo',
				'Tu',
				'We',
				'Th',
				'Fr',
				'Sa',
				'Su',
			],
			self::DAY_ITINERARY_ITEMS => [
				self::DAY_ITINERARY_COMMON => [
					[ 23, '', ],
				],
				self::DAY_ITINERARY_WEEK_RETRO => [
					[ 24, '' ],
				],
				self::DAY_ITINERARY_MONTH_OVERVIEW => [
					[ 16, '' ],
				],
			],
			self::HABITS => [
			],
			self::FORMAT => [ 157, 209 ],
			self::LOCALE => 'en_US.UTF-8',
			self::WEEK_NAME => 'Week',
			self::WEEK_NUMBER => 'W#',
			self::WEEKLY_RETROSPECTIVE_BOOKMARK => 'Retrospective',
			self::WEEKLY_RETROSPECTIVE_TITLE => 'Weekly retrospective',
			self::WEEKLY_TODOS => [
			],
			self::SPECIAL_DATES => [
				// Example:
				// '01-01' => "New Year!",
				// '01-04' => "April Fools' Day",
			],
			self::YEAR => (int) date( 'Y' ),
		];
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
