<?php

namespace BitTorrent\Announcer\Client;

class PlainTorrentClient extends TorrentClientAbstract implements TorrentClientInterface {

	protected $version = '1.6';

	protected $user_agent = '';
	protected $extra_header = array();

	function __construct() {
	}

	function generateKey() {
		throw new \RuntimeException('invalid generateKey call');
	}

	function generateId() {
		throw new \RuntimeException('invalid generateId call');
	}

	function getUserAgent() {

		if(!$this->user_agent) {
			throw new \RuntimeException('invalid UserAgent');
		}

		return $this->user_agent;

	}

	function getExtraHeader() {
		return $this->extra_header;
	}

	public function setExtraHeader($extra_header) {
		$this->extra_header = $extra_header;
	}

	function supportedVersions() {
		throw new \RuntimeException('invalid supportedVersions call');
	}

	function setUserAgent($user_agent) {
		$this->user_agent = $user_agent;
		return $this;
	}

	static function createFromGlobals() {
		$self = new static();

		if(isset($_SERVER['HTTP_USER_AGENT'])) {
			$self->setUserAgent($_SERVER['HTTP_USER_AGENT']);
		}

		if(function_exists('getallheaders')) {
			$headers = getallheaders();
			unset($headers['Host']);
			unset($headers['User-Agent']);
			$self->setExtraHeader($headers);
		}

		if (isset($_GET['peer_id'])) {
			$self->setPeerId($_GET['peer_id']);
		}

		if (isset($_GET['port'])) {
			$self->setPeerPort($_GET['port']);
		}

		if (isset($_GET['key'])) {
			$self->setPeerKey($_GET['key']);
		}

		if (isset($_GET['numwant'])) {
			$self->setNumwant($_GET['numwant']);
		}

		if (isset($_GET['compact'])) {
			$self->setCompact($_GET['compact']);
		}

		if (isset($_GET['no_peer_id'])) {
			$self->setNoPeerId($_GET['no_peer_id']);
		}

		return $self;

	}

}