<?php

namespace App\Controller;

use App\DTO\Request\CreateShortLinkRequest;
use App\DTO\Responses\CreateShortLinkResponse;
use App\Service\AppSerializer;
use App\Service\ShortLinkService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Routing\Attribute\Route;

final class ShortLinksController extends BaseController
{
    public function __construct(
        AppSerializer $appSerializer,
        private readonly ShortLinkService $shortLinkService
    ) {
        parent::__construct($appSerializer);
    }

    #[Route('/shortlink', name: 'app_short_links', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Возвращает короткую ссылку',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'success', type: Type::BUILTIN_TYPE_BOOL),
                new OA\Property(
                    property: 'result',
                    type: Type::BUILTIN_TYPE_ARRAY,
                    items: new OA\Items(ref: new Model(type: CreateShortLinkResponse::class)),
                ),
            ]
        )
    )]
    #[OA\QueryParameter(name: 'originalUrl', description: 'оригинальный URL', required: true)]
    public function createShortLink(
        #[MapQueryString]
        CreateShortLinkRequest $request
    ): Response {
        return $this->appJson($this->shortLinkService->process($request->original_url));
    }
}
