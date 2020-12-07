<?php
/************************************************************************
 * This file was created by devcrm.it which is product of dubas.pro
 *
 * Copyright (C) 2016-2020 DUBAS S.C.
 * Website: https://devcrm.it
 *
 ************************************************************************/

namespace Espo\Modules\DubasPublicAttachment\EntryPoints;

use \Espo\Core\Utils\Util;

use \Espo\Core\Exceptions\NotFound;
use \Espo\Core\Exceptions\Forbidden;
use \Espo\Core\Exceptions\BadRequest;
use \Espo\Core\Exceptions\Error;

use Espo\Core\Api\Request;

class DubasPublicAttachment extends \Espo\Core\EntryPoints\Base
{
    public static $authRequired = false;

    protected $allowedFileTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function run(Request $request)
    {
        $id = $request->get('id');

        if (!$id) {
            throw new BadRequest();
        }

        $attachment = $this->getEntityManager()->getEntity('Attachment', $id);

        if (!$attachment) {
            throw new NotFound();
        }

        $fileName = $this->getEntityManager()->getRepository('Attachment')->getFilePath($attachment);

        if (!file_exists($fileName)) {
            throw new NotFound();
        }

        $fileType = $attachment->get('type');

        if (!in_array($fileType, $this->allowedFileTypes)) {
            throw new Forbidden("EntryPoint Attachment: Not allowed type {$fileType}.");
        }

        if ($attachment->get('type')) {
            header('Content-Type: ' . $fileType);
        }

        header('Pragma: public');
        header('Content-Length: ' . filesize($fileName));
        readfile($fileName);
        exit;
    }
}
