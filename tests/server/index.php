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
		return $response->write( 'home' );
	} );

	$app->get( '/js/{js:enabled|disabled}', function( $request, $response, $args ) {
		return $response->write( render( 'javascript.php', [
			'status' => $args['js'],
		] ) );
	} );

	$status_list = ( new \ReflectionClass( \Slim\Http\StatusCode::class ) )->getConstants();

	foreach ( $status_list as $message => $code ) {
		$app->get(
			"/status-code/{$code}",
			function( $request, $response ) use ( $code, $message ) {
				return $response->write(
					render( 'status-code.php', compact( 'code', 'message' ) )
				)->withStatus( $code );
			}
		);
	}
}

register_routes( $app );

$app->run();
