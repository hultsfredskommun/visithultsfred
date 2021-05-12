<?php


/*
 * Adds read more link after excerpt (to fix events calendar readmore)
 */
function modify_read_more_link() {
	return '<br/><a class="more-link" href="' . get_permalink() . '">Läs mer</a>';
}
add_filter( 'excerpt_more', 'modify_read_more_link' );

/*
 * Adds offline eventAttendanceMode to non-virtual events
 */
add_filter(
	'tribe_json_ld_event_object',
	function( $data, $args, $post ) {
		// Sanity checks @see events-virtual/src/Tribe/JSON_LD.php->modify_virtual_event() for  more context.
		// Skip any events without proper data.
		if ( empty( $data->startDate ) || empty( $data->endDate ) ) {
			return $data;
		}

		$event = tribe_get_event( $post );

		if ( ! $event instanceof \WP_Post ) {
			return $data;
		}

		/**
		 * Filters if an Event is Considered "Online" in JSON-LD context.
		 *
		 * @param boolean $virtual If an event is considered virtual.
		 * @param object  $data    The JSON-LD object.
		 * @param array   $args    The arguments used to get data.
		 * @param WP_Post $post    The post object.
		 */
		$virtual = apply_filters( 'tribe_events_virtual_single_event_online_status', $event->virtual, $data, $args, $post );

		// Bail on modifications for virtual events - let the Virtual Events plugin handle those.
		if ( $virtual ) {
			return $data;
		}

		$data->eventAttendanceMode = 'OfflineEventAttendanceMode';
		// location is set to the venue.

		return $data;
	},
	10,
	3
);







if ( class_exists( 'Tribe__Events__Main' ) ) {
add_action( 'wp', 'remove_tribe_noindex', 999 );

function remove_tribe_noindex() {
remove_action( 'wp', array( 'Tribe__Events__Main', 'issue_noindex' ) );
}
}


/* add meta desc to calendar page */
function visit_add_meta_desc_month_archive() {
/*	if ( ! is_post_type_archive( 'tribe_events' ) || ! function_exists( 'tribe_is_month' ) ) {
		return;
	}
*/
	if ( tribe_is_month() || tribe_is_event_category() || tribe_is_in_main_loop() ) {
		echo '<meta name="description" content="Kommande evenemang. Missa inget som är på gång i Hultsfreds kommun."/>' . PHP_EOL;
	}

	return;
}
add_action( 'wp_head', 'visit_add_meta_desc_month_archive' );
/*add_action('wp_head', 'kill_tribe_noindex', 1);

function kill_tribe_noindex() {
    remove_action('wp_head', array(TribeEvents::instance(), 'noindex_months'));
}
// fix to make calendar visible for Google indexing
add_filter( 'tribe_events_add_no_index_meta', '__return_false' );
*/


/* child style setup */
function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ), 1.4 );
    wp_enqueue_script( 'rek-ai', "https://static.rek.ai/80e4de3d.js");
    wp_enqueue_script( 'visit_js', get_stylesheet_directory_uri() . '/custom.js', array('jquery','jquery-ui-core','jquery-ui-widget'), 1.0, true);

    /* disable google fonts */
    wp_deregister_style('orchestrated_corona_virus_banner-font');

}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );



// Add Tribe Event Namespace
add_action( 'rss2_ns', 'events_rss2_namespace' );

function events_rss2_namespace() {
   echo 'xmlns:ev="http://purl.org/rss/2.0/modules/event/"'."n";
}

// Add Event Dates to RSS Feed
add_action('rss_item','tribe_rss_feed_add_eventdate');
add_action('rss2_item','tribe_rss_feed_add_eventdate');
add_action('commentsrss2_item','tribe_rss_feed_add_eventdate');

function tribe_rss_feed_add_eventdate() {
 if ( ! tribe_is_event() ) return;
 ?>
 <ev:tribe_event_meta xmlns:ev="Event">
 <?php if (tribe_get_start_date() !== tribe_get_end_date() ) { ?>

   <ev:startdate><?php echo tribe_get_start_date(); ?></ev:startdate>
   <ev:enddate><?php echo tribe_get_end_date(); ?></ev:enddate>

 <?php } else { ?>

   <ev:startdate><?php echo tribe_get_start_date(); ?></ev:startdate>

 <?php } ?>
 </ev:tribe_event_meta>

<?php }

/*add_action( 'pre_get_posts', 'custom_teardown_tribe_order_filter', 60 );

function custom_teardown_tribe_order_filter() {
 if ( is_feed() ) remove_filter( 'posts_orderby', array( 'Tribe__Events__Query', 'posts_orderby' ), 10, 2 );
}*/


function gt_footer(){
	echo <<<GTPOPUP
	<div id="gt_fade" class="gt_black_overlay"></div>
	<div id="gt_lightbox" class="gt_white_content notranslate">
		<div style="position:relative;height:14px;"><span onclick="closeGTPopup()" style="position:absolute;right:2px;top:2px;font-weight:bold;font-size:12px;cursor:pointer;color:#444;font-family:cursive;">X</span></div>
		<div class="gt_languages">
		<a href="https://visithultsfred.se/af/" onclick="changeGTLanguage('sv|af', this);return false;" title="Afrikaans" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/af.png" height="24" width="24" alt="af" /> <span>Afrikaans</span></a><a href="https://visithultsfred.se" onclick="changeGTLanguage('sv|sv', this);return false;" title="Swedish" class="glink nturl selected"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sv.png" height="24" width="24" alt="sv" /> <span>Swedish</span></a><a href="https://visithultsfred.se/sq/" onclick="changeGTLanguage('sv|sq', this);return false;" title="Albanian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sq.png" height="24" width="24" alt="sq" /> <span>Albanian</span></a><a href="https://visithultsfred.se/en/" onclick="changeGTLanguage('sv|en', this);return false;" title="English" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/en.png" height="24" width="24" alt="en" /> <span>English</span></a><a href="https://visithultsfred.se/de/" onclick="changeGTLanguage('sv|de', this);return false;" title="German" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/de.png" height="24" width="24" alt="de" /> <span>German</span></a><a href="https://visithultsfred.se/am/" onclick="changeGTLanguage('sv|am', this);return false;" title="Amharic" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/am.png" height="24" width="24" alt="am" /> <span>Amharic</span></a><a href="https://visithultsfred.se/ar/" onclick="changeGTLanguage('sv|ar', this);return false;" title="Arabic" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ar.png" height="24" width="24" alt="ar" /> <span>Arabic</span></a><a href="https://visithultsfred.se/hy/" onclick="changeGTLanguage('sv|hy', this);return false;" title="Armenian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/hy.png" height="24" width="24" alt="hy" /> <span>Armenian</span></a><a href="https://visithultsfred.se/az/" onclick="changeGTLanguage('sv|az', this);return false;" title="Azerbaijani" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/az.png" height="24" width="24" alt="az" /> <span>Azerbaijani</span></a><a href="https://visithultsfred.se/eu/" onclick="changeGTLanguage('sv|eu', this);return false;" title="Basque" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/eu.png" height="24" width="24" alt="eu" /> <span>Basque</span></a><a href="https://visithultsfred.se/be/" onclick="changeGTLanguage('sv|be', this);return false;" title="Belarusian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/be.png" height="24" width="24" alt="be" /> <span>Belarusian</span></a><a href="https://visithultsfred.se/bn/" onclick="changeGTLanguage('sv|bn', this);return false;" title="Bengali" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/bn.png" height="24" width="24" alt="bn" /> <span>Bengali</span></a><a href="https://visithultsfred.se/bs/" onclick="changeGTLanguage('sv|bs', this);return false;" title="Bosnian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/bs.png" height="24" width="24" alt="bs" /> <span>Bosnian</span></a><a href="https://visithultsfred.se/bg/" onclick="changeGTLanguage('sv|bg', this);return false;" title="Bulgarian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/bg.png" height="24" width="24" alt="bg" /> <span>Bulgarian</span></a><a href="https://visithultsfred.se/ca/" onclick="changeGTLanguage('sv|ca', this);return false;" title="Catalan" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ca.png" height="24" width="24" alt="ca" /> <span>Catalan</span></a><a href="https://visithultsfred.se/ceb/" onclick="changeGTLanguage('sv|ceb', this);return false;" title="Cebuano" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ceb.png" height="24" width="24" alt="ceb" /> <span>Cebuano</span></a><a href="https://visithultsfred.se/ny/" onclick="changeGTLanguage('sv|ny', this);return false;" title="Chichewa" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ny.png" height="24" width="24" alt="ny" /> <span>Chichewa</span></a><a href="https://visithultsfred.se/zh-CN/" onclick="changeGTLanguage('sv|zh-CN', this);return false;" title="Chinese (Simplified)" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/zh-CN.png" height="24" width="24" alt="zh-CN" /> <span>Chinese (Simplified)</span></a><a href="https://visithultsfred.se/zh-TW/" onclick="changeGTLanguage('sv|zh-TW', this);return false;" title="Chinese (Traditional)" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/zh-TW.png" height="24" width="24" alt="zh-TW" /> <span>Chinese (Traditional)</span></a><a href="https://visithultsfred.se/co/" onclick="changeGTLanguage('sv|co', this);return false;" title="Corsican" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/co.png" height="24" width="24" alt="co" /> <span>Corsican</span></a><a href="https://visithultsfred.se/hr/" onclick="changeGTLanguage('sv|hr', this);return false;" title="Croatian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/hr.png" height="24" width="24" alt="hr" /> <span>Croatian</span></a><a href="https://visithultsfred.se/cs/" onclick="changeGTLanguage('sv|cs', this);return false;" title="Czech" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/cs.png" height="24" width="24" alt="cs" /> <span>Czech</span></a><a href="https://visithultsfred.se/da/" onclick="changeGTLanguage('sv|da', this);return false;" title="Danish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/da.png" height="24" width="24" alt="da" /> <span>Danish</span></a><a href="https://visithultsfred.se/nl/" onclick="changeGTLanguage('sv|nl', this);return false;" title="Dutch" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/nl.png" height="24" width="24" alt="nl" /> <span>Dutch</span></a><a href="https://visithultsfred.se/eo/" onclick="changeGTLanguage('sv|eo', this);return false;" title="Esperanto" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/eo.png" height="24" width="24" alt="eo" /> <span>Esperanto</span></a><a href="https://visithultsfred.se/et/" onclick="changeGTLanguage('sv|et', this);return false;" title="Estonian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/et.png" height="24" width="24" alt="et" /> <span>Estonian</span></a><a href="https://visithultsfred.se/tl/" onclick="changeGTLanguage('sv|tl', this);return false;" title="Filipino" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/tl.png" height="24" width="24" alt="tl" /> <span>Filipino</span></a><a href="https://visithultsfred.se/fi/" onclick="changeGTLanguage('sv|fi', this);return false;" title="Finnish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/fi.png" height="24" width="24" alt="fi" /> <span>Finnish</span></a><a href="https://visithultsfred.se/fr/" onclick="changeGTLanguage('sv|fr', this);return false;" title="French" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/fr.png" height="24" width="24" alt="fr" /> <span>French</span></a><a href="https://visithultsfred.se/fy/" onclick="changeGTLanguage('sv|fy', this);return false;" title="Frisian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/fy.png" height="24" width="24" alt="fy" /> <span>Frisian</span></a><a href="https://visithultsfred.se/gl/" onclick="changeGTLanguage('sv|gl', this);return false;" title="Galician" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/gl.png" height="24" width="24" alt="gl" /> <span>Galician</span></a><a href="https://visithultsfred.se/ka/" onclick="changeGTLanguage('sv|ka', this);return false;" title="Georgian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ka.png" height="24" width="24" alt="ka" /> <span>Georgian</span></a><a href="https://visithultsfred.se/el/" onclick="changeGTLanguage('sv|el', this);return false;" title="Greek" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/el.png" height="24" width="24" alt="el" /> <span>Greek</span></a><a href="https://visithultsfred.se/gu/" onclick="changeGTLanguage('sv|gu', this);return false;" title="Gujarati" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/gu.png" height="24" width="24" alt="gu" /> <span>Gujarati</span></a><a href="https://visithultsfred.se/ht/" onclick="changeGTLanguage('sv|ht', this);return false;" title="Haitian Creole" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ht.png" height="24" width="24" alt="ht" /> <span>Haitian Creole</span></a><a href="https://visithultsfred.se/ha/" onclick="changeGTLanguage('sv|ha', this);return false;" title="Hausa" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ha.png" height="24" width="24" alt="ha" /> <span>Hausa</span></a><a href="https://visithultsfred.se/haw/" onclick="changeGTLanguage('sv|haw', this);return false;" title="Hawaiian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/haw.png" height="24" width="24" alt="haw" /> <span>Hawaiian</span></a><a href="https://visithultsfred.se/iw/" onclick="changeGTLanguage('sv|iw', this);return false;" title="Hebrew" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/iw.png" height="24" width="24" alt="iw" /> <span>Hebrew</span></a><a href="https://visithultsfred.se/hi/" onclick="changeGTLanguage('sv|hi', this);return false;" title="Hindi" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/hi.png" height="24" width="24" alt="hi" /> <span>Hindi</span></a><a href="https://visithultsfred.se/hmn/" onclick="changeGTLanguage('sv|hmn', this);return false;" title="Hmong" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/hmn.png" height="24" width="24" alt="hmn" /> <span>Hmong</span></a><a href="https://visithultsfred.se/hu/" onclick="changeGTLanguage('sv|hu', this);return false;" title="Hungarian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/hu.png" height="24" width="24" alt="hu" /> <span>Hungarian</span></a><a href="https://visithultsfred.se/is/" onclick="changeGTLanguage('sv|is', this);return false;" title="Icelandic" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/is.png" height="24" width="24" alt="is" /> <span>Icelandic</span></a><a href="https://visithultsfred.se/ig/" onclick="changeGTLanguage('sv|ig', this);return false;" title="Igbo" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ig.png" height="24" width="24" alt="ig" /> <span>Igbo</span></a><a href="https://visithultsfred.se/id/" onclick="changeGTLanguage('sv|id', this);return false;" title="Indonesian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/id.png" height="24" width="24" alt="id" /> <span>Indonesian</span></a><a href="https://visithultsfred.se/ga/" onclick="changeGTLanguage('sv|ga', this);return false;" title="Irish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ga.png" height="24" width="24" alt="ga" /> <span>Irish</span></a><a href="https://visithultsfred.se/it/" onclick="changeGTLanguage('sv|it', this);return false;" title="Italian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/it.png" height="24" width="24" alt="it" /> <span>Italian</span></a><a href="https://visithultsfred.se/ja/" onclick="changeGTLanguage('sv|ja', this);return false;" title="Japanese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ja.png" height="24" width="24" alt="ja" /> <span>Japanese</span></a><a href="https://visithultsfred.se/jw/" onclick="changeGTLanguage('sv|jw', this);return false;" title="Javanese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/jw.png" height="24" width="24" alt="jw" /> <span>Javanese</span></a><a href="https://visithultsfred.se/kn/" onclick="changeGTLanguage('sv|kn', this);return false;" title="Kannada" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/kn.png" height="24" width="24" alt="kn" /> <span>Kannada</span></a><a href="https://visithultsfred.se/kk/" onclick="changeGTLanguage('sv|kk', this);return false;" title="Kazakh" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/kk.png" height="24" width="24" alt="kk" /> <span>Kazakh</span></a><a href="https://visithultsfred.se/ko/" onclick="changeGTLanguage('sv|ko', this);return false;" title="Korean" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ko.png" height="24" width="24" alt="ko" /> <span>Korean</span></a><a href="https://visithultsfred.se/ky/" onclick="changeGTLanguage('sv|ky', this);return false;" title="Kyrgyz" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ky.png" height="24" width="24" alt="ky" /> <span>Kyrgyz</span></a><a href="https://visithultsfred.se/lo/" onclick="changeGTLanguage('sv|lo', this);return false;" title="Lao" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/lo.png" height="24" width="24" alt="lo" /> <span>Lao</span></a><a href="https://visithultsfred.se/lv/" onclick="changeGTLanguage('sv|lv', this);return false;" title="Latvian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/lv.png" height="24" width="24" alt="lv" /> <span>Latvian</span></a><a href="https://visithultsfred.se/lt/" onclick="changeGTLanguage('sv|lt', this);return false;" title="Lithuanian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/lt.png" height="24" width="24" alt="lt" /> <span>Lithuanian</span></a><a href="https://visithultsfred.se/lb/" onclick="changeGTLanguage('sv|lb', this);return false;" title="Luxembourgish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/lb.png" height="24" width="24" alt="lb" /> <span>Luxembourgish</span></a><a href="https://visithultsfred.se/mk/" onclick="changeGTLanguage('sv|mk', this);return false;" title="Macedonian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mk.png" height="24" width="24" alt="mk" /> <span>Macedonian</span></a><a href="https://visithultsfred.se/mg/" onclick="changeGTLanguage('sv|mg', this);return false;" title="Malagasy" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mg.png" height="24" width="24" alt="mg" /> <span>Malagasy</span></a><a href="https://visithultsfred.se/ms/" onclick="changeGTLanguage('sv|ms', this);return false;" title="Malay" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ms.png" height="24" width="24" alt="ms" /> <span>Malay</span></a><a href="https://visithultsfred.se/ml/" onclick="changeGTLanguage('sv|ml', this);return false;" title="Malayalam" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ml.png" height="24" width="24" alt="ml" /> <span>Malayalam</span></a><a href="https://visithultsfred.se/mt/" onclick="changeGTLanguage('sv|mt', this);return false;" title="Maltese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mt.png" height="24" width="24" alt="mt" /> <span>Maltese</span></a><a href="https://visithultsfred.se/mi/" onclick="changeGTLanguage('sv|mi', this);return false;" title="Maori" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mi.png" height="24" width="24" alt="mi" /> <span>Maori</span></a><a href="https://visithultsfred.se/mr/" onclick="changeGTLanguage('sv|mr', this);return false;" title="Marathi" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mr.png" height="24" width="24" alt="mr" /> <span>Marathi</span></a><a href="https://visithultsfred.se/mn/" onclick="changeGTLanguage('sv|mn', this);return false;" title="Mongolian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/mn.png" height="24" width="24" alt="mn" /> <span>Mongolian</span></a><a href="https://visithultsfred.se/my/" onclick="changeGTLanguage('sv|my', this);return false;" title="Myanmar (Burmese)" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/my.png" height="24" width="24" alt="my" /> <span>Myanmar (Burmese)</span></a><a href="https://visithultsfred.se/ne/" onclick="changeGTLanguage('sv|ne', this);return false;" title="Nepali" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ne.png" height="24" width="24" alt="ne" /> <span>Nepali</span></a><a href="https://visithultsfred.se/no/" onclick="changeGTLanguage('sv|no', this);return false;" title="Norwegian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/no.png" height="24" width="24" alt="no" /> <span>Norwegian</span></a><a href="https://visithultsfred.se/ps/" onclick="changeGTLanguage('sv|ps', this);return false;" title="Pashto" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ps.png" height="24" width="24" alt="ps" /> <span>Pashto</span></a><a href="https://visithultsfred.se/fa/" onclick="changeGTLanguage('sv|fa', this);return false;" title="Persian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/fa.png" height="24" width="24" alt="fa" /> <span>Persian</span></a><a href="https://visithultsfred.se/pl/" onclick="changeGTLanguage('sv|pl', this);return false;" title="Polish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/pl.png" height="24" width="24" alt="pl" /> <span>Polish</span></a><a href="https://visithultsfred.se/pt/" onclick="changeGTLanguage('sv|pt', this);return false;" title="Portuguese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/pt.png" height="24" width="24" alt="pt" /> <span>Portuguese</span></a><a href="https://visithultsfred.se/pa/" onclick="changeGTLanguage('sv|pa', this);return false;" title="Punjabi" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/pa.png" height="24" width="24" alt="pa" /> <span>Punjabi</span></a><a href="https://visithultsfred.se/ro/" onclick="changeGTLanguage('sv|ro', this);return false;" title="Romanian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ro.png" height="24" width="24" alt="ro" /> <span>Romanian</span></a><a href="https://visithultsfred.se/ru/" onclick="changeGTLanguage('sv|ru', this);return false;" title="Russian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ru.png" height="24" width="24" alt="ru" /> <span>Russian</span></a><a href="https://visithultsfred.se/sm/" onclick="changeGTLanguage('sv|sm', this);return false;" title="Samoan" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sm.png" height="24" width="24" alt="sm" /> <span>Samoan</span></a><a href="https://visithultsfred.se/gd/" onclick="changeGTLanguage('sv|gd', this);return false;" title="Scottish Gaelic" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/gd.png" height="24" width="24" alt="gd" /> <span>Scottish Gaelic</span></a><a href="https://visithultsfred.se/sr/" onclick="changeGTLanguage('sv|sr', this);return false;" title="Serbian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sr.png" height="24" width="24" alt="sr" /> <span>Serbian</span></a><a href="https://visithultsfred.se/st/" onclick="changeGTLanguage('sv|st', this);return false;" title="Sesotho" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/st.png" height="24" width="24" alt="st" /> <span>Sesotho</span></a><a href="https://visithultsfred.se/sn/" onclick="changeGTLanguage('sv|sn', this);return false;" title="Shona" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sn.png" height="24" width="24" alt="sn" /> <span>Shona</span></a><a href="https://visithultsfred.se/sd/" onclick="changeGTLanguage('sv|sd', this);return false;" title="Sindhi" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sd.png" height="24" width="24" alt="sd" /> <span>Sindhi</span></a><a href="https://visithultsfred.se/si/" onclick="changeGTLanguage('sv|si', this);return false;" title="Sinhala" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/si.png" height="24" width="24" alt="si" /> <span>Sinhala</span></a><a href="https://visithultsfred.se/sk/" onclick="changeGTLanguage('sv|sk', this);return false;" title="Slovak" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sk.png" height="24" width="24" alt="sk" /> <span>Slovak</span></a><a href="https://visithultsfred.se/sl/" onclick="changeGTLanguage('sv|sl', this);return false;" title="Slovenian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sl.png" height="24" width="24" alt="sl" /> <span>Slovenian</span></a><a href="https://visithultsfred.se/so/" onclick="changeGTLanguage('sv|so', this);return false;" title="Somali" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/so.png" height="24" width="24" alt="so" /> <span>Somali</span></a><a href="https://visithultsfred.se/es/" onclick="changeGTLanguage('sv|es', this);return false;" title="Spanish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/es.png" height="24" width="24" alt="es" /> <span>Spanish</span></a><a href="https://visithultsfred.se/su/" onclick="changeGTLanguage('sv|su', this);return false;" title="Sudanese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/su.png" height="24" width="24" alt="su" /> <span>Sudanese</span></a><a href="https://visithultsfred.se/sw/" onclick="changeGTLanguage('sv|sw', this);return false;" title="Swahili" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/sw.png" height="24" width="24" alt="sw" /> <span>Swahili</span></a><a href="https://visithultsfred.se/tg/" onclick="changeGTLanguage('sv|tg', this);return false;" title="Tajik" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/tg.png" height="24" width="24" alt="tg" /> <span>Tajik</span></a><a href="https://visithultsfred.se/ta/" onclick="changeGTLanguage('sv|ta', this);return false;" title="Tamil" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ta.png" height="24" width="24" alt="ta" /> <span>Tamil</span></a><a href="https://visithultsfred.se/te/" onclick="changeGTLanguage('sv|te', this);return false;" title="Telugu" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/te.png" height="24" width="24" alt="te" /> <span>Telugu</span></a><a href="https://visithultsfred.se/th/" onclick="changeGTLanguage('sv|th', this);return false;" title="Thai" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/th.png" height="24" width="24" alt="th" /> <span>Thai</span></a><a href="https://visithultsfred.se/tr/" onclick="changeGTLanguage('sv|tr', this);return false;" title="Turkish" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/tr.png" height="24" width="24" alt="tr" /> <span>Turkish</span></a><a href="https://visithultsfred.se/uk/" onclick="changeGTLanguage('sv|uk', this);return false;" title="Ukrainian" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/uk.png" height="24" width="24" alt="uk" /> <span>Ukrainian</span></a><a href="https://visithultsfred.se/ur/" onclick="changeGTLanguage('sv|ur', this);return false;" title="Urdu" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/ur.png" height="24" width="24" alt="ur" /> <span>Urdu</span></a><a href="https://visithultsfred.se/uz/" onclick="changeGTLanguage('sv|uz', this);return false;" title="Uzbek" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/uz.png" height="24" width="24" alt="uz" /> <span>Uzbek</span></a><a href="https://visithultsfred.se/vi/" onclick="changeGTLanguage('sv|vi', this);return false;" title="Vietnamese" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/vi.png" height="24" width="24" alt="vi" /> <span>Vietnamese</span></a><a href="https://visithultsfred.se/cy/" onclick="changeGTLanguage('sv|cy', this);return false;" title="Welsh" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/cy.png" height="24" width="24" alt="cy" /> <span>Welsh</span></a><a href="https://visithultsfred.se/xh/" onclick="changeGTLanguage('sv|xh', this);return false;" title="Xhosa" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/xh.png" height="24" width="24" alt="xh" /> <span>Xhosa</span></a><a href="https://visithultsfred.se/yo/" onclick="changeGTLanguage('sv|yo', this);return false;" title="Yoruba" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/yo.png" height="24" width="24" alt="yo" /> <span>Yoruba</span></a><a href="https://visithultsfred.se/zu/" onclick="changeGTLanguage('sv|zu', this);return false;" title="Zulu" class="glink nturl"><img data-gt-lazy-src="//visithultsfred.se/wp-content/plugins/gtranslate/flags/24/zu.png" height="24" width="24" alt="zu" /> <span>Zulu</span></a></div>
	</div>
	<style>
		.gt_black_overlay {display:none;position:fixed;top:0%;left:0%;width:100%;height:100%;background-color:black;z-index:2017;-moz-opacity:0.8;opacity:.80;filter:alpha(opacity=80);}
		.gt_white_content {display:none;position:fixed;top:calc(50% - 200px);left:10%;width:80%;height:400px;padding:6px 16px;border-radius:5px;background-color:white;color:black;z-index:19881205;overflow:auto;text-align:left;}
		.gt_white_content a {display:block;padding:5px 0;border-bottom:1px solid #e7e7e7;white-space:nowrap;}
		.gt_white_content a:last-of-type {border-bottom:none;}
		.gt_white_content a.selected {background-color:#ffc;}
		.gt_white_content .gt_languages {column-count:5;column-gap:10px;}
		.gt_white_content::-webkit-scrollbar-track{-webkit-box-shadow:inset 0 0 3px rgba(0,0,0,0.3);border-radius:5px;background-color:#F5F5F5;}
		.gt_white_content::-webkit-scrollbar {width:5px;}
		.gt_white_content::-webkit-scrollbar-thumb {border-radius:5px;-webkit-box-shadow: inset 0 0 3px rgba(0,0,0,.3);background-color:#888;}
	</style>

	<script>
		function openGTPopup(a) {jQuery('.gt_white_content a img').each(function() {if(!jQuery(this)[0].hasAttribute('src'))jQuery(this).attr('src', jQuery(this).attr('data-gt-lazy-src'))});if(a === undefined){document.getElementById('gt_lightbox').style.display='block';document.getElementById('gt_fade').style.display='block';}else{jQuery(a).parent().find('#gt_lightbox').css('display', 'block');jQuery(a).parent().find('#gt_fade').css('display', 'block');}}
		function closeGTPopup() {jQuery('.gt_white_content').css('display', 'none');jQuery('.gt_black_overlay').css('display', 'none');}
		function changeGTLanguage(pair, a) {doGTranslate(pair);jQuery('a.switcher-popup').html(jQuery(a).html()+'<span style="color:#666;font-size:8px;font-weight:bold;">&#9660;</span>');closeGTPopup();}
		jQuery('.gt_black_overlay').click(function(e) {if(jQuery('.gt_white_content').is(':visible')) {closeGTPopup()}});
	</script>


	<script>
		function doGTranslate(lang_pair) {if(lang_pair.value)lang_pair=lang_pair.value;if(lang_pair=='')return;var lang=lang_pair.split('|')[1];if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent', 'GTranslate', lang, location.pathname+location.search]);}else {if(typeof ga!='undefined')ga('send', 'event', 'GTranslate', lang, location.pathname+location.search);}var plang=location.pathname.split('/')[1];if(plang.length !=2 && plang != 'zh-CN' && plang != 'zh-TW' && plang != 'hmn' && plang != 'haw' && plang != 'ceb')plang='sv';if(lang == 'sv')location.href=location.protocol+'//'+location.host+gt_request_uri;else location.href=location.protocol+'//'+location.host+'/'+lang+gt_request_uri;}
	</script>
GTPOPUP;

}
add_action( 'wp_footer', 'gt_footer', 5 );
