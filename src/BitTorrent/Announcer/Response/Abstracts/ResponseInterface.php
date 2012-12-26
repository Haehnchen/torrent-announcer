<?php

namespace BitTorrent\Announcer\Response\Abstracts;

interface ResponseInterface {
	function setResponse($string);
	function render();
}