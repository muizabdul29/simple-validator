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

        $v = new Validator();

        $this->assertEquals(true, $v->validate($rules, $_POST));
    }

    public function testRegex()
    {
        $_POST = [
            'name' => 'Abdul Muiz'
        ];

        $rules = [
            'name' => [['regex', '/^[a-zA-Z-. ]+$/']]
        ];

        $v = new Validator();

        $this->assertEquals(true, $v->validate($rules, $_POST));
    }

    public function testMultiple()
    {
        $_POST = [
            'full_name' => 'Abdul Muiz',
            'phone' => '1231234567',
            'email' => 'test@abc.com',
            'password' => 'very-secret-pass-123',
            't_and_c' => '1'
        ];

        $rules = [
            'full_name' => [ 'required', ['lengthBetween', 3, 32], ['regex', '/^[a-zA-Z-. ]+$/'] ],
            'phone' => [ 'required', 'digit', ['lengthBetween', 10, 11] ],
            'email' => [ 'required', 'email', ['lengthMax', 64] ],
            'password' => [ 'required', ['lengthBetween', 8, 32], ['regex', '/^(?=.*[a-zA-Z])(?=.*[0-9])/'] ],
            't_and_c' => [ 'accepted' ]
        ];

        $v = new Validator();
        
        $this->assertEquals(true, $v->validate($rules, $_POST));

    }
}
