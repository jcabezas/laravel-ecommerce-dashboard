<?php

namespace Tests\Unit;

use App\Http\Controllers\StoreController;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class UrlNormalizationTest extends TestCase
{
    public function el_metodo_normalizeUrl_limpia_correctamente_las_urls()
    {
        $controller = new StoreController();

        $method = new ReflectionMethod(StoreController::class, 'normalizeUrl');
        $method->setAccessible(true);

        // Definimos varios casos de prueba
        $urls = [
            'http://mitienda.com' => 'mitienda.com',
            'https://mitienda.com' => 'mitienda.com',
            'http://www.mitienda.com' => 'mitienda.com',
            'https://www.mitienda.com/' => 'mitienda.com',
            'mitienda.com/' => 'mitienda.com',
        ];

        foreach ($urls as $input => $expected) {
            $this->assertEquals($expected, $method->invoke($controller, $input));
        }
    }
}
