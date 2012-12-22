<?php

namespace BitTorrent\Announcer\Client;

use BitTorrent\Announcer\Client\TransmissionClient;

class TransmissionClientTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::setVersion
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::getPeerId
	 */
	public function testPeerVersionId() {
		$client = new TransmissionClient();

		$this->assertStringStartsWith('-TR1220-', $client->setVersion('1.22')->getPeerId());
		$this->assertStringStartsWith('-TR0006-', $client->setVersion('0.6')->getPeerId());
		$this->assertStringStartsWith('-TR1200-', $client->setVersion('1.2')->getPeerId());
		$this->assertStringStartsWith('-TR1020-', $client->setVersion('1.02')->getPeerId());
	}

	/**
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::setVersion
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::getPeerId
	 */
	public function testPeerUserAgentVersion() {
		$client = new TransmissionClient();

		$this->assertEquals('Transmission/1.22', $client->setVersion('1.22')->getUserAgent());
		$this->assertEquals('Transmission/0.6', $client->setVersion('0.6')->getUserAgent());
		$this->assertEquals('Transmission/1.2', $client->setVersion('1.2')->getUserAgent());
		$this->assertEquals('Transmission/1.02', $client->setVersion('1.02')->getUserAgent());
	}

	/**
	 * @expectedException RuntimeException
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::setVersion
	 */
	public function testUnsupportedVersion() {
		$client = new TransmissionClient();
		$client->setVersion('0.9');
	}

	/**
	 * @covers BitTorrent\Announcer\Client\TransmissionClient::setVersion
	 */
	public function testSupportedVersion() {
		$client = new TransmissionClient();
		$this->assertTrue($client->supportsVersion('2.9'));
		$this->assertTrue($client->supportsVersion('0.6'));
	}

}