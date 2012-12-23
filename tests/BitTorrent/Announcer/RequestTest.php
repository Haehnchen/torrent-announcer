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
	 * @covers BitTorrent\Announcer\Request::createFromRequestArray
	 */
	public function testCreateRequestFromArray() {

		$get_request = 'info_hash=%80%17%d2%b9%2b%08Ob%ac%0c%a1%9a%40%b4%18%2a%91%07%caB&peer_id=-UT1600-%da%81%14Q%9d%20xo%c0%caLf&port=47143&uploaded=222&downloaded=333&left=11111&key=179C5900&numwant=200&compact=1&no_peer_id=1&event=start';
		parse_str($get_request, $global);

		$request = Request::createFromRequestArray(PlainTorrentClient::createFromGlobals($global), $global);

		$this->assertEquals('8017d2b92b084f62ac0ca19a40b4182a9107ca42', $request->parameter->getInfoHash());
		$this->assertEquals(47143, $request->getTorrentClient()->getPeerPort());
	}

	/**
	 * @covers BitTorrent\Announcer\Request::createFromRequestArray
	 */
	public function testParameterRequestFromRequest() {

		$get_request = 'info_hash=%80%17%d2%b9%2b%08Ob%ac%0c%a1%9a%40%b4%18%2a%91%07%caB&peer_id=-UT1600-%da%81%14Q%9d%20xo%c0%caLf&port=47143&uploaded=222&downloaded=333&left=11111&key=179C5900&numwant=200&compact=1&no_peer_id=1&event=start';
		parse_str($get_request, $global);

		$request = Request::createFromRequestArray(PlainTorrentClient::createFromGlobals($global), $global);
		$this->assertEquals(333, $request->parameter->getDownloaded());
		$this->assertEquals(222, $request->parameter->getUploaded());
		$this->assertEquals(11111, $request->parameter->getLeft());
		$this->assertEquals('8017d2b92b084f62ac0ca19a40b4182a9107ca42', $request->parameter->getInfoHash());
		$this->assertEquals('start', $request->parameter->getEvent());

	}

	/**
	 * @covers BitTorrent\Announcer\Request::getUrl
	 */
	public function testUrlGenerator() {

		$request = $this->createRequest();
		$this->assertContains(urlencode(pack('H*', 'B75B955E703DC3EC9696018C53BE9CB940F27856')), $request->getUrl());

		$request->parameter->setLeft('1234567');
		$this->assertContains('1234567', $request->getUrl());

		$this->assertContains('peer_id=-UT', $request->getUrl());

	}

	/**
	 * @covers BitTorrent\Announcer\Request::getUrl
	 */
	public function testUrlGeneratorParameter() {

		$request = $this->createRequest();

		$request->setAnnounceUrl('http://example.com');
		$this->assertStringStartsWith('http://example.com?info_hash', $request->getUrl());
		$this->assertNotContains('?&', $request->getUrl());
		$this->assertStringStartsNotWith('http://example.com&', $request->getUrl());

		$request->setAnnounceUrl('http://example.com?test=p');
		$this->assertStringStartsWith('http://example.com?test=p&', $request->getUrl());
		$this->assertNotContains('?&', $request->getUrl());
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
