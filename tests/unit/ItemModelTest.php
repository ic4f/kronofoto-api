<?php
namespace Kronofoto\Test;

use Kronofoto\Models\ItemModel;

class ItemModelTest extends \Codeception\Test\Unit
{
    public function testValidateSortCriteria()
    {
        $model = new ItemModel();
        $this->assertTrue($model->validateSort('id'));
        $this->assertTrue($model->validateSort('identifier'));
        $this->assertTrue($model->validateSort('collectionId'));
        $this->assertTrue($model->validateSort('latitude'));
        $this->assertTrue($model->validateSort('longitude'));
        $this->assertTrue($model->validateSort('yearMin'));
        $this->assertTrue($model->validateSort('yearMax'));
        $this->assertTrue($model->validateSort('created')); 
        $this->assertTrue($model->validateSort('modified'));
    }


    public function testValidateBadSortCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new ItemModel();
        $model->validateSort('invalid'); 
    }

    public function testValidateFilterCriteria()
    {
        $model = new ItemModel();
        $this->assertTrue($model->validateFilter('identifier')); 
        $this->assertTrue($model->validateFilter('year')); 
        $this->assertTrue($model->validateFilter('before')); 
        $this->assertTrue($model->validateFilter('after')); 
        $this->assertTrue($model->validateFilter('collection')); 
    }

    public function testValidateBadFilterCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new ItemModel();
        $this->assertTrue($model->validateFilter('invalid')); 
    }
}

