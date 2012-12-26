<?php

namespace BitTorrent\Announcer\Response\Abstracts;

use PHP\BitTorrent\Decoder;
use PHP\BitTorrent\Encoder;

abstract class ResponseAbstract implements ResponseInterface {

	protected $response = array();

	function __construct($response_string = null) {
		if ($response_string !== null) {
			$this->setResponse($response_string);
		}
	}

	function setResponse($string) {
		$decoder = new Decoder();
		$this->response = $decoder->decode($string);
	}

	function getResponse() {

		if ($this->response === null) {
			throw new \RuntimeException('You need to set a request first.');
		}

		return $this->response;
	}

	function isFailure() {
		return (bool)$this->get('failure reason', false);
	}

	function getFailure() {
		return $this->isFailure() ? $this->get('failure reason') : null;
	}

	function getComplete() {
		return $this->get('complete');
	}

	function getIncomplete() {
		return $this->get('incomplete');
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

	protected function renderer($string) {
		$encoder = new Encoder();
		return $encoder->encode($string);
	}

	function render() {
		return $this->renderer($this->response);
	}

	static function create() {
		return new static();
	}

}