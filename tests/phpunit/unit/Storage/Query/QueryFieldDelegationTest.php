<?php

namespace Bolt\Tests\Storage\Query;

use Bolt\Storage\Query\QueryResultset;
use Bolt\Tests\BoltUnitTest;

/**
 * Class to test src/Storage/Query/ContentQueryParser.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 */
class QueryFieldDelegationTest extends BoltUnitTest
{
    public function testTaxonomyFilter()
    {
        $app = $this->getApp();
        $this->addDefaultUser($app);
        $this->addSomeContent();

        $test1 = $app['storage']->getContent('entries', ['categories' => 'movies']);
        $test1count = count($test1);

        $test2 = $app['query']->getContent('entries', ['categories' => 'movies']);
        $test2count = count($test2);

        $this->assertEquals($test1count, $test2count);
        $this->assertEquals(2, $test2count);
    }

    public function testRelationFilter()
    {
        $app = $this->getApp();

        $results = $app['query']->getContent('showcases', ['entries' => '1 || 2 || 3']);
        $this->assertInstanceOf(QueryResultset::class, $results);
        foreach ($results as $result) {
            foreach ($result->relation['entries'] as $entry) {
                $this->assertTrue(in_array($entry->id, [1, 2, 3]));
                $this->assertEquals('entries', $entry->getContentType());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function addSomeContent($contentTypes = null, $categories = null, $count = null)
    {
        $app = $this->getApp();
        $storage = $app['storage'];
        parent::addSomeContent(['showcases', 'entries', 'pages']);

        // We also set some relations between showcases and entries
        $showcases = $storage->getContent('showcases');
        $randEntries = $storage->getContent('entries/random/2');
        foreach ($showcases as $show) {
            foreach (array_keys($randEntries) as $key) {
                $show->setRelation('entries', $key);
                $storage->saveContent($show);
            }
        }
        foreach ($randEntries as $entry) {
            $entry->setTaxonomy('categories', ['movies']);
            $storage->saveContent($entry);
        }
    }
}
