<?php

namespace BitTorrent\Announcer;

use PHP\BitTorrent\Decoder;
use PHP\BitTorrent\Encoder;

class Response {

	private $response = null;

	function __construct($response_string = null) {
		if($response_string !== null) {
			$this->setResponse($response_string);
		}
	}

	function setResponse($string) {
		$decoder = new Decoder();
		$this->response = $decoder->decode($string);
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

	function getPeers() {

		$back = array();

		if(!$peers = $this->get('peers')) {
			return $back;
		}

		if(is_array($peers)) {
			return $peers;
		}

		// we have compact mode here
		$peers = str_split($peers, 6);
		foreach ($peers as $row) {

			$peer = unpack('Nip/nport', $row);
			$peer['ip'] = long2ip($peer['ip']);

			$back[] = $peer;
		}

		return $back;
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

	function get($key, $default = null) {
		return isset($this->response[$key]) ? $this->response[$key] : $default;
	}

	function render() {
		$encoder = new Encoder();
		return $encoder->encode($this->response);
	}

}