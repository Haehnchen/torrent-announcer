<?php

namespace BitTorrent\Announcer\Client;

class uTorrentClient extends Abstracts\TorrentClientAbstract implements Abstracts\TorrentClientInterface {

	protected $version = '1.6';

	function generateKey() {
		return $this->getPeerTokens(8);
	}

	function generateId() {
		return '-UT1600-' . pack('H*', $this->getPeerTokens(24));
	}

	function getUserAgent() {
		return 'uTorrent/1600';
	}

	function getExtraHeader() {
		return array(
			'Accept-Encoding' => 'gzip',
		);
	}

	function supportsVersion($version) {
		return in_array($version, array('1.6'));
	}

}