<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Rating;
use App\Models\Property;

class RatingController extends Controller
{
    public function submit(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('property');
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $name       = trim($_POST['reviewer_name'] ?? '');
        $rating     = (int)($_POST['rating']        ?? 0);
        $review     = trim($_POST['review']         ?? '');

        if (!$propertyId || $rating < 1 || $rating > 5) {
            $this->flash('error', 'Invalid rating submission.');
            $this->redirect('property/detail/' . $propertyId);
        }

        $ratingModel = new Rating();
        $ratingModel->create([
            ':property_id' => $propertyId,
            ':name'        => $name ?: 'Anonymous',
            ':rating'      => $rating,
            ':review'      => $review,
        ]);

        $propertyModel = new Property();
        $propertyModel->updateRatingAvg($propertyId);

        $this->flash('success', 'Thank you for your review!');
        $this->redirect('property/detail/' . $propertyId);
    }
}
