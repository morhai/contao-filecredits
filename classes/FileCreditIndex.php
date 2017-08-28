<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 *
 * @package filecredits
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FileCredit;


use HeimrichHannot\Haste\Cache\FileCache;

class FileCreditIndex extends \Controller
{

    public static function doIndex()
    {
        global $objPage;

        // Index page if there is no back end user
        if ($objPage->type == 'regular' && !BE_USER_LOGGED_IN) {
            // Index protected pages if enabled
            if ((!FE_USER_LOGGED_IN && !$objPage->protected)) {
                // Do not index the page if certain parameters are set
                foreach (array_keys($_GET) as $key) {
                    if (in_array($key, $GLOBALS['TL_NOINDEX_KEYS']) || strncmp($key, 'page_', 5) === 0) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }

    public static function addFile($objFile)
    {
        $checksum = md5(preg_replace('/ +/', ' ', strip_tags($objFile->copyright)));

        $objModel = FileCreditModel::findByUuid($objFile->uuid);

        // do not index again if copyright did not change, but add the current page
        if ($objModel !== null && $checksum == $objModel->checksum) {
            static::addCurrentPage($objModel);
            return true;
        }

        $arrSet = [
            'tstamp'    => time(),
            'uuid'      => $objFile->uuid,
            'checksum'  => $checksum,
            'published' => 1,
            'start'     => '',
            'stop'      => '',
        ];

        if ($objModel !== null) {
            // delete: remove credit if copyright is empty
            if ($objFile->copyright == '') {
                // remove all pages for the credit before
                FileCreditPageModel::deleteByPid($objModel->id);

                $objModel->delete();

                return false;
            }

            // update: otherwise update existing filecredit
            $objModel->setRow($arrSet);
            $objModel->save();

            static::addCurrentPage($objModel);

            return true;
        }

        // create: add new credit
        if ($objFile->copyright != '') {
            $objModel = new FileCreditModel();
            $objModel->setRow($arrSet);
            $objModel->save();

            static::addCurrentPage($objModel);

            return true;
        }

        return false;
    }

    protected static function addCurrentPage(FileCreditModel $objCredit)
    {
        if (!$objCredit->id) {
            return false;
        }

        global $objPage;

        $strRequestRaw = preg_replace('/\\?.*/', '', \Environment::get('request')); // strip get parameter from url
        $strRequest    = rtrim($strRequestRaw, "/");
        $strAlias      = \Controller::generateFrontendUrl($objPage->row());

        // only accept pages or auto_item pages
        if (!Validator::isRequestAlias($strRequest, $strAlias)) {
            // cleanup pages that no longer exist
            $objModel = FileCreditPageModel::findByPidAndPageAndUrl($objCredit->id, $objPage->id, $strRequestRaw);

            if ($objModel !== null) {
                while ($objModel->next()) {
                    $objModel->delete();
                }
            }

            return false;
        }

        $objModel = FileCreditPageModel::findByPidAndPageAndUrl($objCredit->id, $objPage->id, $strRequest);

        // do not index page again
        if ($objModel !== null) {
            return false;
        }

        $arrSet = [
            'pid'       => $objCredit->id,
            'tstamp'    => time(),
            'page'      => $objPage->id,
            'url'       => $strRequest,
            'protected' => ($objPage->protected ? '1' : ''),
            'groups'    => $objPage->groups,
            'language'  => $objPage->language,
            'published' => 1,
            'start'     => '',
            'stop'      => '',
        ];


        // create: add new page for the credit
        $objModel = new FileCreditPageModel();
        $objModel->setRow($arrSet);
        $objModel->save();

        return true;
    }


    public static function indexFile(\Contao\Image $objImage)
    {
        /**
         * @var $objPage \PageModel
         */
        global $objPage;

        $imagePaths = [];
        $cache      = FileCache::getInstance();
        $cacheItem  = $cache->getItem('fcp_' . md5(\StringUtil::standardize(\StringUtil::restoreBasicEntities($objPage->getAbsoluteUrl()))));

        // in backend when synchronizing credits, cache input parameter = 0
        if ($cacheItem->get() !== null) {

            $imagePaths = deserialize($cacheItem->get(), true);

            // skip if image path listed in cache for current page
            if (in_array($objImage->getOriginalPath(), $imagePaths)) {
                return false;
            }
        }

        $objFile = \FilesModel::findByPath($objImage->getOriginalPath());

        if ($objFile instanceof \Model\Collection) {
            return static::indexFiles($objFile);
        }

        if (!$objFile instanceof \Model) {
            return false;
        }


        if (!static::doIndex()) {
            return false;
        }

        if (!static::addFile($objFile)) {
            return false;
        }

        $imagePaths[] = $objImage->getOriginalPath();

        $cacheItem->set($imagePaths);
        $cacheItem->addTag('fcp');

        // add image to cache
        $cache->save($cacheItem);

        return true;
    }

    protected static function indexFiles(\Model\Collection $objFiles)
    {
        $blnCheck = true;

        while ($objFiles->next()) {
            $return   = static::indexFile($objFiles->current());
            $blnCheck = !$blnCheck ?: $return;
        }

        return $blnCheck;
    }

}
