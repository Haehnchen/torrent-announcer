<?php

namespace BitTorrent\Announcer;

use PHP\BitTorrent\Torrent;
use BitTorrent\Announcer\Client\TorrentClientInterface;

use Buzz\Browser;


class Request {

	/** @var \PHP\BitTorrent\Torrent */
	private $torrent_file;

	/** @var  \BitTorrent\Announcer\Client\TorrentClientInterface */
	private $torrent_client;

	/** @var RequestParameter */
	private $parameter;

	private $announce_url;

	function __construct() {
		$this->setParameter(new RequestParameter());
	}

	private function generateParameter() {

		$parameter = $this->Parameter()->toArray();

		$parameter['info_hash'] = pack("H*", $this->Parameter()->getInfoHash());

		$parameter['peer_id'] = $this->torrent_client->getPeerId();
		$parameter['port'] = $this->torrent_client->getPeerPort();

		if ($peer_id = $this->torrent_client->getPeerKey()) {
			$parameter['key'] = $peer_id;
		}

		return http_build_query($parameter);
	}

	function generateUrl() {
		$announce_url = $this->getAnnounceUrl();
		$query_parameter = $this->generateParameter();

		$url_parsed = parse_url($announce_url);

		$query_parameter = (!isset($url_parsed["query"]) ? '?' : '&') . $query_parameter;

		return $announce_url . $query_parameter;

	}

	/**
	 * @return Response
	 */
	function announce() {

		$headers = array();

		$headers['User-Agent'] = $this->torrent_client->getUserAgent();
		$headers = array_merge($headers, (array) $this->torrent_client->getExtraHeader());

		$browser = new Browser();
		$browser->setClient(new \Buzz\Client\Curl());

		/** @var $response \Buzz\Message\Response */
		$response = $browser->get($this->generateUrl(), $headers);
		$this->decompressContent($response);

		return new Response($response->getContent());
	}

	private function decompressContent(\Buzz\Message\Response $response) {

		if (!$content_encoding = $response->getHeader('Content-Encoding')) {
			return;
		}

		$content = $response->getContent();

		if (strpos($content_encoding, 'deflate') !== false) {
			$content = gzuncompress($content);
		}

		if (strpos($content_encoding, 'gzip') !== false) {
			$content = gzinflate(substr($content, 10));
		}

		$response->setContent($content);

	}

	public function setAnnounceUrl($announce_url) {
		$this->announce_url = $announce_url;
	}

	public function getAnnounceUrl() {
		return $this->announce_url;
	}

	function validateRequest() {

		if(!$this->getAnnounceUrl()) {
			throw new \RuntimeException('no announce url found');
		}

		if (!$this->Parameter()->getInfoHash()) {
			throw new \RuntimeException('no info_hash found');
		}

		if (!$this->torrent_client->getPeerId()) {
			throw new \RuntimeException('no peer_id found');
		}

		if ($this->Parameter()->getEvent() == RequestParameter::EVENT_START) {

			if ($this->Parameter()->getDownloaded() > 0 OR $this->Parameter()->getUploaded() > 0) {
				throw new \RuntimeException('started event cant have downloaded or uploaded != 0');
			}

		}

		if ($this->getTorrentFile()) {

			if ($this->Parameter()->getDownloaded() > $this->getTorrentFile()->getSize()) {
				throw new \RuntimeException('downloaded cant be greater the torrent size');
			}

		}

	}

	function setTorrentFile(Torrent $torrent) {
		$this->torrent_file = $torrent;
		$this->setAnnounceUrl(static::findTorrentFileAnnounceUrl($torrent));
		$this->Parameter()->setInfoHash($torrent->getHash());
		return $this;
	}

	function getTorrentFile() {
		return $this->torrent_file;
	}

	function setTorrentClient(TorrentClientInterface $client) {
		$this->torrent_client = $client;
		return $this;
	}


	static function findTorrentFileAnnounceUrl(Torrent $torrent) {

		if($url = $torrent->getAnnounce()) {
			return $url;
		}

		if (count($urls = $torrent->getAnnounceList()) > 0) {
			return $urls[0];
		}

		throw new \RuntimeException('Cant find announce url');

	}

	static function createOnTorrent(Torrent $torrent, $client = null) {
		$self = new static();

		if($client !== null) {
			$self->setTorrentClient($client);
		}

		return $self->setTorrentFile($torrent);
	}

	function setParameter(RequestParameter $parameter) {
		$this->parameter = $parameter;
	}

	function Parameter() {
		return $this->parameter;
	}

}

