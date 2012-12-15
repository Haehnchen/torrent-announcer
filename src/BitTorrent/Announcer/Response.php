<?php

namespace BitTorrent\Announcer;

use PHP\BitTorrent\Decoder;
use PHP\BitTorrent\Encoder;

class Response {

	private $response = array();
	private $compact = true;

	function __construct($response_string = null) {
		if($response_string !== null) {
			$this->setResponse($response_string);
		}
	}

	function setResponse($string) {
		$decoder = new Decoder();
		$this->response = $decoder->decode($string);
		$this->setPeers($this->get('peers', array()));
	}

	function getResponse() {

		if($this->response === null) {
			throw new \RuntimeException('You need to set a request first.');
		}

		return $this->response;
	}

	function isFailure() {
		return (bool) $this->get('failure reason', false);
	}

	function getFailure() {
		return $this->isFailure() ? $this->get('failure reason') : null;
	}

	function setPeers($peers) {

		$back = array();

		if (is_array($peers)) {
			$this->set('peers', $peers);
			return $this;
		}

		// we have compact mode here
		$peers = str_split($peers, 6);
		foreach ($peers as $row) {

			$peer = unpack('Nip/nport', $row);
			$peer['ip'] = long2ip($peer['ip']);

			$back[] = $peer;
		}

		$this->set('peers', $back);

		return $this;
	}

	function addPeer($ip, $port, $peer_id = null) {

		$peer = array_filter(array(
			'ip' => $ip,
			'port' => $port,
			'peer_id' => $peer_id,
		));

		// empty one
		if(!isset($this->response['peers'])) {
			$this->response['peers'] = array();
		}

		$this->response['peers'][] = $peer;

		return $this;
	}

	function getPeers() {
		return $this->get('peers', array());
	}

	function getPeersCount() {
		return count($this->getPeers());
	}

	function getComplete() {
		return $this->get('complete');
	}

	function getIncomplete() {
		return $this->get('incomplete');
	}

	function getInterval() {
		return $this->get('interval');
	}


	public function setCompactMode($is_compact) {
		$this->compact = $is_compact;
		return $this;
	}

	public function isCompactMode() {
		return $this->compact;
	}

	/**
	 * @param $key
	 * @param mixed $default
	 * @return mixed
	 */
	function get($key, $default = null) {
		return isset($this->response[$key]) ? $this->response[$key] : $default;
	}

	function set($key, $value) {
		$this->response[$key] = $value;
		return $this;
	}

	function render() {
		$encoder = new Encoder();

		$response = $this->response;

		if($this->isCompactMode() == true and $this->getPeersCount() > 0) {
			$response['peers'] = '';
			foreach($this->getPeers() as $peer) {
				$response['peers'] .= pack('Nn', ip2long($peer['ip']), $peer['port']);
			}
		}

		return $encoder->encode($response);
	}

	static function create() {
		return new static();
	}

}