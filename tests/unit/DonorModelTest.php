<?php
namespace Kronofoto\Test\Unit;

use Kronofoto\Models\DonorModel;

class DonorModelTest extends \Codeception\Test\Unit
{
    public function testValidateSortCriteria()
    {
        $model = new DonorModel();
        $this->assertTrue($model->validateSort('userId')); 
        $this->assertTrue($model->validateSort('firstName')); 
        $this->assertTrue($model->validateSort('lastName')); 
        $this->assertTrue($model->validateSort('collectionCount')); 
        $this->assertTrue($model->validateSort('itemCount')); 
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
