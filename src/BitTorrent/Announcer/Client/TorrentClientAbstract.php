<?php

namespace BitTorrent\Announcer\Client;

abstract class TorrentClientAbstract implements TorrentClientInterface {

	protected $peer_id;
	protected $peer_key;
	protected $peer_port = 3366 ;

	protected $version;

	function __construct() {
		$this->peer_id = $this->generateId();
		$this->peer_key = $this->generateKey();
	}

	function getKeyTokens() {
		return 'abcdef0123456789';
	}

	protected function getPeerTokens($length) {
		$tokens = '';

		mt_srand((double)microtime() * 1000000);

		for ($i = 1; $i <= $length; $i++) {
			$tokens .= substr($this->getKeyTokens(), mt_rand(0, strlen($this->getKeyTokens()) - 1), 1);
		}

		return $tokens;
	}

	function setVersion($version) {

		if(!$this->isSupported($version)) {
			throw new \RuntimeException('Unknown version string: ' . $version);
		}

		$this->__construct();

		return $this;
	}

	function isSupported($version) {
		return in_array($version, $this->supportedVersions());
	}

	function getVersion() {
		$this->version;
	}

	public function setPeerId($peer_id) {
		$this->peer_id = $peer_id;
	}

	public function getPeerId() {
		return $this->peer_id;
	}

	public function setPeerKey($peer_key) {
		$this->peer_key = $peer_key;
	}

	public function getPeerKey() {
		return $this->peer_key;
	}

	public function setPeerPort($peer_port) {
		$this->peer_port = $peer_port;
	}

	public function getPeerPort() {
		return $this->peer_port;
	}

}