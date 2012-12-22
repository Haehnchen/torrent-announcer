<?php

namespace BitTorrent\Announcer\Client\Abstracts;

interface TorrentClientInterface {

	function getUserAgent();
	function getExtraHeader();
	function generateKey();
	function generateId();

	function supportsVersion($version);

	function setVersion($version);
	function getVersion();
	function setPeerId($peer_id);
	function getPeerId();
	function setPeerKey($peer_key);
	function getPeerKey();
	function setPeerPort($peer_port);
	function getPeerPort();

	function setCompact($compact);
	function getCompact();
	function setNoPeerId($no_peer_id);
	function getNoPeerId();
	function setNumwant($numwant);
	function getNumwant();

}