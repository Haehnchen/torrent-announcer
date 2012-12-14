<?php

namespace BitTorrent\Announcer;


class RequestParameter {

	const EVENT_START = 'started';
	const EVENT_UPDATE = 'update';
	const EVENT_STOP = 'stopped';
	const EVENT_COMPLETE = 'completed';

	private $parameters = array(
		'uploaded' => 0,
		'downloaded' => 0,
		'left' => 0,
		'info_hash' => null,
		'event' => self::EVENT_START,
	);

	function setInfoHash($value) {

		if (strlen($value) != 40 OR !preg_match("/^[a-f0-9]{1,}$/is", $value)) {
			throw new \RuntimeException('invalid info_hash given');
		}

		return $this->set('info_hash', $value);
	}

	function getInfoHash() {
		return $this->get('info_hash');
	}

	function setEvent($value) {
		return $this->set('event', $value);
	}

	function getEvent() {
		return $this->get('event');
	}

	function setDownloaded($value) {
		return $this->set('downloaded', $value);
	}

	function getDownloaded() {
		return $this->get('downloaded');
	}

	function setLeft($value) {
		return $this->set('left', $value);
	}

	function getLeft() {
		return $this->get('left');
	}

	function setUploaded($value) {
		return $this->set('uploaded', $value);
	}

	function getUploaded() {
		return $this->get('uploaded');
	}

	function getParameters() {
		return $this->parameters;
	}

	function setParameters($parameters) {
		$this->parameters = $parameters;
		return $this;
	}

	function toArray() {

		$parameter = array(
			'info_hash' => $this->getInfoHash(),
			'uploaded' => $this->getUploaded(),
			'downloaded' => $this->getDownloaded(),
			'left' => $this->getLeft(),
		);

		if ($this->getEvent() != self::EVENT_UPDATE) {
			$parameter['event'] = $this->getEvent();
		}

		return $parameter;
	}

	function set($key, $value) {
		$this->parameters[$key] = $value;
		return $this;
	}

	static function createFromArray($array) {
		$self = new static();
		$self->setParameters($array);
		return $self;
	}

	function get($key, $default = null) {
		return array_key_exists($key, $this->parameters) ? $this->parameters[$key] : $default;
	}

}