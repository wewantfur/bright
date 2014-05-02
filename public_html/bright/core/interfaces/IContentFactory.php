<?php
namespace bright\core\interfaces;

interface IContentFactory {
	/**
	 * Stores content into the database
	 * @param IContent $content
	 */
	public static function setContent($content);
}