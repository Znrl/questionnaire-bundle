<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

namespace Znrl\QuestionnaireBundle\Model;

use Contao\Model;
use Contao\Model\Collection;

class QuestionnaireItemModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_questionnaire_item';


    /**
     * Find all published Questionnaire Items by their parent ID
     *
     * @param int $intPid     The parent ID
     * @param array $arrOptions An optional options array
     *
     * @return Collection<QuestionnaireModel>|null A collection of models or null if there are no Questionnaire Items
     */
    public static function findPublishedByPid(int $intPid, array $arrOptions=array()): ?Collection
    {
        $t = static::$strTable;
        $arrColumns = array("$t.pid=?");

        if (!static::isPreviewMode($arrOptions))
        {
            $arrColumns[] = "$t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.sorting";
        }

        return static::findBy($arrColumns, array($intPid), $arrOptions);
    }
}
