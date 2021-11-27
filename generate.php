#!/usr/bin/env php
<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if ( $argc > 1 && $argv[1] === '-c' ) {
	if ( empty( $argv[2] ) ) {
		exit( 'Please provide a path to the configuration file.' );
	}

	if ( ! file_exists( $argv[2] ) ) {
		exit( 'The provided configuration file does not exist: ' . $argv[2] );
	}

	require_once $argv[2];
} elseif ( file_exists( __DIR__ . '/local.config.php' ) ) {
	require_once __DIR__ . '/local.config.php';
}

if ( class_exists( '\ReCalendar\LocalConfig' ) ) {
	$config = new \Recalendar\LocalConfig();
} else {
	$config = new \Recalendar\Config();
}

setlocale( LC_TIME, $config->get( \ReCalendar\Config::LOCALE ) );

require_once __DIR__ . '/recalendar.php';

function l( $stuff ) : void {
	if ( is_string( $stuff ) ) {
		echo $stuff . "\n";
	} else {
		var_export( $stuff );
		echo "\n";
	}
}

$defaultConfig = ( new \Mpdf\Config\ConfigVariables() )->getDefaults();
$fontDirs = $defaultConfig['fontDir'];

$defaultFontConfig = ( new \Mpdf\Config\FontVariables() )->getDefaults();
$fontData = $defaultFontConfig['fontdata'];

$mpdf = new \Mpdf\Mpdf( [
    'fontDir' => array_merge( $fontDirs, [
        __DIR__ . $config->get( \ReCalendar\Config::FONT_DIR ),
    ] ),
    'fontdata' => $fontData + $config->get( \ReCalendar\Config::FONT_DATA ),
	'mode' => 'utf-8',
	'format' => $config->get( \ReCalendar\Config::FORMAT ),
	'default_font' => $config->get( \ReCalendar\Config::FONT_DEFAULT ),
	'margin_left' => 0,
	'margin_right' => 0,
	'margin_top' => 0,
	'margin_bottom' => 0,
	'margin_header' => 0,
	'margin_footer' => 0,
] );

$mpdf->useSubstitutions = false;

$recalendar = new \ReCalendar\ReCalendar( $mpdf, $config );

$recalendar->generate();
