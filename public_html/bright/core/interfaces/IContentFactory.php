<?php
namespace bright\core\interfaces;

interface IContentFactory {
	/**
	 * Stores content into the database
	 * @param IContent $content
	 */
	public static function SetContent($content);

    /**
     * Creates a new revision of the content object. This method must be implemented
     * by all factories. It will be called by the superclass ContentFactory
     * @param IContent $content The content to create a revision of
     * @return mixed
     */
    public static function CreateRevision($content);

    /**
     * Gets the content of the item with the given $id.
     * @param  int $id The contentId of the item
     * @return IContent
     */
    public static function GetContentById($id);
}