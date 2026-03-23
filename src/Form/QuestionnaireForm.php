<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

namespace Znrl\QuestionnaireBundle\Form;

use Codefog\HasteBundle\Form\Form;

class QuestionnaireForm
{

    public static function createForm(int $questionnaireId, $questionnaireItems, array $formData): Form {

        $form = new Form('questionnaire_form_' . $questionnaireId, 'POST');

        foreach ($questionnaireItems as $questionnaireItem) {

            $form->addFormField('question_' . $questionnaireItem->id, [
                'inputType' => 'explanation',
                'eval' => ['text' => $questionnaireItem->question],
            ]);

            $form->addFormField('answer_' . $questionnaireItem->id, [
                'inputType' => 'radio',
                'options' => ['1', '2', '3', '4', '5'],
                'default' => $formData['answer_' . $questionnaireItem->id] ?? '',
                'eval' => ['mandatory' => true],
            ]);
        }

        $form->addCaptchaFormField();
        $form->addSubmitFormField($GLOBALS['TL_LANG']['MSC']['questionnaire_submit_label']);

        return $form;
    }
}