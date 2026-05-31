<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Property;

class HomeController extends Controller
{
    public function index(): void
    {
        $propertyModel = new Property();
        $featured      = $propertyModel->getFeatured(6);
        $stats         = $propertyModel->getStats();
        $districts     = $propertyModel->getDistricts();

        $this->view('home', [
            'title'     => 'Find Your Next Home in Uganda',
            'featured'  => $featured,
            'stats'     => $stats,
            'districts' => $districts,
        ]);
    }
}
