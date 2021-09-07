<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';
require_once __DIR__ . '/calendar-generator.php';

class WeekRetrospectiveGenerator extends Generator {
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
		return self::get_week_retrospective_anchor( $this->week );
	}

	protected function generate_content() : void {
		$calendar_html = $this->calendar_generator->generate();
		$week_start = strftime( '%d %B', $this->week->modify( 'monday this week' )->getTimestamp() );
		$week_end = strftime( '%d %B', $this->week->modify( 'sunday this week' )->getTimestamp() );
		$week_overview_anchor = self::get_week_overview_anchor( $this->week );
		$previous_week_retrospective_anchor = self::get_week_retrospective_anchor( $this->week->modify( 'previous week' ) );
		$next_week_retrospective_anchor = self::get_week_retrospective_anchor( $this->week->modify( 'next week' ) );
?>
		<table width="100%">
			<tr>
				<td class="week-retrospective__week-name"><?php echo $this->config->get( Config::WEEKLY_RETROSPECTIVE_TITLE ); ?></td>
				<td class="week-retrospective__previous-week"><a href="#<?php echo $previous_week_retrospective_anchor; ?>">«</a></td>
				<td class="week-retrospective__day-number"><a href="#<?php echo $week_overview_anchor; ?>"><?php echo $this->week_number; ?></a></td>
				<td class="week-retrospective__next-week"><a href="<?php echo $next_week_retrospective_anchor; ?>">»</a></td>
				<td rowspan="2" class="calendar-box"><?php echo $calendar_html ?></td>
			</tr>
			<tr>
				<td colspan="4" class="header-line week-retrospective__range"><?php echo $week_start; ?> - <?php echo $week_end; ?></td>
			</tr>
		</table>
<?php
		$all_itinerary_items = $this->config->get( Config::DAY_ITINERARY_ITEMS );
		$itinerary_items = $all_itinerary_items[ Config::DAY_ITINERARY_WEEK_RETRO ] ?? $all_itinerary_items[ Config::DAY_ITINERARY_COMMON ];
		self::generate_content_box( $itinerary_items );
?>
<?php
	}
}
