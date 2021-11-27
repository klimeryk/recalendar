<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/generators/calendar-generator.php';
require_once __DIR__ . '/generators/day-entry-generator.php';
require_once __DIR__ . '/generators/month-overview-generator.php';
require_once __DIR__ . '/generators/title-page-generator.php';
require_once __DIR__ . '/generators/year-overview-generator.php';
require_once __DIR__ . '/generators/week-overview-generator.php';
require_once __DIR__ . '/generators/week-retrospective-generator.php';

class ReCalendar {
	private $mpdf = null;
	private $config = null;
	private $html = '';

	private $month_overview_links = [];

	public function __construct( \Mpdf\Mpdf $mpdf, Config $config ) {
		$this->mpdf = $mpdf;
		$this->config = $config;
	}

	public function generate() {
		$stylesheet_filename = $this->config->get( Config::STYLE_SHEET );
		if ( ! file_exists( $stylesheet_filename ) ) {
			exit( 'The provided stylesheet does not exist: ' . $stylesheet_filename . PHP_EOL );
		}
		$stylesheet = file_get_contents( $stylesheet_filename );
		$this->mpdf->WriteHTML( $stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS );
		$this->generate_title_page();

		$month = sprintf( '%02d', (int) $this->config->get( Config::MONTH ) );
		$year = (int) $this->config->get( Config::YEAR );
		$start = new \DateTimeImmutable( "$year-$month-01" );
		$month_count = (int) $this->config->get( Config::MONTH_COUNT );
		$end = $start->modify( "$month_count months" );
		$year_overview_generator = new YearOverviewGenerator( $start, $end, $this->config );
		$this->add_page();
		$this->append_html( $year_overview_generator->generate() );

		$start = $start->modify( 'monday this week' );
		$interval = new \DateInterval( 'P1W' );
		$period = new \DatePeriod( $start, $interval, $end );

		foreach( $period as $week ) {
			$this->generate_week( $week, $end );

			$this->write_html();
		}

		$this->mpdf->Output( __DIR__ . '/ReCalendar.pdf', \Mpdf\Output\Destination::FILE );
	}

	private function generate_title_page() : void {
		$title_page_generator = new TitlePageGenerator( $this->config );
		$this->append_html( $title_page_generator->generate() );
	}

	private function generate_week( \DateTimeImmutable $week, \DateTimeImmutable $year_end ) : void {
		$this->generate_week_overview( $week );

		$this->generate_days_per_week( $week, $year_end );

		$this->generate_week_retrospective( $week );
	}

	private function generate_month_overview( \DateTimeImmutable $month ) : void {
		$localized_month_name = $this->config->get( Config::MONTHS )[ (int) $month->format( 'n' ) ];

		$this->add_page();
		$this->bookmark( $localized_month_name, 1 );

		$calendar_generator = new CalendarGenerator( $month, CalendarGenerator::HIGHLIGHT_NONE, $this->config );
		$month_overview_generator = new MonthOverviewGenerator( $month, $calendar_generator, $this->config );

		$this->append_html( $month_overview_generator->generate() );
	}

	private function generate_week_overview( \DateTimeImmutable $week ) : void {
		$this->add_page();
		$week_number = $this->get_week_number( $week );
		$this->bookmark( $this->config->get( Config::WEEK_NAME ) . ' ' . $week_number, 0 );

		$calendar_generator = new CalendarGenerator( $week, CalendarGenerator::HIGHLIGHT_WEEK, $this->config );
		$week_overview_generator = new WeekOverviewGenerator( $week, $calendar_generator, $this->config );
		$this->append_html( $week_overview_generator->generate() );
	}

	private function generate_days_per_week( \DateTimeImmutable $week, \DateTimeImmutable $year_end ) : void {
		$next_week = $week->modify( 'next week' );
		$week_period = new \DatePeriod( $week, new \DateInterval( 'P1D' ), $next_week );
		foreach( $week_period as $week_day ) {
			if ( (int) $week_day->format( 'j' ) === 1 && $week_day < $year_end ) {
				$this->generate_month_overview( $week_day );
			}

			$this->generate_day_entry( $week_day );
		}
	}

	private function generate_day_entry( \DateTimeImmutable $day ) : void {
		$this->add_page();

		$calendar_generator = new CalendarGenerator( $day, CalendarGenerator::HIGHLIGHT_DAY, $this->config );
		$day_entry_generator = new DayEntryGenerator( $day, $calendar_generator, $this->config );
		$this->append_html( $day_entry_generator->generate() );
	}

	private function generate_week_retrospective( \DateTimeImmutable $week ) : void {
		$this->add_page();
		$this->bookmark( $this->config->get( Config::WEEKLY_RETROSPECTIVE_BOOKMARK ), 1 );

		$calendar_generator = new CalendarGenerator( $week, CalendarGenerator::HIGHLIGHT_WEEK, $this->config );
		$week_retrospective_generator = new WeekRetrospectiveGenerator( $week, $calendar_generator, $this->config );
		$this->append_html( $week_retrospective_generator->generate() );
	}

	private static function get_week_number( \DateTimeImmutable $week ) : int {
		return (int) $week->modify( 'thursday this week' )->format( 'W' );
	}

	private function add_page() : void {
		$this->html .= '<pagebreak />';
	}

	private function append_html( string $new_html ) : void {
		$this->html .= $new_html;
	}

	private function bookmark( string $bookmark, int $level ) : void {
		$this->html .= "<bookmark content=\"$bookmark\" level=\"$level\" />";
	}

	private function write_html() : void {
		if ( empty( $this->html ) ) {
			return;
		}
		$this->mpdf->WriteHTML( $this->html );
		$this->html = '';
	}
}

