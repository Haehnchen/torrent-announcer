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


	/**
	 * @covers BitTorrent\Announcer\RequestParameter::createFromArray
	 */
	public function testRequestFromRequest() {

		$get_request = 'info_hash=%80%17%d2%b9%2b%08Ob%ac%0c%a1%9a%40%b4%18%2a%91%07%caB&peer_id=-UT1600-%da%81%14Q%9d%20xo%c0%caLf&port=47143&uploaded=222&downloaded=333&left=11111&key=179C5900&numwant=200&compact=1&no_peer_id=1';
		parse_str($get_request, $global);

		$parameter = RequestParameter::createFromArray($global);

		$this->assertEquals(333, $parameter->getDownloaded());
		$this->assertEquals(222, $parameter->getUploaded());
		$this->assertEquals(11111, $parameter->getLeft());
		$this->assertEquals('8017d2b92b084f62ac0ca19a40b4182a9107ca42', $parameter->getInfoHash());
		$this->assertEquals('update', $parameter->getEvent());

	}

	/**
	 * @covers BitTorrent\Announcer\RequestParameter::setParameters
	 */
	public function testInfoHashConverter() {

		$hash = '8017d2b92b084f62ac0ca19a40b4182a9107ca42';

		$parameter = new RequestParameter();
		$this->assertEquals($hash, $parameter->setParameters(array('info_hash' => $hash))->getInfoHash());
		$this->assertEquals($hash, $parameter->setParameters(array('info_hash' => pack('H*', $hash)))->getInfoHash());

	}

}