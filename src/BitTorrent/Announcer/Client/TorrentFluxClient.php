<?php

namespace BitTorrent\Announcer\Client;

class TorrentFluxClient extends TorrentClientAbstract implements TorrentClientInterface {

	function getPeerKeyTokens() {
		return 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	}

	function getPeerId() {
		#return '-TR0006-' . $this->getPeerTokens(12);
	}

	function getKeyTokens() {
		// TODO: Implement getKeyTokens() method.
	}

	function getHeader() {
		// TODO: Implement getHeader() method.
	}

	function generateKey() {
		// TODO: Implement generateKey() method.
	}

	function generateId() {
		// TODO: Implement generateId() method.
	}

	function supportedVersions() {
		// TODO: Implement supportedVersions() method.
	}

	function getUserAgent() {
		// TODO: Implement getUserAgent() method.
	}

	function getExtraHeader() {
		// TODO: Implement getExtraHeader() method.
	}
}