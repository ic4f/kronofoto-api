<?php

//TODO: maybe delete this class: seems to be testing the Slim framework instead of 
//      testing the app code
class HttpMethodCest
{
    //TODO: move these out into a helper class or a config location
    const VALID_URL = '/api/collections'; //known valid url

    public function allowedMethodGet(ApiTester $I)
    {
        $I->wantTo('get a response to a GET request');
        $I->sendGET(self::VALID_URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK); //200
    }

    public function forbiddenMethodPost(ApiTester $I)
    {
        $I->wantTo('get no response to a POST request');
        $I->sendPOST(self::VALID_URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED); // 405
    }
    public function forbiddenMethodPut(ApiTester $I)
    {
        $I->wantTo('get no response to a PUT request');
        $I->sendPUT(self::VALID_URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED); // 405
    }

    public function forbiddenMethodPatch(ApiTester $I)
    {
        $I->wantTo('get no response to a PATCH request');
        $I->sendPATCH(self::VALID_URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED); // 405
    }

    public function forbiddenMethodDelete(ApiTester $I)
    {
        $I->wantTo('get no response to a DELETE request');
        $I->sendDELETE(self::VALID_URL); 
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::METHOD_NOT_ALLOWED); // 405
    }
}
