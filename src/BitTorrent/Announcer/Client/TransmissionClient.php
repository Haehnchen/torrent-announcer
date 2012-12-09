<?php

namespace BitTorrent\Announcer;

class TransmissionClient extends TorrentClientAbstract implements TorrentClientInterface {

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

	function supportedVersions() {
		return array('0.6');
	}

}