<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin;
use App\Models\Property;
use App\Models\Inquiry;

class AdminController extends Controller
{
    // ──────────────────────────────────────────
    //  Auth
    // ──────────────────────────────────────────

    public function index(): void
    {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('admin/dashboard');
        } else {
            $this->redirect('admin/login');
        }
    }

    public function login(): void
    {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('admin/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $adminModel = new Admin();
            $admin      = $adminModel->findByUsername($username);

            if ($admin && $adminModel->verifyPassword($admin, $password)) {
                $_SESSION['admin_id']   = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'] ?: $admin['username'];
                $this->redirect('admin/dashboard');
            }

            $this->flash('error', 'Invalid username or password.');
        }

        $this->view('admin/login', ['title' => 'Admin Login']);
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('admin/login');
    }

    // ──────────────────────────────────────────
    //  Dashboard
    // ──────────────────────────────────────────

    public function dashboard(): void
    {
        $this->requireAdmin();

        $propertyModel = new Property();
        $inquiryModel  = new Inquiry();

        $propStats     = $propertyModel->getStats();
        $pendingInq    = $inquiryModel->countPending();
        $totalInq      = $inquiryModel->countAll();
        $recentProps   = $propertyModel->getAll(['admin' => true], 1, 10);

        $this->view('admin/dashboard', [
            'title'       => 'Admin Dashboard',
            'propStats'   => $propStats,
            'pendingInq'  => $pendingInq,
            'totalInq'    => $totalInq,
            'recentProps' => $recentProps,
        ]);
    }

    // ──────────────────────────────────────────
    //  Properties
    // ──────────────────────────────────────────

    public function properties(): void
    {
        $this->requireAdmin();

        $propertyModel = new Property();
        $page          = max(1, (int)($_GET['page'] ?? 1));
        $search        = $_GET['search'] ?? '';
        $status        = $_GET['status'] ?? '';

        $filters = ['admin' => true, 'search' => $search, 'status' => $status];
        $properties = $propertyModel->getAll($filters, $page, 10);
        $total      = $propertyModel->countAll($filters);
        $pages      = (int)ceil($total / 10);

        $this->view('admin/properties', [
            'title'      => 'Properties List',
            'properties' => $properties,
            'total'      => $total,
            'page'       => $page,
            'pages'      => $pages,
            'search'     => $search,
            'statusFilter' => $status,
        ]);
    }

    public function add(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAddProperty();
            return;
        }

        $this->view('admin/add-property', [
            'title'       => 'Add New Property',
            'typeOptions' => Property::typeOptions(),
        ]);
    }

    public function edit(string $id = ''): void
    {
        $this->requireAdmin();

        $propertyModel = new Property();
        $property      = $propertyModel->getById((int)$id);

        if (!$property) {
            $this->flash('error', 'Property not found.');
            $this->redirect('admin/properties');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleEditProperty((int)$id, $propertyModel);
            return;
        }

        $this->view('admin/edit-property', [
            'title'       => 'Edit Property',
            'property'    => $property,
            'typeOptions' => Property::typeOptions(),
        ]);
    }

    public function delete(string $id = ''): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/properties');
        }

        $propertyModel = new Property();
        $property      = $propertyModel->getById((int)$id);

        if (!$property) {
            $this->flash('error', 'Property not found.');
            $this->redirect('admin/properties');
        }

        // Delete physical image files
        foreach ($property['images'] as $img) {
            if (strpos($img['image_path'], 'uploads/') !== false) {
                $fullPath = __DIR__ . '/../../public/' . $img['image_path'];
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $propertyModel->delete((int)$id);
        $this->flash('success', 'Property deleted successfully.');
        $this->redirect('admin/properties');
    }

    public function setstatus(string $id = ''): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/properties');
        }

        $status = $_POST['status'] ?? '';
        if (!in_array($status, ['available', 'rented', 'sold', 'under_review'])) {
            $this->flash('error', 'Invalid status.');
            $this->redirect('admin/properties');
        }

        $propertyModel = new Property();
        $propertyModel->updateStatus((int)$id, $status);
        $this->flash('success', 'Property status updated.');
        $this->redirect('admin/properties');
    }

    public function deleteimage(string $id = ''): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/properties');
        }

        $propertyId = (int)($_POST['property_id'] ?? 0);
        $propertyModel = new Property();
        $propertyModel->deleteImage((int)$id);

        $this->flash('success', 'Image removed.');
        $this->redirect('admin/edit/' . $propertyId);
    }

    // ──────────────────────────────────────────
    //  Inquiries
    // ──────────────────────────────────────────

    public function inquiries(): void
    {
        $this->requireAdmin();

        $inquiryModel = new Inquiry();
        $page         = max(1, (int)($_GET['page'] ?? 1));
        $inquiries    = $inquiryModel->getAll($page, 20);
        $total        = $inquiryModel->countAll();
        $pages        = (int)ceil($total / 20);

        $this->view('admin/inquiries', [
            'title'     => 'Inquiries',
            'inquiries' => $inquiries,
            'total'     => $total,
            'page'      => $page,
            'pages'     => $pages,
        ]);
    }

    public function closeinquiry(string $id = ''): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/inquiries');
        }

        $status = $_POST['status'] ?? 'responded';
        $inquiryModel = new Inquiry();
        $inquiryModel->updateStatus((int)$id, $status);
        $this->flash('success', 'Inquiry status updated.');
        $this->redirect('admin/inquiries');
    }

    public function deleteinquiry(string $id = ''): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/inquiries');
        }

        $inquiryModel = new Inquiry();
        $inquiryModel->delete((int)$id);
        $this->flash('success', 'Inquiry deleted.');
        $this->redirect('admin/inquiries');
    }

    // ──────────────────────────────────────────
    //  Settings
    // ──────────────────────────────────────────

    public function settings(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminModel = new Admin();
            $action     = $_POST['action'] ?? '';

            if ($action === 'profile') {
                $adminModel->updateProfile($_SESSION['admin_id'], [
                    'name'  => trim($_POST['name']  ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                ]);
                $this->flash('success', 'Profile updated.');
            } elseif ($action === 'password') {
                $current = $_POST['current_password'] ?? '';
                $new     = $_POST['new_password']     ?? '';
                $confirm = $_POST['confirm_password'] ?? '';

                $adminModel2 = new Admin();
                $admin       = $adminModel2->findByUsername($_SESSION['admin_name']);
                if (!$admin || !$adminModel2->verifyPassword($admin, $current)) {
                    $this->flash('error', 'Current password is incorrect.');
                } elseif ($new !== $confirm || strlen($new) < 6) {
                    $this->flash('error', 'Passwords do not match or are too short (min 6 chars).');
                } else {
                    $adminModel->updatePassword($_SESSION['admin_id'], $new);
                    $this->flash('success', 'Password changed successfully.');
                }
            }

            $this->redirect('admin/settings');
        }

        $adminModel = new Admin();
        $admin      = $adminModel->findByUsername($_SESSION['admin_name'] ?? '');

        $this->view('admin/settings', [
            'title' => 'Settings',
            'admin' => $admin,
        ]);
    }

    // ──────────────────────────────────────────
    //  Private helpers
    // ──────────────────────────────────────────

    private function handleAddProperty(): void
    {
        $data = [
            ':title'        => trim($_POST['title']       ?? ''),
            ':type'         => $_POST['type']             ?? 'studio',
            ':description'  => trim($_POST['description'] ?? ''),
            ':location'     => trim($_POST['location']    ?? ''),
            ':district'     => trim($_POST['district']    ?? ''),
            ':address'      => trim($_POST['address']     ?? ''),
            ':price'        => (float)($_POST['price']    ?? 0),
            ':price_period' => $_POST['price_period']     ?? 'month',
            ':bedrooms'     => (int)($_POST['bedrooms']   ?? 1),
            ':bathrooms'    => (int)($_POST['bathrooms']  ?? 1),
            ':area_sqm'     => $_POST['area_sqm']         ? (int)$_POST['area_sqm'] : null,
            ':status'       => $_POST['status']           ?? 'available',
            ':is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            ':amenities'    => trim($_POST['amenities']   ?? ''),
            ':badge'        => trim($_POST['badge']       ?? '') ?: null,
        ];

        if (!$data[':title'] || !$data[':location'] || $data[':price'] <= 0) {
            $this->flash('error', 'Title, location, and price are required.');
            $this->redirect('admin/add');
        }

        $propertyModel = new Property();
        $propertyId    = $propertyModel->create($data);

        $this->handleImageUploads($propertyId, $propertyModel);

        $this->flash('success', 'Property added successfully!');
        $this->redirect('admin/edit/' . $propertyId);
    }

    private function handleEditProperty(int $id, Property $propertyModel): void
    {
        $data = [
            'title'        => trim($_POST['title']       ?? ''),
            'type'         => $_POST['type']             ?? 'studio',
            'description'  => trim($_POST['description'] ?? ''),
            'location'     => trim($_POST['location']    ?? ''),
            'district'     => trim($_POST['district']    ?? ''),
            'address'      => trim($_POST['address']     ?? ''),
            'price'        => (float)($_POST['price']    ?? 0),
            'price_period' => $_POST['price_period']     ?? 'month',
            'bedrooms'     => (int)($_POST['bedrooms']   ?? 1),
            'bathrooms'    => (int)($_POST['bathrooms']  ?? 1),
            'area_sqm'     => $_POST['area_sqm']         ? (int)$_POST['area_sqm'] : null,
            'status'       => $_POST['status']           ?? 'available',
            'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            'amenities'    => trim($_POST['amenities']   ?? ''),
            'badge'        => trim($_POST['badge']       ?? '') ?: null,
        ];

        $propertyModel->update($id, $data);
        $this->handleImageUploads($id, $propertyModel);

        $this->flash('success', 'Property updated successfully!');
        $this->redirect('admin/edit/' . $id);
    }

    private function handleImageUploads(int $propertyId, Property $propertyModel): void
    {
        if (empty($_FILES['images']['name'][0])) {
            return;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $isFirst      = true;
        $existingImages = $propertyModel->getImages($propertyId);
        $hasPrimary   = !empty(array_filter($existingImages, fn($i) => $i['is_primary']));

        foreach ($_FILES['images']['tmp_name'] as $idx => $tmpName) {
            if (!is_uploaded_file($tmpName)) {
                continue;
            }

            $mimeType = mime_content_type($tmpName);
            if (!in_array($mimeType, $allowedTypes)) {
                continue;
            }

            if ($_FILES['images']['size'][$idx] > 5 * 1024 * 1024) {
                continue;
            }

            $ext      = pathinfo($_FILES['images']['name'][$idx], PATHINFO_EXTENSION);
            $filename = uniqid('prop_', true) . '.' . strtolower($ext);
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destPath)) {
                $isPrimary = $isFirst && !$hasPrimary;
                $propertyModel->addImage($propertyId, 'uploads/images/' . $filename, $isPrimary);
                $isFirst = false;
            }
        }

        // Handle video upload
        if (!empty($_FILES['video']['tmp_name']) && is_uploaded_file($_FILES['video']['tmp_name'])) {
            $videoDir  = __DIR__ . '/../../public/uploads/videos/';
            if (!is_dir($videoDir)) {
                mkdir($videoDir, 0755, true);
            }
            $allowedVideo = ['video/mp4', 'video/webm', 'video/ogg'];
            $mime         = mime_content_type($_FILES['video']['tmp_name']);
            if (in_array($mime, $allowedVideo) && $_FILES['video']['size'] <= 50 * 1024 * 1024) {
                $ext      = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('vid_', true) . '.' . strtolower($ext);
                $destPath = $videoDir . $filename;
                if (move_uploaded_file($_FILES['video']['tmp_name'], $destPath)) {
                    $propertyModel->addVideo($propertyId, 'uploads/videos/' . $filename, $_POST['video_title'] ?? '');
                }
            }
        }
    }
}
