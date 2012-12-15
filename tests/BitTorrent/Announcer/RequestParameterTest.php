<?php

namespace BitTorrent\Announcer;

use BitTorrent\Announcer\RequestParameter;

class RequestParameterTest extends \PHPUnit_Framework_TestCase {

	/** @var RequestParameter */
	private $parameter;

	public function setUp() {
		$this->parameter = new RequestParameter();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage invalid info_hash given
	 * @covers BitTorrent\Announcer\RequestParameter::setInfoHash
	 */
	public function testInvalidInfoHashLength() {
		$this->parameter->setInfoHash(md5(uniqid()));
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage invalid info_hash given
	 * @covers BitTorrent\Announcer\RequestParameter::setInfoHash
	 */
	public function testInfoHashNonHex() {
		$this->parameter->setInfoHash(str_pad(null, 40, 'g'));
	}

	/**
	 * @covers BitTorrent\Announcer\RequestParameter::setInfoHash
	 */
	public function testInfoHash() {
		$this->parameter->setInfoHash($hash = sha1(uniqid()));
		$this->assertEquals($hash, $this->parameter->getInfoHash());
	}

}