<?php

namespace ReCalendar;

class LocalConfig extends Config {
	protected function get_configuration() : array {
		return array_merge( parent::get_configuration(), [
			self::HABITS => [
				'Book',
				'Run',
				'Exercise',
				'Bike',
				'Hobby',
			],
			self::WEEKLY_TODOS => [
				'Plan a hike',
				'Schedule calls',
			],
			self::DAY_ITINERARY_ITEMS => [
				self::DAY_ITINERARY_COMMON => [
					[ 21, '', ],
					[ 2, 'Something I\'m grateful for today', ],
				],
				4 => [
					[ 21, '', ],
					[ 3, 'Something I\'m grateful for today', ],
					[ 28, 'Notes from therapy session', ],
				],
				self::DAY_ITINERARY_WEEK_RETRO => [
					[ 24, '' ],
				],
				self::DAY_ITINERARY_MONTH_OVERVIEW => [
					[ 3, 'Main goal', ],
					[ 16, 'Notes', ],
				],
			],
			self::SPECIAL_DATES => [
				'01-01' => [ 'New Years\'!' ],
				'11-01' => [ 'Some important anniversary', "John's birthday" ],
				'13-01' => [ 'Some other important date' ],
				'16-01' => [ 'Imaginary Calendar Day' ],
				'30-01' => [ 'Maybe some holiday?' ],
				'14-02' => [ 'Valentine\'s Day' ],
				'24-02' => [ 'Some super long celebration that tests how the text breaks into multiple lines' ],
				'04-05' => [ 'Star Wars Day', "Will Arnett's birthday" ],
			],
		] );
	}
}
