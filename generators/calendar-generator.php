<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';

class CalendarGenerator extends Generator {
	public const HIGHLIGHT_NONE = 'HIGHLIGHT_NONE';
	public const HIGHLIGHT_DAY = 'HIGHLIGHT_DAY';
	public const HIGHLIGHT_WEEK = 'HIGHLIGHT_WEEK';

	private $month;
	private $date;
	private $display_full_month_name;

	public function __construct( \DateTimeImmutable $date, string $highlight_mode, Config $config, bool $display_full_month_name = false ) {
		parent::__construct( $config );
		$this->month = $date->modify( 'first day of this month' );
		$this->date = $date;
		$this->highlight_mode = $highlight_mode;
		$this->display_full_month_name = $display_full_month_name;
	}

	protected function generate_anchor_string() : ?string {
		return null;
	}

	protected function generate_content() : void {
?>
		<table class="ui-datepicker-calendar">
			<thead>
				<tr>
					<th scope="col" colspan="9" class="ui-datepicker-header">
						<?php $this->generate_header(); ?>
					</th>
				</tr>
				<tr>
					<th scope="col" class="calendar__week-number"><span><?php echo $this->config->get( Config::WEEK_NUMBER ); ?></span></th>
<?php
					$day_names_short = $this->config->get( Config::DAY_NAMES_SHORT );
					foreach ( $day_names_short as $day_name ) {
						echo "<th scope=\"col\"><span>$day_name</span></th>";
					}
?>
					<th scope="col" class="calendar__week-retrospective"><span>Re</span></th>
				</tr>
			</thead>
			<tbody>
<?php
			$starting_week = $this->month->modify( 'monday this week' );
			$end_week = $this->month->modify( 'last day of this month' );
			$week_period = new \DatePeriod( $starting_week, new \DateInterval( 'P1W' ), $end_week );
			$special_dates = $this->config->get( Config::SPECIAL_DATES );
			foreach ( $week_period as $index => $week ) {
				$week_number = self::get_week_number( $week );
				$row_css_classes = 'calendar-week';
				if ( $this->highlight_mode === self::HIGHLIGHT_WEEK && self::get_week_number( $this->date ) === $week_number ) {
					$row_css_classes .= ' highlight-week';
				}
				echo "<tr class=\"$row_css_classes\">";

				$week_overview_anchor = self::get_week_overview_anchor( $week );
				echo "<td class=\"calendar__week-number\"><a href=\"#$week_overview_anchor\">$week_number</a></td>";
				$start_of_week = $week->modify( 'monday this week' );
				$next_week = $start_of_week->modify( 'next week' );
				$week_period = new \DatePeriod( $start_of_week, new \DateInterval( 'P1D' ), $next_week );

				foreach( $week_period as $week_day ) {
					$css_classes = 'calendar-day';

					if ( $index === 0 ) {
						$css_classes .= ' first-week';
					}

					if ( $this->highlight_mode === self::HIGHLIGHT_DAY && $this->date->format( 'dmY' ) === $week_day->format( 'dmY' ) ) {
						$css_classes .= ' highlight-day';
					}

					if ( self::is_weekend( $week_day ) ) {
						$css_classes .= ' weekend-day';
					}

					if ( ! empty( self::get_matching_special_items( $week_day, $special_dates ) ) ) {
						$css_classes .= ' special-date';
					}

					if ( $week_day->format( 'm' ) !== $this->month->format( 'm' ) ) {
						$css_classes .= ' other-month';
					}

					$day_entry_anchor = self::get_day_entry_anchor( $week_day );
					$day_number = $week_day->format( 'j' );
					echo "<td class=\"$css_classes\"><a href=\"#$day_entry_anchor\">$day_number</a></td>\n";
				}

				$week_retrospective_anchor = self::get_week_retrospective_anchor( $week );
				echo "<td class=\"calendar__week-retrospective\"><a href=\"#$week_retrospective_anchor\">R</a></td>";

				echo '</tr>';
			}
?>
			</tbody>
		</table>
<?php
	}

	private function generate_header() : void {
		$month_overview_anchor = self::get_month_overview_anchor( $this->month );
		if ( $this->display_full_month_name ) {
			$month_name = self::get_localized_month_name( $this->month, $this->config->get( Config::MONTHS ) );
			echo "<a class=\"calendar__full-month-name\" href=\"#$month_overview_anchor\">$month_name</a>";
			return;
		}

		$previous_month_overview_anchor = self::get_month_overview_anchor( $this->month->modify( 'previous month' ) );
		$next_month_overview_anchor = self::get_month_overview_anchor( $this->month->modify( 'next month' ) );
		$year_overview_anchor = self::get_year_overview_anchor();
?>
		<a href="#<?php echo $previous_month_overview_anchor; ?>" class="calendar__previous-month">&nbsp;&lt;&nbsp;</a>
		<a href="#<?php echo $month_overview_anchor; ?>"><?php echo strftime( '%b', $this->month->getTimestamp() ); ?></a>
		<a href="#<?php echo $year_overview_anchor; ?>"><?php echo strftime( '%Y', $this->month->getTimestamp() ); ?></a>
		<a href="#<?php echo $next_month_overview_anchor; ?>" class="calendar__next-month">&nbsp;&gt;&nbsp;</a>
<?php
	}
}
