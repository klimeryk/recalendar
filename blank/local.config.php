<?php

namespace ReCalendar;

class LocalConfig extends Config {
	protected function get_configuration() : array {
		return array_merge( parent::get_configuration(), [
			self::HABITS => [
				'',
				'',
				'',
				'',
				'',
			],
			self::DAY_ITINERARY_ITEMS => [
				self::DAY_ITINERARY_COMMON => [
					[ 23, '', ],
				],
				self::DAY_ITINERARY_WEEK_RETRO => [
					[ 23, '' ],
				],
				self::DAY_ITINERARY_MONTH_OVERVIEW => [
					[ 19, '', ],
				],
			],
		] );
	}
}
