<?php
declare( strict_types = 1 );

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

	public function testScopedCallback(): void {
		$called = false;
		$sc = new ScopedCallback( static function () use ( &$called ): void {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		unset( $sc );
		$this->assertTrue( $called, 'Callback was called' );
	}

	public function testParams(): void {
		$params = [ 'foo', 'bar', 'baz' ];
		$sc = new ScopedCallback( function ( ...$args ): void {
			$this->assertSame( [ 'foo', 'bar', 'baz' ], $args );
		}, $params );
		ScopedCallback::consume( $sc );
	}

	public function testCancel(): void {
		$called = false;
		$sc = new ScopedCallback( static function () use ( &$called ): void {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		ScopedCallback::cancel( $sc );
		unset( $sc );
		$this->assertFalse( $called, 'Callback was not called' );
	}

	public function testInvalidConstructor(): void {
		$this->expectException( \TypeError::class );
		new ScopedCallback( 'not a valid callback' );
	}

	public function testSerialize(): void {
		$this->expectException( \UnexpectedValueException::class );
		serialize( new ScopedCallback( 'shell_exec', [ 'echo hi' ] ) );
	}

	public function testUnserialize(): void {
		// phpcs:ignore Generic.Files.LineLength.TooLong
		$serialized = 'O:24:"Wikimedia\\ScopedCallback":2:{s:11:"' . "\0" . '*' . "\0" . 'callback";s:10:"shell_exec";s:9:"' . "\0" . '*' . "\0" . 'params";a:1:{i:0;s:7:"echo hi";}}';
		$this->expectException( \UnexpectedValueException::class );
		unserialize( $serialized );
	}

}
