<?php
class HomeController
{
    // GET /index.php
    public function index(): void
    {
        $categorias = Categoria::destacadas(4);
        $recetas = Receta::destacadas(6);

        // Imágenes fijas para las categorías populares (igual que en el original)
        $imagenes_categorias = [
            'Postres'             => 'pastel de chocolate.jpg',
            'Ensaladas'           => 'ensalada fresca.jpg',
            'Sopas'               => 'sopa de verduras.jpg',
            'Platos Principales'  => 'pollo al horno.jpg',
            'Desayunos'           => 'brownies-237776_1280.jpg',
            'Bebidas'             => 'ensalada de frutas.jpg',
        ];

        require APP_PATH . '/views/home/index.php';
    }
}
