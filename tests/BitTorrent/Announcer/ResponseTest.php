<?php

namespace BitTorrent\Announcer;

use BitTorrent\Announcer\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase {

	/** @var Response */
	private $response;

	public function setUp() {
		$this->response = new Response();
	}

	/**
	 * @covers BitTorrent\Announcer\Response::getIncomplete
	 * @covers BitTorrent\Announcer\Response::getComplete
	 * @covers BitTorrent\Announcer\Response::getInterval
	 * @covers BitTorrent\Announcer\Response::getPeersCount
	 * @covers BitTorrent\Announcer\Response::getPeers
	 */
	public function testResponse() {
		$this->response->setResponse(file_get_contents(__DIR__ . '/Fixtures/response_compact.bencode'));
		$this->assertEquals(9, $this->response->getIncomplete());
		$this->assertEquals(639, $this->response->getComplete());
		$this->assertEquals(1800, $this->response->getInterval());
		$this->assertEquals(50, $this->response->getPeersCount());
		$this->assertContains('66.246.76.139', current($this->response->getPeers()));
	}

	/**
	 * @covers BitTorrent\Announcer\Response::addPeer
	 * @covers BitTorrent\Announcer\Response::setPeers
	 * @covers BitTorrent\Announcer\Response::getPeers
	 */
	public function testPeersResponse() {
		$response = Response::create()->addPeer('127.0.0.1', 222);

		$this->assertContains('127.0.0.1', current($response->getPeers()));

		$response->setPeers(array('host' => '127.0.0.1', 'port' => 222));
		$this->assertContains('127.0.0.1', current($response->getPeers()));

	}

	/**
	 * @covers BitTorrent\Announcer\Response::addPeer
	 * @covers BitTorrent\Announcer\Response::render
	 * @covers BitTorrent\Announcer\Response::setCompactMode
	 */
	public function testCompactPeersResponse() {
		$response = Response::create()->addPeer('127.0.0.1', 222)->setCompactMode(true);
		$this->assertNotContains('127.0.0.1', $response->render());

	}

	/**
	 * @covers BitTorrent\Announcer\Response::setCompactMode
	 * @covers BitTorrent\Announcer\Response::render
	 */
	public function testisCompactPeersResponse() {
		$response = Response::create()->addPeer('127.0.0.1', 222)->setCompactMode(false);
		$this->assertContains('127.0.0.1', $response->render());

	}


	/**
	 * @covers BitTorrent\Announcer\Response::getComplete
	 * @covers BitTorrent\Announcer\Response::getInterval
	 * @covers BitTorrent\Announcer\Response::getPeersCount
	 */
	public function testFailureResponse() {
		#d8:completei639e10:incompletei9e8:intervali1800e5:peers300:
		$this->response->setResponse(file_get_contents(__DIR__ . '/Fixtures/reponse_failure.bencode'));
		$this->assertTrue($this->response->isFailure());
		$this->assertEquals('Requested download is not authorized for use with this tracker.', $this->response->getFailure());
	}

}