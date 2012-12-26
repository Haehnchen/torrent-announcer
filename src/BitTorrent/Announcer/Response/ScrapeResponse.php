<?php
namespace BitTorrent\Announcer\Response;

class ScrapeResponse extends Abstracts\ResponseAbstract {

	function getFirstFile() {
		if(isset($this->response['files']) AND count($this->response['files']) > 0) {
			return current($this->response['files']);
		}

		return false;

	}

	function getComplete() {
		return $this->getFileOption('complete');
	}

	function getDownloaded() {
		return $this->getFileOption('downloaded');
	}

	function getIncomplete() {
		return $this->getFileOption('incomplete');
	}

	function getName() {
		return $this->getFileOption('name');
	}

	private function getFileOption($option) {
		if (!$file = $this->getFirstFile() AND !isset($file[$option])) {
			return null;
		}

		return $file[$option];
	}

}

