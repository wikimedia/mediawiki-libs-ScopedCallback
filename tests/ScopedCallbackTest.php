<?php
/**
 * Copyright (C) 2016 Aaron Schulz <aschulz@wikimedia.org>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @author Aaron Schulz <aschulz@wikimedia.org>
 */

namespace Wikimedia\ScopedCallback\Test;

use Wikimedia\ScopedCallback;

/**
 * @covers ScopedCallback
 */
class ScopedCallbackTest extends \PHPUnit_Framework_TestCase {

	public function testScopedCallback() {
		$called = false;
		$sc = new ScopedCallback( function () use ( &$called ) {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		unset( $sc );
		$this->assertTrue( $called, 'Callback was called' );
	}

	public function testParams() {
		$params = [ 'foo', 'bar', 'baz' ];
		$sc = new ScopedCallback( function( /*...*/ ) {
			$this->assertSame( [ 'foo', 'bar', 'baz' ], func_get_args() );
		}, $params );
		ScopedCallback::consume( $sc );
	}

	public function testCancel() {
		$called = false;
		$sc = new ScopedCallback( function () use ( &$called ) {
			$called = true;
		} );

		$this->assertFalse( $called, 'Callback has not run yet' );
		ScopedCallback::cancel( $sc );
		unset( $sc );
		$this->assertFalse( $called, 'Callback was not called' );
	}

	public function testInvalidConstructor() {
		$this->setExpectedException( \InvalidArgumentException::class );
		new ScopedCallback( 'not a valid callback' );
	}

}
