<?php

namespace BitTorrent\Announcer;

use BitTorrent\Announcer\Request;
use PHP\BitTorrent\Torrent;

use BitTorrent\Announcer\Client\PlainTorrentClient;

class RequestTest extends \PHPUnit_Framework_TestCase {

	/** @var Request */
	private $request;

	public function setUp() {
		$this->request = new Request();
	}

	/**
	 * @expectedException RuntimeException
	 * @covers BitTorrent\Announcer\Request::setAnnounceUrl
	 */
	public function testInvalidAnnounceUrl() {
		$this->request->setAnnounceUrl('')->announce();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage started event cant have downloaded or uploaded != 0
	 * @covers BitTorrent\Announcer\Request::validateRequest
	 */
	public function testInvalidUploadedOnStart() {
		$this->setTestsArgs()->parameter->setEvent(RequestParameter::EVENT_START)->setUploaded(1024);
		$this->request->validateRequest();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage started event cant have downloaded or uploaded != 0
	 * @covers BitTorrent\Announcer\Request::validateRequest
	 */
	public function testInvalidDownloadedOnStart() {
		$this->setTestsArgs()->parameter->setEvent(RequestParameter::EVENT_START)->setDownloaded(1024);
		$this->request->validateRequest();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage uploaded cant be greater the torrent size
	 * @covers BitTorrent\Announcer\Request::validateRequest
	 */
	public function testUploadedBiggerThanSize() {
		$this->setTestsArgs()->parameter->setEvent(RequestParameter::EVENT_UPDATE)->setUploaded(1024);
		$this->request->validateRequest();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage downloaded cant be greater the torrent size
	 * @covers BitTorrent\Announcer\Request::validateRequest
	 */
	public function testDownloadedBiggerThanSize() {
		$this->setTestsArgs()->parameter->setEvent(RequestParameter::EVENT_UPDATE)->setDownloaded(1024);
		$this->request->validateRequest();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage no peer_id found
	 * @covers BitTorrent\Announcer\Request::validateRequest
	 */
	public function testNonPeerId() {
		$request = $this->createRequest();
		$request->getTorrentClient()->setPeerId(null);
		$request->validateRequest();
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage Cant find announce url
	 * @covers BitTorrent\Announcer\Request::findTorrentFileAnnounceUrl
	 */
	public function testNoAnnounceUrlFound() {
		$torrent = Torrent::createFromTorrentFile(__DIR__ . '/Fixtures/extra.torrent');
		$torrent->setAnnounce(null)->setAnnounceList(null);
		Request::findTorrentFileAnnounceUrl($torrent);
	}

	/**
	 * @covers BitTorrent\Announcer\Request::findTorrentFileAnnounceUrl
	 */
	public function testAnnounceFinderSingleAndList() {
		$torrent = Torrent::createFromTorrentFile(__DIR__ . '/Fixtures/extra.torrent');
		$this->assertEquals('http://www.google.de', Request::findTorrentFileAnnounceUrl($torrent->setAnnounce('http://www.google.de')->setAnnounceList(null)));
		$this->assertEquals('http://www.google.de', Request::findTorrentFileAnnounceUrl($torrent->setAnnounce(null)->setAnnounceList(array('http://www.google.de'))));
	}

	/**
	 * @covers BitTorrent\Announcer\Request::testCreateRequestFromArray
	 */
	public function testCreateRequestFromArray() {

		$global = array(
			'info_hash' => sha1('Hello World', true),
			'port' => 1337,
		);

		$request = Request::createFromRequestArray(PlainTorrentClient::createFromGlobals($global), $global);

		$this->assertEquals(sha1('Hello World'), $request->parameter->getInfoHash());
		$this->assertEquals($global['port'], $request->getTorrentClient()->getPeerPort());
	}

	/**
	 * @return Request
	 */
	private function createRequest() {
		$request = new Request();
		$request->setTorrentClient(new \BitTorrent\Announcer\Client\uTorrentClient());
		$request->setTorrentFile(Torrent::createFromTorrentFile(__DIR__ . '/Fixtures/extra.torrent'));
		return $request;

	}

	/**
	 * @return Request
	 */
	private function setTestsArgs() {
		$this->request->setTorrentClient(new \BitTorrent\Announcer\Client\uTorrentClient());
		$this->request->setTorrentFile(Torrent::createFromTorrentFile(__DIR__ . '/Fixtures/extra.torrent'))->validateRequest();
		return $this->request;
	}

}