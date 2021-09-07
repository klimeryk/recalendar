<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';
require_once __DIR__ . '/calendar-generator.php';

class DayEntryGenerator extends Generator {
	private $day;
	private $calendar_generator;

	public function __construct( \DateTimeImmutable $day, CalendarGenerator $calendar_generator, Config $config ) {
		parent::__construct( $config );
		$this->day = $day;
		$this->calendar_generator = $calendar_generator;
	}

	protected function generate_anchor_string() : ?string {
		return self::get_day_entry_anchor( $this->day );
	}

	protected function generate_content() : void {
		$day_number = $this->day->format( 'd' );
		$month_name = self::get_localized_month_name( $this->day, $this->config->get( Config::MONTHS ) );
		$month_overview_anchor = self::get_month_overview_anchor( $this->day );
		$calendar_html = $this->calendar_generator->generate();
		$special_items = self::get_matching_special_items( $this->day, $this->config->get( Config::SPECIAL_DATES ) );
		$previous_day_anchor = self::get_day_entry_anchor( $this->day->modify( 'yesterday' ) );
		$next_day_anchor = self::get_day_entry_anchor( $this->day->modify( 'tomorrow' ) );
?>
		<table width="100%">
			<tr>
				<td class="day-entry__month-name"><a href="#<?php echo $month_overview_anchor; ?>"><?php echo $month_name; ?></a></td>
				<td class="day-entry__previous-day"><a href="#<?php echo $previous_day_anchor; ?>">«</a></td>
				<td class="day-entry__day-number"><?php echo $day_number; ?></td>
				<td class="day-entry__next-day"><a href="#<?php echo $next_day_anchor; ?>">»</a></td>
				<td rowspan="2" class="calendar-box"><?php echo $calendar_html ?></td>
			</tr>
			<tr>
				<td colspan="4" style="border-bottom: 1px solid black; padding: 0; margin: 0;">
					<table width="100%">
						<tbody>
							<tr>
								<td class="day-entry__special-items">
<?php
									foreach ( $special_items as $index => $special_item ) {
										echo "<span class=\"day-entry__special-item\">» $special_item</span>";
										if ( $index < ( count( $special_items ) - 1 ) ) {
											echo '<br />';
										}
									}
?>
								</td>
								<td class="header-line day-entry__day-of-week"><?php echo strftime( '%A', $this->day->getTimestamp() ); ?></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</table>
<?php
		$all_itinerary_items = $this->config->get( Config::DAY_ITINERARY_ITEMS );
		$itinerary_items = $all_itinerary_items[ (int) $this->day->format( 'N' ) ] ?? $all_itinerary_items[ Config::DAY_ITINERARY_COMMON ];
		self::generate_content_box( $itinerary_items );
?>
<?php
	}
}
