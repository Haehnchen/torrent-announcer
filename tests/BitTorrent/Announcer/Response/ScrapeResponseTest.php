<?php

namespace BitTorrent\Announcer\Response;

use BitTorrent\Announcer\Response\ScrapeResponse;

class ScrapeResponseTest extends \PHPUnit_Framework_TestCase {

	/** @var ScrapeResponse */
	private $response;

	public function setUp() {
		$this->response = new ScrapeResponse();
	}

	/**
	 * @covers BitTorrent\Announcer\Response\ScrapeResponse::getComplete
	 * @covers BitTorrent\Announcer\Response\ScrapeResponse::getDownloaded
	 * @covers BitTorrent\Announcer\Response\ScrapeResponse::getIncomplete
	 * @covers BitTorrent\Announcer\Response\ScrapeResponse::getName
	 * @covers BitTorrent\Announcer\Response\ScrapeResponse::render
	 */
	public function testResponse() {
		$this->response->setResponse(file_get_contents(__DIR__ . '/../Fixtures/response_scrape.bencode'));
		$this->assertEquals(683, $this->response->getComplete());
		$this->assertEquals(3, $this->response->getDownloaded());
		$this->assertEquals(11, $this->response->getIncomplete());
		$this->assertEquals('ubuntu-12.10-server-i386.iso', $this->response->getName());

		$this->assertContains('ubuntu-12.10-server-i386.iso', $this->response->render());
	}

}