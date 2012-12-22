<?php

namespace BitTorrent\Announcer\Client;

class BitTornado extends Abstracts\TorrentClientAbstract implements Abstracts\TorrentClientInterface {

	protected $version = '0.3.18';

	function generateKey() {
		return $this->getPeerTokens(6);
	}

	protected function getKeyTokens() {
		return 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	}

	function generateId() {
		return 'T03I-----' . $this->getPeerTokens(11);
	}

	function getUserAgent() {
		return 'BitTornado/T-0.3.18';
	}

	function getExtraHeader() {
		return array(
			'Accept-Encoding' => 'gzip',
		);
	}

	function supportsVersion($version) {
		return in_array($version, array('0.3.18'));
	}

}