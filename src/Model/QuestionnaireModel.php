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

class QuestionnaireModel extends Model
{
    /**
     * Table name
     * @var string
     */
    protected static $strTable = 'tl_questionnaire';
}