<?php

use Simple\Validator;

class RulesTest extends BaseTestCase
{
    public function testAlpha()
    {
        $_POST = [
            'name' => 'Abdul Muiz'
        ];

        $rules = [
            'name' => [['alpha', 1]]
        ];

        $v = new Validator($_POST);

        $this->assertEquals(true, $v->validate($rules));
    }

    public function testRegex()
    {
        $_POST = [
            'name' => 'Abdul Muiz'
        ];

        $rules = [
            'name' => [['regex', '/^[a-zA-Z-. ]+$/']]
        ];

        $v = new Validator($_POST);

        $this->assertEquals(true, $v->validate($rules));
    }
}
