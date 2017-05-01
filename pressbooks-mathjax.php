<?php

/**
 * Plugin Name: Pressbooks Mathjax
 * Description: Helper plugin to add Mathjax to list of proprietary latex renderers.
 * Version: 1.0
 * Author: Lumen Learning
 * Author URI: http://lumenlearning.com
 * Text Domain: lumen
 * License: GPLv2 or later
 * GitHub Plugin URI: https://github.com/lumenlearning/pressbooks-mathjax
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Requires the MathJax class.
 *
 * @param string $class
 */
function pbmj_init( $class ) {
	if ( 'mathjax' == $class ) {
		require_once ( __DIR__ . '/latex/mathjax.php' );
	}
}
add_filter( 'pb_require_latex', 'pbmj_init' );

/**
 * Adds MathJax renderer option to select field.
 *
 * @param array $options
 *
 * @return array
 */
function pbmj_add_renderer_option( array $options ) {
	$options['mathjax'] = __( 'MathJax in-browser', 'pb-latex' );
	return $options;
}
add_filter( 'pb_add_latex_renderer_option', 'pbmj_add_renderer_option' );

/**
 * Adds MathJax renderer to proprietary list of latex renderers.
 *
 * @param array $renderers
 *
 * @return array
 */
function pbmj_add_renderer_type( array $renderers ) {
	$renderers['mathjax'] = 'mathjax';
	return $renderers;
}
add_filter( 'pb_latex_renderers', 'pbmj_add_renderer_type' );

/**
 * Enqueues necessary scripts.
 *
 * @param string $methods
 */
function pbmj_enqueue_scripts( $method ) {
	if ( 'mathjax' == $method ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'pb_mathjax', 'https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML.js&delayStartupUntil=configured' );
	}
}
add_action( 'pb_enqueue_latex_scripts', 'pbmj_enqueue_scripts' );

/**
 * Echos the scripts in the head of the page needed for MathJax.
 *
 * @param string $method
 */
function pbmj_config_scripts( $method ) {
	if ( 'mathjax' == $method ) {
		echo '<script type="text/x-mathjax-config">
			MathJax.Hub.Config({
				TeX: { extensions: ["cancel.js", "mhchem.js"] },
				tex2jax: {inlineMath: [["[latex]","[/latex]"]] }
			});
		</script>
		<script type="text/javascript">
			MathJax.Hub.Configured();
		</script>';
	}
}
add_filter( 'pb_add_latex_config_scripts', 'pbmj_config_scripts' );

/**
 * MathJax latex shortcode
 *
 * @param $_atts, $latex
 *
 * @return shortcode
 */
function pbmj_short_code( $_atts, $latex ) {
	$latex = preg_replace( array( '#<br\s*/?>#i', '#</?p>#i' ), ' ', $latex );

	$latex = str_replace(
		array( '&quot;', '&#8220;', '&#8221;', '&#039;', '&#8125;', '&#8127;', '&#8217;', '&#038;', '&amp;', "\n", "\r", "\xa0", '&#8211;' ), array( '"', '``', "''", "'", "'", "'", "'", '&', '&', ' ', ' ', ' ', '-' ), $latex
	);

	return "[latex]" . $latex . "[/latex]";
}
add_shortcode( 'latex', 'pbmj_short_code' );
