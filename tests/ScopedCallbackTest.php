<?php
/**
 * Copyright (C) 2016 Aaron Schulz <aschulz@wikimedia.org>
 *
 * @license GPL-2.0-or-later
 * @file
 * @author Aaron Schulz <aschulz@wikimedia.org>
 */

namespace Wikimedia\ScopedCallback\Test;

use Wikimedia\ScopedCallback;

/**
 * @covers \Wikimedia\ScopedCallback
 */
class ScopedCallbackTest extends \PHPUnit\Framework\TestCase {

	public function testScopedCallback() {
		$called = false;
		$sc = new ScopedCallback( static function () use ( &$called ) {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		unset( $sc );
		$this->assertTrue( $called, 'Callback was called' );
	}

	public function testParams() {
		$params = [ 'foo', 'bar', 'baz' ];
		$sc = new ScopedCallback( function ( ...$args ) {
			$this->assertSame( [ 'foo', 'bar', 'baz' ], $args );
		}, $params );
		ScopedCallback::consume( $sc );
	}

	public function testCancel() {
		$called = false;
		$sc = new ScopedCallback( static function () use ( &$called ) {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		ScopedCallback::cancel( $sc );
		unset( $sc );
		$this->assertFalse( $called, 'Callback was not called' );
	}

	public function testInvalidConstructor() {
		$this->expectException( \InvalidArgumentException::class );
		new ScopedCallback( 'not a valid callback' );
	}

	public function testSerialize() {
		$this->expectException( \UnexpectedValueException::class );
		serialize( new ScopedCallback( 'shell_exec', [ 'echo hi' ] ) );
	}

	public function testUnserialize() {
		// phpcs:ignore Generic.Files.LineLength.TooLong
		$serialized = 'O:24:"Wikimedia\\ScopedCallback":2:{s:11:"' . "\0" . '*' . "\0" . 'callback";s:10:"shell_exec";s:9:"' . "\0" . '*' . "\0" . 'params";a:1:{i:0;s:7:"echo hi";}}';
		$this->expectException( \UnexpectedValueException::class );
		unserialize( $serialized );
	}

}
