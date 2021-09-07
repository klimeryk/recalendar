<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';
require_once __DIR__ . '/calendar-generator.php';

class WeekOverviewGenerator extends Generator {
	private $week;
	private $week_number;
	private $calendar_generator;

	public function __construct( \DateTimeImmutable $week, CalendarGenerator $calendar_generator, Config $config ) {
		parent::__construct( $config );
		$this->week = $week;
		$this->week_number = self::get_week_number( $week );
		$this->calendar_generator = $calendar_generator;
	}

	protected function generate_anchor_string() : ?string {
		return self::get_week_overview_anchor( $this->week );
	}

	protected function generate_content() : void {
		$calendar_html = $this->calendar_generator->generate();
		$week_start = strftime( '%d %B', $this->week->modify( 'monday this week' )->getTimestamp() );
		$week_end = strftime( '%d %B', $this->week->modify( 'sunday this week' )->getTimestamp() );

		$previous_week_anchor = self::get_week_overview_anchor( $this->week->modify( 'previous week' ) );
		$next_week_anchor = self::get_week_overview_anchor( $this->week->modify( 'next week' ) );
?>
		<table width="100%">
			<tr>
				<td class="week-overview__week-name"><?php echo $this->config->get( Config::WEEK_NAME ); ?></td>
				<td class="week-overview__previous-week"><a href="#<?php echo $previous_week_anchor; ?>">«</a></td>
				<td class="week-overview__day-number"><?php echo $this->week_number; ?></td>
				<td class="week-overview__next-week"><a href="#<?php echo $next_week_anchor; ?>">»</a></td>
				<td rowspan="2" class="calendar-box"><?php echo $calendar_html ?></td>
			</tr>
			<tr>
				<td colspan="4" class="header-line week-overview__range"><?php echo $week_start; ?> - <?php echo $week_end; ?></td>
			</tr>
		</table>
		<table class="content-box" height="100%">
<?php
		$month_start_week_number = self::get_week_number( $this->week->modify( 'first day of this month' )->modify( 'monday this week' ) );
		$month_end_week_number = self::get_week_number( $this->week->modify( 'last day of this month' )->modify( 'monday this week' ) );
		$day_entry_height = self::get_day_entry_height( $month_start_week_number, $month_end_week_number );
		$next_week = $this->week->modify( 'next week' );
		$week_period = new \DatePeriod( $this->week, new \DateInterval( 'P1D' ), $next_week );
		$week_days = [];
		foreach ( $week_period as $week_day ) {
			$week_days[] = $week_day;
		}
?>
			<tr>
				<?php $this->generate_day_entry( $week_days[0], $day_entry_height ); ?>
				<?php $this->generate_day_entry( $week_days[1], $day_entry_height ); ?>
				<?php $this->generate_day_entry( $week_days[2], $day_entry_height ); ?>
			</tr>
			<tr>
				<?php $this->generate_day_entry( $week_days[3], $day_entry_height ); ?>
				<?php $this->generate_day_entry( $week_days[4], $day_entry_height ); ?>
				<?php $this->generate_day_entry( $week_days[5], $day_entry_height ); ?>
			</tr>
			<tr>
				<?php $this->generate_day_entry( $week_days[6], $day_entry_height ); ?>
				<td colspan="2" class="week-overview__notes">
<?php
					$weekly_todos = $this->config->get( Config::WEEKLY_TODOS );
					foreach ( $weekly_todos as $weekly_todo ) {
						echo "<span>$weekly_todo</span><br />";
					}
?>
				</td>
			</tr>
		</table>
<?php
	}

	private function generate_day_entry( \DateTimeImmutable $week_day, int $day_entry_height ) {
		$special_items = self::get_matching_special_items( $week_day, $this->config->get( Config::SPECIAL_DATES ) );
?>
	<td class="week-overview__day-entry" style="height: <?php echo $day_entry_height; ?>px;">
			<table width="100%">
				<tr height="100%">
					<td class="week-overview__day-of-week"><a href="#<?php echo self::get_day_entry_anchor( $week_day ); ?>"><?php echo strftime( '%A', $week_day->getTimestamp() ); ?></a></td>
					<td class="week-overview__date"><a href="#<?php echo self::get_day_entry_anchor( $week_day ); ?>"><?php echo strftime( '%d %b', $week_day->getTimestamp() ); ?></a></td>
				</tr>
<?php
				foreach ( $special_items as $special_item ) {
					echo "<tr><td colspan=\"2\" class=\"week-overview__special-item\">» $special_item</td></tr>";
				}
?>
			</table>
		</td>
<?php
	}

	private static function get_day_entry_height( int $start_week_number, int $end_week_number ) : int {
		if ( $start_week_number > $end_week_number ) {
			// Edge case when the Jan 1st falls on Week 53 of the previous year
			// See 2020/2021
			$start_week_number = 0;
		}

		$number_of_weeks_in_month = $end_week_number - $start_week_number + 1;
		switch ( $number_of_weeks_in_month ) {
			case 6:
				return 208;

			case 4:
				return 221;

			case 5:
			default:
				return 215;
		}
	}
}
