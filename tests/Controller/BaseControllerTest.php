<?php

namespace App\Tests\Controller;

use App\Controller\BaseController;
use App\Service\AppSerializer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class BaseControllerTest extends TestCase
{
    private BaseController $controller;
    private AppSerializer $appSerializerMock;

    protected function setUp(): void
    {
        $this->appSerializerMock = $this->createMock(AppSerializer::class);

        $this->controller = new class($this->appSerializerMock) extends BaseController {
        };
    }

    public function testAppJsonReturnsResponseWithSerializedData(): void
    {
        $data = ['key' => 'value'];
        $serializedData = '{"key":"value"}';
        $status = Response::HTTP_OK;
        $headers = ['X-Custom-Header' => 'HeaderValue'];
        $context = ['context' => 'value'];

        $this->appSerializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($data), 'json', $this->equalTo($context))
            ->willReturn($serializedData);

        $response = $this->controller->appJson($data, $status, $headers, $context);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertSame($serializedData, $response->getContent());

        $this->assertSame($status, $response->getStatusCode());

        $this->assertSame('application/json', $response->headers->get('content-type'));
        $this->assertSame('HeaderValue', $response->headers->get('X-Custom-Header'));
    }

    public function testAppJsonWithDefaultStatusAndHeaders(): void
    {
        $data = ['key' => 'value'];
        $serializedData = '{"key":"value"}';

        $this->appSerializerMock
            ->expects($this->once())
            ->method('serialize')
            ->with($this->equalTo($data), 'json', $this->equalTo([]))
            ->willReturn($serializedData);

        $response = $this->controller->appJson($data);

        $this->assertInstanceOf(Response::class, $response);

        $this->assertSame($serializedData, $response->getContent());

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $this->assertSame('application/json', $response->headers->get('content-type'));
    }
}