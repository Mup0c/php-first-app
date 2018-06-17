<?php

namespace AppTest\App\Authentication\Service;

use App\Authentication\Repository\UserRepositoryInterface;
use App\Authentication\Service\AuthenticationService;
use PHPUnit\Framework\TestCase;

class AuthenticationServiceTest extends TestCase
{
	public function testShouldReturnsAnonymousTokenIfUserNotExists()
	{
		$someId = 13;

		$repository = $this->createMock(UserRepositoryInterface::class);
		$repository
            ->expects($this->once()) // replace with $this->once()
			->method('findById')
			->with($someId)
			->willReturn(null);

		$service = new AuthenticationService($repository);
		$userToken = $service->authenticate("{$someId}/231");
		$this->assertTrue($userToken->isAnonymous());
	}

    public function testShouldReturnsAnonymousTokenIfCredsNotValid()
    {
        $repository = $this->createMock(UserRepositoryInterface::class);
        $repository
            ->expects($this->never())
            ->method('findById');

        $service = new AuthenticationService($repository);
        $userToken = $service->authenticate("notvalid");
        $this->assertTrue($userToken->isAnonymous());
    }
}