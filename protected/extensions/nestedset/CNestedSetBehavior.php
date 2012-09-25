<?php
/**
 * CNestedSetRecord class file.
 *
 * @author Remko Nolten <remko@nolten.nu>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Remko Nolten
 */

/**
 * Abstract behavior class that allows to store hierarchical data using the {@link CActiveRecord} class.
 * This abstract class defines the default function that every nested set class should have.
 *
 * @author Remko Nolten <remko@nolten.nu>
 * @package extensions.nestedset
 */

abstract class CNestedSetBehavior extends CActiveRecordBehavior
{


	/**
	 * Returns the childnodes of this node in an array.
	 * This function does not return the children of the child nodes (i.e. grandchildren)
	 * @return array() An array with the child nodes or an empty array when no child nodes are found.
	 */
	public abstract function getChildNodes();

	/**
	 * Returns false when the node is a leaf node (i.e. has no child nodes)
	 * @return boolean True when the node has childe nodes, false when the node is a leaf.
	 */
	public abstract function hasChildNodes();

    /**
     * Returns the depth of the current node in the complete tree.
     * @return int The depth of the node, where the root node is always at depth 0.
     */
    public abstract function getDepth();

    /**
     * Inserts this node as the last child of the given node.
     * if $brother is specified then this node will be inserted before
     * it's brother.
     * @param CNestedSetRecord $node The node to which you want to append this node.
     * @param CNestedSetRecord $brother The node before which you want to insert this node.
     * @return boolean True on succes or False on failure.
     */
    public abstract function appendChild($node, $brother = null) ;

    /**
     * Returns an array with all siblings of the current node. The current node is not included in this array.
     * @return array An array with sibling nodes;
     */
    public abstract function getSiblings();

    /**
     * Returns the next sibling (i.e. the sibling on the right of this node)
     * @return CNestedSetRecord The next sibling or null when no sibling is found
     */
    public abstract function getNextSibling();

    /**
     * Returns the previous sibling (i.e. the sibling on the right of this node)
     * @return CNestedSetRecord The previous sibling or null when no sibling is found.
     */
    public abstract function getPreviousSibling();

    /**
     * Returns the parent node of the current node.
     * @return CNestedSetRecord The parent node or null when there is no parent found (for example, when this node is the root node)
     */
    public abstract function getParentNode();

    /**
     * Insert this node before the given node (as a sibling on the left side)
     * @param CNestedSetRecord $node The given node
     * @return boolean True on success or False on failure.
     */
    public abstract  function insertBefore($node);

    /**
     * Insert this node after the given node (as a sibling on the right side)
     * @param CNestedSetRecord $node The given node
     * @return boolean True on success or False on failure.
     */
    public abstract function insertAfter($node);

    /**
     * Function to delete a node from the tree. The object does still exist, and you can insert it again in the tree.
     * In this case, it will get a new ID value.
     * Remember that when you delete the children as well, they are they are gone forever (even when you insert this node again).
     * @param boolean $deleteChildren When true, all children will also be deleted. This action CANNOT be undone!!!
     * @return boolean True on succes, False on failure. (Check the logs when deletion fails)
     */
    public abstract function deleteNode($deleteChildren = false);


    /**
     * Move a node one level up
     * @param boolean $after When true, the node is places after the parent node (this is the default behaviour).
     * When false, the node is places before the parent.
     * @return boolean Returns true on succes.
     * @throws TreeException on any error during moving the node.
     */
    public abstract function moveUp($after = true);


    /**
     * Move a node below a given node (i.e. appends this node as a child of the given node)
     * @param $node The node in the tree which becomes the new parent node.
     */
    public abstract function moveBelow($node);

    /**
     * Move a node to the left (i.e. exchange it with its left sibling)
     * Returns false when the node is in its most left position and there are no left siblings anymore.
     * @return boolean True on success.
     */
    public abstract function moveLeft();

    /**
     * Move this node to a position before the given node. (i.e. this node becomes the left sibling of the given node)
     * @param $node The given node
     * @return boolean True on success.
     */
    public abstract function moveBefore($node);

    /**
     * Move this node to a position after the given node. (i.e. this node becomes the right sibling of the given node)
     * @param $node The given node
     * @return boolean True on success.
     */
    public abstract function moveAfter($node);

    /**
     * Move a node to the right (i.e. exchange it with its right sibling)
     * Returns false when the node is in its most right position and there are no right siblings anymore.
     * @return boolean True on success.
     */
    public abstract function moveRight();


}