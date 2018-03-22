<?php
namespace Kronofoto\Test;

use Kronofoto\Models\DonorModel;

class DonorModelTest extends \Codeception\Test\Unit
{
    public function testValidateSortCriteria()
    {
        $model = new DonorModel();
        $this->assertTrue($model->validateSort('user_id')); 
        $this->assertTrue($model->validateSort('first_name')); 
        $this->assertTrue($model->validateSort('last_name')); 
        $this->assertTrue($model->validateSort('collection_count')); 
        $this->assertTrue($model->validateSort('item_count')); 
        $this->assertTrue($model->validateSort('created')); 
        $this->assertTrue($model->validateSort('modified')); 
    }

    public function testValidateBadSortCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new DonorModel();
        $model->validateSort('invalid'); 
    }

    public function testValidateFilterCriteria()
    {
        $model = new DonorModel();
        $this->assertTrue($model->validateFilter('first_name')); 
        $this->assertTrue($model->validateFilter('last_name')); 
    }

    public function testValidateBadFilterCriteria()
    {
        $this->expectException(\Exception::class);
        $model = new DonorModel();
        $this->assertTrue($model->validateFilter('invalid')); 
    }
}
