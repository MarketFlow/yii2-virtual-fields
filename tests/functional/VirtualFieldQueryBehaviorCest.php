<?php

use SamIT\Yii2\VirtualFields\VirtualFieldQueryBehavior;
use yii\base\InvalidConfigException;

class VirtualFieldQueryBehaviorCest
{
    public function _before(FunctionalTester $I)
    {
        \tests\Author::createTable();
        \tests\Post::createTable();
        $author = new \tests\Author();
        $author->name = 'test';
        $author->id = 15;
        $I->assertTrue($author->save());
        $post= new \tests\Post();
        $post->name = 'test post';
        $post->author_id = $author->id;
        $I->assertTrue($post->save());
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {

        $query = \tests\Author::find();
        $behavior = new VirtualFieldQueryBehavior();
        $I->expectThrowable(\Throwable::class, function() use ($query) {
            $query->withField('postCount');
        });


        $query->attachBehavior(VirtualFieldQueryBehavior::class, $behavior);
        $I->expectThrowable(InvalidConfigException::class, function() use ($query) {
            $query->withField('Invalid');
        });
        $query->withField('postCount');
        $I->assertSame([[
            'id' => '15',
            'name' => 'test',
            'postCount' => '1'
        ]], $query->asArray()->all());


        $author = $query->asArray(false)->one();
        $I->assertInstanceOf(\tests\Author::class, $author);

        $post = new \tests\Post();
        $post->name = 'test post';
        $post->author_id = 15;
        $I->assertTrue($post->save());

        $I->assertSame(1, $author->postCount);
    }
}
