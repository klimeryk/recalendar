<?php
declare(strict_types=1);

namespace ReCalendar;

require_once __DIR__ . '/generator.php';

class TitlePageGenerator extends Generator {
	protected function generate_anchor_string() : ?string {
		return 'title_page';
	}

	protected function generate_content() : void {
		$year = (int) $this->config->get( Config::YEAR );
		$subtitle = $this->config->get( Config::SUBTITLE );
?>
		<div class="title-page">
			<div class="title-page__year"><?php echo $year; ?></div>
			<div class="title-page__recalendar"><?php echo $subtitle ?></div>
		</div>
<?php
	}
}
