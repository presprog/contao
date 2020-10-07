<?php declare(strict_types=1);

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Contao\CoreBundle\EventListener\Undo;

use Contao\CoreBundle\Event\UndoDescriptionEvent;
use Contao\FilesModel;
use Contao\StringUtil;

class ContentUndoDescriptionListener
{
    public function __invoke(UndoDescriptionEvent $event): void
    {
        if (!$this->supports($event)) {
            return;
        }

        $description = $this->getDescriptionForType($event->getData());
        $event->setDescription($description);
    }

    private function getMethodFromType(string $type): string
    {
        return 'get' . ucwords($type) . 'Description';
    }

    private function getDescriptionForType(array $data): ?string
    {
        $method = $this->getMethodFromType($data['type']);
        return method_exists($this, $method) ? $this->{$method}($data) : null;
    }

    private function getHeadlineDescription(array $data): string
    {
        $headline = StringUtil::deserialize($data['headline']);
        return $headline['value'];
    }

    private function getTextDescription(array $data): string
    {
        $text = $data['text'];
        return StringUtil::substrHtml($text, 100);
    }

    private function getHtmlDescription(array $data): string
    {
        $html = htmlspecialchars($data['html']);
        return StringUtil::substr($html, 100);
    }

    private function getListDescription(array $data): string
    {
        $items = StringUtil::deserialize($data['listitems']);
        return implode(', ', array_values($items));
    }

    private function getTableDescription(array $data): string
    {
        $table = StringUtil::deserialize($data['tableitems']);
        return implode(', ', $table[0]);
    }

    private function getAccordionStartDescription(array $data): string
    {
        if (!empty($data['mooHeadline'])) {
            return $data['mooHeadline'];
        }

        return 'ID ' . $data['id'];
    }

    private function getAccordionStopDescription(array $data): string
    {
        return 'ID ' . $data['id'];
    }

    private function getAccordionSingleDescription(array $data): string
    {
        if (!empty($data['mooHeadline'])) {
            return $data['mooHeadline'];
        }

        return htmlspecialchars($data['text']);
    }

    private function getSliderStartDescription(array $data): string
    {
        if (!empty($data['headline'])) {
            return $data['headline'];
        }

        return 'ID ' . $data['id'];
    }

    private function getSliderStopDescription(array $data): string
    {
        if (!empty($data['headline'])) {
            return $data['headline'];
        }

        return 'ID ' . $data['id'];
    }

    private function getCodeDescription(array $data): string
    {
        return htmlspecialchars($data['code']);
    }

    private function getMarkdownDescription(array $data): string
    {
        return htmlspecialchars($data['markdown']);
    }

    private function getHyperlinkDescription(array $data): string
    {
        return $data['url'];
    }

    private function getToplinkDescription(array $data): string
    {
        return !empty($data['linkTitle']) ? $data['linkTitle'] : 'ID ' . $data['id'];
    }

//    private function getImageDescription(array $data): string
//    {
//        $uuid = StringUtil::binToUuid($data['singleSRC']);
//        $image = FilesModel::findByUuid($uuid);
//
//        return null !== $image ? $image->name . '.' . $image->extension : $uuid;
//    }

//    private function getGalleryDescription(array $data): string
//    {
//        $uuids = array_map(
//            static function ($uuid) {
//                return \StringUtil::binToUuid($uuid);
//            },
//            StringUtil::deserialize($data['multiSRC'])
//        );
//
//        $files = FilesModel::findMultipleByUuids($uuids);
//
//        if (null === $files) {
//            return implode(', ', $uuids);
//        }
//
//        $fileNames = array_reduce($files->fetchAll(), static function ($acc, $file) {
//            array_push($acc, $file['name']);
//
//            return $acc;
//        }, []);
//
//        return implode(', ', $fileNames);
//    }

    public function getYoutubeDescription(array $data): string
    {
        return $data['youtube'];
    }

    public function getVimeoDescription(array $data): string
    {
        return $data['vimeo'];
    }


    /**
     * @param UndoDescriptionEvent $event
     * @return bool
     */
    private function supports(UndoDescriptionEvent $event)
    {
        return ('tl_content' === $event->getTable());
    }
}
