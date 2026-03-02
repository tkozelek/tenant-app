<?php

namespace App\Services;

use App\Models\Tenant;
use HTMLPurifier;
use HTMLPurifier_Config;

class TenantService
{
    public function __construct(protected FileService $fileService) {}

    public function createShop(array $data)
    {
        $file = $data['image'] ?? null;
        $title = $data['title_image'] ?? null;
        $fileRecord = $file ? $this->fileService->uploadFile($file) : null;
        $fileRecordTitle = $title ? $this->fileService->uploadFile($title) : null;

        $data['image_id'] = $fileRecord?->id;
        $data['title_id'] = $fileRecordTitle?->id;

        $shop = Tenant::create($data);

        $shop->users()->attach(auth()->id(), [
            'role_id' => config('constants.roles.majitel'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $shop;
    }

    public function updateShop(Tenant $shop, array $data)
    {
        $oldImage = $shop->image;
        $oldTitle = $shop->title;

        if (! empty($data['image'])) {
            $fileRecord = $this->fileService->uploadFile($data['image']);

            if ($oldImage) {
                $this->fileService->deleteFile($oldImage);
            }
            $shop->image_id = $fileRecord->id;
        }

        if (! empty($data['title_image'])) {
            $fileRecord = $this->fileService->uploadFile($data['title_image']);

            if ($oldTitle) {
                $this->fileService->deleteFile($oldTitle);
            }
            $shop->title_id = $fileRecord->id;
        }

        $data['description'] = $data['description'] ? $this->sanitizeDescription($data['description']) : null;

        $shop->update($data);

        return $shop;
    }

    private function sanitizeDescription(string $description): string
    {
        $config = HTMLPurifier_Config::createDefault();

        $config->set('HTML.Allowed', 'b,i,u,s,strong,em,a[href|title|target],img[src|alt|width|height],p,ul,ol,li,br,span[style],blockquote,code,pre,hr,h1,h2,h3,h4,h5,h6');

        $config->set('HTML.TargetBlank', true);

        $config->set('CSS.AllowedProperties', [
            'color', 'background-color', 'text-decoration', 'text-align', 'padding-left',
        ]);

        $config->set('Attr.AllowedFrameTargets', ['_blank']);

        $config->set('URI.DisableExternalResources', false);
        $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($description);
    }
}
