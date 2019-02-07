<?php

// Don't judge - I'm lazy...

namespace SsnTestKit\Tests\Server;

require __DIR__ . '/../../vendor/autoload.php';

$app = new \Slim\App();

function render( string $template, array $data = [] ) {
	extract( $data, EXTR_SKIP );

	ob_start();

	include __DIR__ . "/views/{$template}";

	$html = ob_get_contents();
	ob_end_clean();

	return $html;
}

function register_routes( $app ) {
	$app->get( '/', function( $request, $response ) {
		return $response->write( render('home.php') );
	} );

	$app->get( '/js-delayed-visibility', function( $request, $response ) {
		return $response->write( render( 'js-delayed-visibility.php' ) );
	} );

	$app->get( '/js-dom-mod', function( $request, $response ) {
		return $response->write( render( 'js-dom-mod.php' ) );
	} );

	$app->get( '/status-200', function( $request, $response ) {
		return $response->write( render( 'status-code.php', [ 'status' => 200 ] ) );
	} );
}

register_routes( $app );

$app->run();
