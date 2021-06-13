<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';

class TitlePageGenerator extends Generator {
	private $config;

	public function __construct( Config $config ) {
		$this->config = $config;
	}

	protected function generate_anchor_string() : ?string {
		return 'title_page';
	}

	protected function generate_content() : void {
		$year = (int) $this->config->get( Config::YEAR );
?>
		<div class="title-page">
			<div class="title-page__year"><?php echo $year; ?></div>
			<div class="title-page__recalendar">ReCalendar</div>
		</div>
<?php
	}
}
