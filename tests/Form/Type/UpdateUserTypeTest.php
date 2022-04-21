<?php

namespace App\Tests\Form\Type;

use App\Form\UpdateUserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UpdateUserTypeTest extends TypeTestCase
{
    public function testSubmitValidData(): void
    {
        $formData = [
            'firstName' => 'FirstName',
            'lastName' => 'LastName',
            'email' => 'email',
            'telephoneNr' => 'telephoneNr',
            'submit' => 'submit'
        ];
        $form = $this->factory->create(UpdateUserType::class);
        $resultedData = $form->submit($formData)->getData();
        $expected = [
            'firstName' => 'FirstName',
            'lastName' => 'LastName',
            'email' => 'email',
            'telephoneNr' => 'telephoneNr',
        ];

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $resultedData);
    }
}
