<?php

namespace BitTorrent\Announcer\Client;

class TransmissionClient extends Abstracts\TorrentClientAbstract implements Abstracts\TorrentClientInterface {

	protected $version = '0.6';

	function getKeyTokens() {
		return 'abcdefghijklmnopqrstuvwxyz0123456789';
	}

	function generateKey() {
		return $this->getPeerTokens(20);
	}

	function generateId() {
		return '-TR0006-' . $this->getPeerTokens(12);
	}

	function getUserAgent() {
		return 'Transmission/0.6';
	}

	function getExtraHeader() {
		return array(
			'Content-length' => '0',
			'Connection' => 'close',
		);
	}

	function supportsVersion($version) {
		return in_array($version, array('0.6'));
	}

}