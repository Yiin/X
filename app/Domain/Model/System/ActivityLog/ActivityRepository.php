<?php

namespace App\Domain\Model\System\ActivityLog;

use App\Domain\Model\Documents\Shared\AbstractDocument;

class ActivityRepository
{
    public function registerUserActivity($user, $action, AbstractDocument $document, $meta = [])
    {
        return Activity::create([
            'user_uuid' => $user ? $user->uuid : null,
            'action' => $action,
            'document_type' => get_class($document),
            'document_uuid' => $document->getKey(),
            'changes' => json_encode($document->getDirty()),
            'json_backup' => json_encode([
                'user' => $user ? $user->transform(['for_backup'])->toJson() : null,
                'documentRaw' => $document->toJson(),
                'documentTransformed' => $document->transform(['for_backup'])->toJson(),
                'meta' => json_encode($meta)
            ])
        ]);
    }

    public function getDocumentHistory(AbstractDocument $document)
    {
        return Activity::where([
            'document_type' => get_class($document),
            'document_uuid' => $document->getKey()
        ])->orderBy('id', 'desc')->get();
    }
}