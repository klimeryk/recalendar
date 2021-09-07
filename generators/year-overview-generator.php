<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';
require_once __DIR__ . '/calendar-generator.php';

class YearOverviewGenerator extends Generator {
	private $year_start;
	private $year_end;

	public function __construct( \DateTimeImmutable $year_start, \DateTimeImmutable $year_end, Config $config ) {
		parent::__construct( $config );
		$this->year_start = $year_start;
		$this->year_end = $year_end;
	}

	protected function generate_anchor_string() : ?string {
		return self::get_year_overview_anchor();
	}

	protected function generate_content() : void {
		$interval = new \DateInterval( 'P1M' );
		$period = new \DatePeriod( $this->year_start, $interval, $this->year_end );

		$title = (int) $this->year_start->format( 'Y' );
		echo "<h1 class=\"year-overview__title\">$title</h1>";
		echo '<table class="year-overview__calendars">';
		foreach ( $period as $index => $month ) {
			$is_new_row = 0 === ( $index % 3 );
			if ( $is_new_row ) {
				if ( $index > 0 ) {
					echo '</tr>';
				}
				echo '<tr class="year-overview__row">';
			}
			$calendar_generator = new CalendarGenerator( $month, CalendarGenerator::HIGHLIGHT_NONE, $this->config, true );
			echo '<td class="year-overview__calendar">' . $calendar_generator->generate() . '</td>';
		}
		echo '</tr></table>';
	}
}
