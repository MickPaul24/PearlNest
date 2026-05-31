<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Property;
use App\Models\Inquiry;
use App\Models\Rating;

class PropertyController extends Controller
{
    public function index(): void
    {
        $propertyModel = new Property();

        $filters = [
            'type'       => $_GET['type']       ?? '',
            'district'   => $_GET['district']   ?? '',
            'min_price'  => $_GET['min_price']  ?? '',
            'max_price'  => $_GET['max_price']  ?? '',
            'bedrooms'   => $_GET['bedrooms']   ?? '',
            'min_rating' => $_GET['min_rating'] ?? '',
            'search'     => $_GET['search']     ?? '',
            'sort'       => $_GET['sort']       ?? '',
        ];

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9;

        $properties = $propertyModel->getAll($filters, $page, $perPage);
        $total      = $propertyModel->countAll($filters);
        $pages      = (int)ceil($total / $perPage);
        $districts  = $propertyModel->getDistricts();

        $this->view('properties', [
            'title'      => 'Browse Properties',
            'properties' => $properties,
            'total'      => $total,
            'page'       => $page,
            'pages'      => $pages,
            'perPage'    => $perPage,
            'filters'    => $filters,
            'districts'  => $districts,
            'typeOptions'=> Property::typeOptions(),
        ]);
    }

    public function detail(string $id = ''): void
    {
        $propertyModel = new Property();
        $property      = $propertyModel->getById((int)$id);

        if (!$property) {
            http_response_code(404);
            $this->view('404', ['title' => 'Property Not Found']);
            return;
        }

        $propertyModel->incrementViews((int)$id);

        $ratingModel = new Rating();
        $ratings     = $ratingModel->getByPropertyId((int)$id);

        // Related properties (same district, different id)
        $related = $propertyModel->getAll([
            'district' => $property['district'],
        ], 1, 3);
        $related = array_filter($related, fn($r) => $r['id'] !== $property['id']);

        $this->view('property-detail', [
            'title'    => $property['title'] . ' — PearlNest',
            'property' => $property,
            'ratings'  => $ratings,
            'related'  => array_values($related),
        ]);
    }

    public function inquiry(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('property');
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $name       = trim($_POST['name']    ?? '');
        $email      = trim($_POST['email']   ?? '');
        $phone      = trim($_POST['phone']   ?? '');
        $message    = trim($_POST['message'] ?? '');

        if (!$name || !$email || !$message) {
            $this->flash('error', 'Please fill in all required fields.');
            $this->redirect('property/detail/' . $propertyId);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect('property/detail/' . $propertyId);
        }

        $inquiryModel = new Inquiry();
        $inquiryModel->create([
            ':property_id' => $propertyId ?: null,
            ':name'        => $name,
            ':email'       => $email,
            ':phone'       => $phone,
            ':message'     => $message,
        ]);

        $this->flash('success', 'Inquiry sent! Our broker will contact you shortly.');
        $this->redirect('property/detail/' . $propertyId);
    }
}
