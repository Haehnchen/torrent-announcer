<?php

namespace BitTorrent\Announcer\Response;

class AnnounceResponse extends Abstracts\ResponseAbstract {

	protected $compact = true;

	function __construct($response_string = null) {
		if($response_string !== null) {
			$this->setResponse($response_string);
		}
	}

	function setResponse($string) {
		parent::setResponse($string);
		$this->setPeers($this->get('peers', array()));
	}

	function getInterval() {
		return $this->get('interval');
	}

	function setPeers($peers) {

		$back = array();

		if (is_array($peers)) {
			$this->setCompactMode(false);
			$this->set('peers', $peers);
			return $this;
		}

		// we have compact mode here
		$this->setCompactMode(true);
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

	public function setCompactMode($is_compact) {
		$this->compact = $is_compact;
		return $this;
	}

	public function isCompactMode() {
		return $this->compact;
	}

	function render() {

		$response = $this->response;

		if($this->isCompactMode() == true and $this->getPeersCount() > 0) {
			$response['peers'] = '';
			foreach($this->getPeers() as $peer) {
				$response['peers'] .= pack('Nn', ip2long($peer['ip']), $peer['port']);
			}
		}

		return $this->renderer($response);
	}

}