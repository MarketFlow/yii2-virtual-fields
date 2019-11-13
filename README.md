# yii2-virtual-fields
Implementation of virtual fields for Yii2 AR

This library contains 2 classes: ActiveRecord and ActiveQuery.

These classes together add support for virtual fields like CakePHP http://book.cakephp.org/2.0/en/models/virtual-fields.html

Virtual fields allow you to define model attributes as SQL fragments. The advantage of this implementation is that it supports both lazy and greedy loading.

Syntax:

In your ActiveRecord class define a virtualFields function, below is an example for the Author model where Author hasMany Posts.


    public static function virtualFields() {
      return [
        'postCount' => [
          'lazy' => function($model) { return $model->getPosts()->count; }
          'greedy' => Post::find()->limit(1)->select('count(*)')->where('author_id = author.id')
        ]
      ];
    }

The library will then take care of everything:

    Author::findByPk(1)->postCount; // Lazy loaded 
    Author::find()->select(['*', 'postCount'])->one()->postCount; // Greedy loaded
    
If lazy loading is not implemented and an attribute is used lazily, an exception will be thrown. If greedy loading is not implemented and the field is added to select the normal Yii2 / SQL exception is thrown.

We found these classes greatly helped us in reducing the number of queries (implementing getPostCount() on author is not ideal).

To maximize compatibility and minimize issues we chose not to use joins, since they can potentially affect the number of records. In a number of cases the resulting query plan could be less than optimal.

