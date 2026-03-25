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

class QuestionnaireResultModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_questionnaire_result';


    /**
     * Find all published Questionnaire Items by their parent ID
     *
     * @param int $intPid The parent ID
     * @param float $minScore The resulting score
     * @param array $arrOptions An optional options array
     *
     * @return Collection<QuestionnaireModel>|null A collection of models or null if there are no Questionnaire Items
     */
    public static function findPublishedByPidAndMinScore(int $intPid, float $minScore, array $arrOptions = []): ?Collection
    {
        $t = static::$strTable;
        $arrColumns = array("$t.pid=? AND $t.minScore<=?",);

        if (!static::isPreviewMode($arrOptions))
        {
            $arrColumns[] = "$t.published=1";
        }

        if (!isset($arrOptions['order']))
        {
            $arrOptions['order'] = "$t.minScore DESC";
        }

        return static::findBy($arrColumns, array($intPid, $minScore), $arrOptions);
    }
}
