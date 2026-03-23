<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */


use Znrl\QuestionnaireBundle\Model\QuestionnaireItemModel;
use Znrl\QuestionnaireBundle\Model\QuestionnaireModel;
use Znrl\QuestionnaireBundle\Model\QuestionnaireResultModel;

// Add backend modules
$GLOBALS['BE_MOD']['content']['questionnaire'] = [
    'tables' => ['tl_questionnaire', 'tl_questionnaire_item', 'tl_questionnaire_result'],
];


// Models
$GLOBALS['TL_MODELS']['tl_questionnaire'] = QuestionnaireModel::class;
$GLOBALS['TL_MODELS']['tl_questionnaire_item'] = QuestionnaireItemModel::class;
$GLOBALS['TL_MODELS']['tl_questionnaire_result'] = QuestionnaireResultModel::class;