<?php
/**
 * Created by Buffercode.
 * User: M A Vinoth Kumar
 */

/**
 * Append the Version -- Extra
 */
add_filter( 'fed_plugin_versions', function ( $version ) {
	return array_merge( $version, array( 'extra' => 'Extra ('.BC_FED_EXTRA_PLUGIN_VERSION.')' ) );
} );

function fed_get_date_formats() {
	$date_formats = array(
		'd-m-Y' => 'Date-Month-Year',
		'm-d-Y' => 'Month-Date-Year',
	);

	return apply_filters( 'fed_get_date_formats_filter', $date_formats );
}

function fed_get_date_mode() {
	return array( 'single' => 'Single', 'multiple' => 'Multiple', 'range' => 'Range' );
}

add_action( 'fed_enqueue_script_style_admin', 'fede_wp_enqueue_scripts' );
add_action( 'fed_enqueue_script_style_frontend', 'fede_wp_enqueue_scripts' );
function fede_wp_enqueue_scripts() {
	wp_enqueue_script( 'fede_script', plugins_url( 'assets/script.js', BC_FED_EXTRA_PLUGIN ), array( 'jquery' ), BC_FED_EXTRA_PLUGIN_VERSION );

}


function fed_image_mime_types() {
	return  array(
		'video/x-ms-asf'   => site_url() . '/wp-includes/images/media/video.png"',
		'video/x-ms-wmv'   => site_url() . '/wp-includes/images/media/video.png"',
		'video/x-ms-wmx'   => site_url() . '/wp-includes/images/media/video.png"',
		'video/x-ms-wm'    => site_url() . '/wp-includes/images/media/video.png"',
		'video/avi'        => site_url() . '/wp-includes/images/media/video.png"',
		'video/divx'       => site_url() . '/wp-includes/images/media/video.png"',
		'video/x-flv'      => site_url() . '/wp-includes/images/media/video.png"',
		'video/quicktime'  => site_url() . '/wp-includes/images/media/video.png"',
		'video/mpeg'       => site_url() . '/wp-includes/images/media/video.png"',
		'video/mp4'        => site_url() . '/wp-includes/images/media/video.png"',
		'video/ogg'        => site_url() . '/wp-includes/images/media/video.png"',
		'video/webm'       => site_url() . '/wp-includes/images/media/video.png"',
		'video/x-matroska' => site_url() . '/wp-includes/images/media/video.png"',
		'video/3gpp'       => site_url() . '/wp-includes/images/media/video.png"',
		'video/3gpp2'      => site_url() . '/wp-includes/images/media/video.png"',

		'text/plain'                => site_url() . '/wp-includes/images/media/text.png"',
		'text/csv'                  => site_url() . '/wp-includes/images/media/text.png"',
		'text/tab-separated-values' => site_url() . '/wp-includes/images/media/text.png"',
		'text/calendar'             => site_url() . '/wp-includes/images/media/text.png"',
		'text/richtext'             => site_url() . '/wp-includes/images/media/text.png"',
		'text/css'                  => site_url() . '/wp-includes/images/media/text.png"',
		'text/html'                 => site_url() . '/wp-includes/images/media/text.png"',
		'text/vtt'                  => site_url() . '/wp-includes/images/media/text.png"',
		'application/ttaf+xml'      => site_url() . '/wp-includes/images/media/text.png"',

		'audio/mpeg'        => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/x-realaudio' => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/wav'         => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/ogg'         => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/midi'        => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/x-ms-wma'    => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/x-ms-wax'    => site_url() . '/wp-includes/images/media/audio.png"',
		'audio/x-matroska'  => site_url() . '/wp-includes/images/media/audio.png"',

		'application/rtf'               => site_url() . '/wp-includes/images/media/archive.png"',
		'application/javascript'        => site_url() . '/wp-includes/images/media/archive.png"',
		'application/pdf'               => site_url() . '/wp-includes/images/media/archive.png"',
		'application/x-shockwave-flash' => site_url() . '/wp-includes/images/media/archive.png"',
		'application/java'              => site_url() . '/wp-includes/images/media/archive.png"',
		'application/x-tar'             => site_url() . '/wp-includes/images/media/archive.png"',
		'application/zip'               => site_url() . '/wp-includes/images/media/archive.png"',
		'application/x-gzip'            => site_url() . '/wp-includes/images/media/archive.png"',
		'application/rar'               => site_url() . '/wp-includes/images/media/archive.png"',
		'application/x-7z-compressed'   => site_url() . '/wp-includes/images/media/archive.png"',
		'application/x-msdownload'      => site_url() . '/wp-includes/images/media/archive.png"',
		'application/octet-stream'      => site_url() . '/wp-includes/images/media/archive.png"',
		'application/octet-stream'      => site_url() . '/wp-includes/images/media/document.png"',


		'application/msword'                                                        => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint'                                             => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-write'                                                  => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-excel'                                                  => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-access'                                                 => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-project'                                                => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-word.document.macroEnabled.12'                          => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template'   => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-word.template.macroEnabled.12'                          => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-excel.sheet.macroEnabled.12'                            => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-excel.sheet.binary.macroEnabled.12'                     => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template'      => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-excel.template.macroEnabled.12'                         => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-excel.addin.macroEnabled.12'                            => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint.presentation.macroEnabled.12'                => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow'    => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'                   => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.presentationml.template'     => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint.template.macroEnabled.12'                    => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint.addin.macroEnabled.12'                       => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.openxmlformats-officedocument.presentationml.slide'        => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-powerpoint.slide.macroEnabled.12'                       => site_url() . '/wp-includes/images/media/document.png"',
		'application/onenote'                                                       => site_url() . '/wp-includes/images/media/document.png"',
		'application/oxps'                                                          => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.ms-xpsdocument'                                            => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.text'                                   => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.presentation'                           => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.spreadsheet'                            => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.graphics'                               => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.chart'                                  => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.database'                               => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.oasis.opendocument.formula'                                => site_url() . '/wp-includes/images/media/document.png"',
		'application/wordperfect'                                                   => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.apple.keynote'                                             => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.apple.numbers'                                             => site_url() . '/wp-includes/images/media/document.png"',
		'application/vnd.apple.pages'                                               => site_url() . '/wp-includes/images/media/document.png"',
	);
}