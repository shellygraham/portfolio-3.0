<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package some_like_it_neat
 */
?>
<!DOCTYPE html>
<?php tha_html_before(); ?>
<html <?php language_attributes(); ?>>

<head>

    <?php tha_head_top(); ?>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<style type="text/css">
		<?php if ( 'no' === get_theme_mod( 'some-like-it-neat_post_format_support' ) ): ?>
			h1.entry-title:before {
				display: none;
			}
		<?php endif; ?>
	</style>

    <?php tha_head_bottom(); ?>
    <?php wp_head(); ?>
    <script src="https://use.typekit.net/hty2dzo.js"></script>
	<script>try{Typekit.load({ async: false });}catch(e){}</script>
</head>

<body <?php body_class(); ?>>
<?php tha_body_top(); ?>

<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'some-like-it-neat' ); ?></a>

		<?php tha_header_before(); ?>
		<header id="masthead" class="site-header wrap" role="banner" itemscope="itemscope" itemtype="http://schema.org/WPHeader">

		<?php tha_header_top(); ?>

			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="logo-anchor"><h1><?php bloginfo( 'name' ); ?></h1></a>

			<nav id="primary-nav" role="navigation" itemscope="itemscope" itemtype="http://schema.org/SiteNavigationElement">
				<button class="menu-button">
					<span class="dashicons <?php echo get_theme_mod( 'some_like_it_neat_mobile_nav_icon', 'dashicons-menu' ); ?>"></span>
				</button>
		        <?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary-navigation',
							'menu_class' => 'flexnav', //Adding the class for FlexNav
							'items_wrap' => '<ul data-breakpoint=" '. esc_attr( get_theme_mod( 'some_like_it_neat_mobile_min_width', '768' ) ) .' " id="%1$s" class="%2$s">%3$s</ul>', // Adding data-breakpoint for FlexNav
						)
					);
				?>

			</nav><!-- #site-navigation -->

			<?php tha_header_bottom(); ?>

		</header><!-- #masthead -->
		<?php tha_header_after(); ?>

		<?php tha_content_before(); ?>

		<main id="main" class="site-main wrap" role="main">
			<?php tha_content_top(); ?>
