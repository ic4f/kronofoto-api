<?php namespace Kronofoto\Test;

use Kronofoto\Models\CollectionModel;

class CollectionModelTest extends \Codeception\Test\Unit
{
    public function testValidateSortCriteria()
    {
        $model = new CollectionModel();
        $this->assertTrue($model->validateSort('id')); 
        $this->assertTrue($model->validateSort('name')); 
        $this->assertTrue($model->validateSort('year_min')); 
        $this->assertTrue($model->validateSort('year_max')); 
        $this->assertTrue($model->validateSort('item_count')); 
        $this->assertTrue($model->validateSort('is_published')); 
        $this->assertTrue($model->validateSort('created')); 
        $this->assertTrue($model->validateSort('modified')); 
        $this->assertTrue($model->validateSort('donor_id')); 
        $this->assertTrue($model->validateSort('featured_item_id')); 
    }

    public function testValidateBadSortCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new CollectionModel();
        $model->validateSort('invalid'); 
    }

    public function testValidateBadFilterCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new CollectionModel();
        $this->assertTrue($model->validateFilter('any-criteria')); 
    }
}
