<?php

namespace App\Tests\Form\Type;

use App\Form\AddUserType;
use Symfony\Component\Form\Test\TypeTestCase;

class AddUserTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'firstname' => 'FirstName',
            'lastname' => 'LastName',
            'email' => 'email',
            'telephoneNr' => 'telephoneNr',
            'plainPassword' => 'plainPassword',
            'cnp' => 'cnp',
            'role' => 'role',
            'submit' => 'submit'
        ];
        $form = $this->factory->create(AddUserType::class);
        $resultedData = $form->submit($formData)->getData();
        $expected = [
            'firstname' => 'FirstName',
            'lastname' => 'LastName',
            'email' => 'email',
            'telephoneNr' => 'telephoneNr',
            'cnp' => 'cnp',
            'role' => 'role',
        ];

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $resultedData);
    }
}
