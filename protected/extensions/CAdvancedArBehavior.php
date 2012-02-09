<?php
/**
 * CAdvancedArBehavior class file.
 *
 * @author Herbert Maschke <thyseus@gmail.com>
 * @link http://www.yiiframework.com/
 * @version 0.2
 */

/* The CAdvancedArBehavior extension adds up some functionality to the default
 * possibilites of yii´s ActiveRecord implementation.
 *
 * To use this extension, just copy this file to your extensions/ directory,
 * add 'import' => 'application.extensions.CAdvancedArBehavior', [...] to your 
 * config/main.php and add this behavior to each model you would like to
 * inherit the new possibilities:
 *
 * public function behaviors(){
 *         return array( 'CAdvancedArBehavior' => array(
 *        	 'class' => 'application.extensions.CAdvancedArBehavior')); 
 *         }                                  
 *
 *
 * Better support of MANY_TO_MANY relations:
 *
 * When we have defined a MANY_MANY relation in our relations() function, we
 * are now able to add up instances of the foreign Model on the fly while
 * saving our Model to the Database. Let´s assume the following Relation:
 *
 * Post has:
 *  'categories'=>array(self::MANY_MANY, 'Category',
 *                  'tbl_post_category(post_id, category_id)')
 *
 * Category has:
 * 'posts'=>array(self::MANY_MANY, 'Post',
 *                  'tbl_post_category(category_id, post_id)')
 *
 * Now we can use the attribute 'categories' of our Post model to add up new
 * rows to our MANY_MANY connection Table:
 *
 * $post = new Post();
 * $post->categories = Category::model()->findAll();
 * $post->save();
 *
 * This will save our new Post in the table Post, and in addition to this it
 * updates our N:M-Table with every Category available in the Database.
 * 
 * We can further limit the Objects given to the attribute, and can also go 
 * the other Way around:
 *
 * $category = new Category();
 * $category->posts = array(5, 6, 7, 10);
 * $caregory->save(); 
 *
 * We can pass Object instances like in the first example, or a list of
 * integers that representates the Primary key of the Foreign Table, so that
 * the Posts with the id 5, 6, 7 and 10 get´s added up to our new Category.
 *
 * 5 Queries will be performed here, one for the Category-Model and four for
 * the N:M-Table tbl_post_category. Note that this behavior could be tuned
 * further in the future, so only one query get´s executed for the MANY_MANY
 * Table.
 *
 * We can also pass a _single_ object or an single integer:
 *
 * $category = new Category();
 * $category->posts = Post::model()->findByPk(12);
 * $category->posts = 12;
 * $category->save();
 */


class CAdvancedArbehavior extends CActiveRecordBehavior
{
	public function afterSave($on) 
	{
		$this->writeManyManyTables();
		return TRUE;
	}

	/**
	 * At first, this function cycles through each MANY_MANY Relation. Then
	 * it checks if the attribute of the Object instance is an integer, an
	 * array or another ActiveRecord instance. It then builds up the SQL-Query
	 * to add up the needed Data to the MANY_MANY-Table given in the relation
	 * settings.
	 */
	public function writeManyManyTables() 
	{
		Yii::trace('writing MANY_MANY data for '.get_class($this->owner),'system.db.ar.CActiveRecord');

		foreach($this->owner->relations() as $key => $relation)
		{
			if($relation['0'] == CActiveRecord::MANY_MANY) // ['0'] equals relationType
			{
				if(isset($this->owner->$key))
				{
					if(is_object($this->owner->$key) || is_numeric($this->owner->$key))
					{
						$this->executeManyManyEntry($this->makeManyManyDeleteCommand(
							$relation[2],
							$this->owner->{$this->owner->tableSchema->primaryKey}));
						$this->executeManyManyEntry($this->owner->makeManyManyInsertCommand(
							$relation[2],
							(is_object($this->owner->$key))
							? $this->owner->$key->{$this->owner->$key->tableSchema->primaryKey}
							: $this->owner->{$key}));
					}
					else //if (is_array($this->owner->$key) && $this->owner->$key != array())
					{
						$this->executeManyManyEntry($this->makeManyManyDeleteCommand(
							$relation[2],
							$this->owner->{$this->owner->tableSchema->primaryKey}));
						foreach((array)$this->owner->$key as $foreignobject)
						{
							$this->executeManyManyEntry ($this->makeManyManyInsertCommand(
								$relation[2],
								(is_object($foreignobject))
								? $foreignobject->{$foreignobject->tableSchema->primaryKey}
								: $foreignobject));
						}
					}
				}
			}
		}
	}

	// We can't throw an Exception when this query fails, because it is possible
	// that there is not row available in the MANY_MANY table, thus execute()
	// returns 0 and the error gets thrown falsely.
	public function executeManyManyEntry($query) {
		Yii::app()->db->createCommand($query)->execute();
	}

	// It is important to use insert IGNORE so SQL doesn't throw an foreign key
	// integrity violation
	public function makeManyManyInsertCommand($model, $rel) {
		return sprintf("insert ignore into %s values ('%s', '%s')", $model,	$this->owner->{$this->owner->tableSchema->primaryKey}, $rel);
	}

	public function makeManyManyDeleteCommand($model, $rel) {
		return sprintf("delete ignore from %s where %s = '%s'", $this->getManyManyTable($model), $this->getRelationNameForDeletion($model), $rel);
	}

	public function getManyManyTable($model) {
		if (($ps=strpos($model, '('))!==FALSE)
		{
			return substr($model, 0, $ps);
		}
		else
			return $model;
	}

	public function getRelationNameForDeletion($model) {
		preg_match('/\((.*),/',$model, $matches) ;
		return substr($matches[0], 1, strlen($matches[0]) - 2);
	}
}
