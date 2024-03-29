<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\OneStepCheckout\Test\Unit\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Lastname;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute\Lastname
 */
class LastnameTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Lastname
     */
    private $modifier;

    /**
     * @var CustomerSession|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerSessionMock;

    /**
     * @var CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerRepositoryMock;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->customerSessionMock = $this->getMockBuilder(CustomerSession::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'isLoggedIn',
                'getCustomerId'
            ])
            ->getMock();
        $this->customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);
        $this->modifier = $objectManager->getObject(
            Lastname::class,
            [
                'customerSession' => $this->customerSessionMock,
                'customerRepository' => $this->customerRepositoryMock
            ]
        );
    }

    public function testModify()
    {
        $customerId = 1;
        $customerLastName = 'Doe';

        $customerMock = $this->getMockForAbstractClass(CustomerInterface::class);

        $this->customerSessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(true);
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);
        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->willReturn($customerMock);
        $customerMock->expects($this->once())
            ->method('getLastname')
            ->willReturn($customerLastName);

        $this->assertEquals(
            [
                'label' => 'Last Name',
                'default' => $customerLastName
            ],
            $this->modifier->modify(['label' => 'Last Name'], 'shipping')
        );
    }

    public function testModifyNotLoggedIn()
    {
        $this->customerSessionMock->expects($this->once())
            ->method('isLoggedIn')
            ->willReturn(false);

        $this->assertEquals(
            ['label' => 'Last Name'],
            $this->modifier->modify(['label' => 'Last Name'], 'shipping')
        );
    }
}
