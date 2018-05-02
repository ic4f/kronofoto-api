<?php 
namespace Kronofoto\Test\Unit;

use Kronofoto\Models\CollectionModel;

class CollectionModelTest extends \Codeception\Test\Unit
{
    public function testValidateSortCriteria()
    {
        $model = new CollectionModel();
        $this->assertTrue($model->validateSort('id')); 
        $this->assertTrue($model->validateSort('name')); 
        $this->assertTrue($model->validateSort('yearMin')); 
        $this->assertTrue($model->validateSort('yearMax')); 
        $this->assertTrue($model->validateSort('itemCount')); 
        $this->assertTrue($model->validateSort('isPublished')); 
        $this->assertTrue($model->validateSort('created')); 
        $this->assertTrue($model->validateSort('modified')); 
        $this->assertTrue($model->validateSort('donorId')); 
        $this->assertTrue($model->validateSort('donorFirstName')); 
        $this->assertTrue($model->validateSort('donorLastName')); 
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
