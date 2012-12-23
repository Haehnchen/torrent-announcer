<?php

namespace BitTorrent\Announcer\Client;

use BitTorrent\Announcer\Client\PlainTorrentClient;

class PlainTorrentClientTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers BitTorrent\Announcer\Client\PlainTorrentClient::createFromGlobals
	 */
	public function testGetParameterClient() {

		$request = 'info_hash=%80%17%d2%b9%2b%08Ob%ac%0c%a1%9a%40%b4%18%2a%91%07%caB&peer_id=-UT1600-%da%81%14Q%9d%20xo%c0%caLf&port=47143&uploaded=0&downloaded=0&left=0&key=179C5900&event=started&numwant=200&compact=1&no_peer_id=1';
		parse_str($request, $global);

		$client = PlainTorrentClient::createFromGlobals($global);
		$this->assertEquals(200, $client->getNumwant());
		$this->assertEquals(47143, $client->getPeerPort());
		$this->assertEquals(1, $client->getNoPeerId());
		$this->assertEquals(1, $client->getCompact());
		$this->assertEquals('179C5900', $client->getPeerKey());
		$this->assertStringStartsWith('-UT1600-', $client->getPeerId());
	}

	/**
	 * @covers BitTorrent\Announcer\Client\PlainTorrentClient::getUserAgent
	 * @covers BitTorrent\Announcer\Client\PlainTorrentClient::createFromGlobals
	 */
	public function testTestUserAgent() {

		$_SERVER['HTTP_USER_AGENT'] = 'PlainPHPClient/1.2';

		$client = PlainTorrentClient::createFromGlobals(array());
		$this->assertEquals('PlainPHPClient/1.2', $client->getUserAgent());
	}

}