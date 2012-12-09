<?php

namespace BitTorrent\Announcer\Client;

interface TorrentClientInterface {
	function getKeyTokens();

	function getUserAgent();
	function getExtraHeader();
	function generateKey();
	function generateId();

	function supportedVersions();

	function setVersion($version);
	function getVersion();
	function setPeerId($peer_id);
	function getPeerId();
	function setPeerKey($peer_key);
	function getPeerKey();
	function setPeerPort($peer_port);
	function getPeerPort();
}