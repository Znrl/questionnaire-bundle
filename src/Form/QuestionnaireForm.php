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

    public static function createQuestionnaireForm(int $questionnaireId, $questionnaireItems, array $formData): Form {

        $form = new Form('questionnaire-form-' . $questionnaireId, 'POST');

        foreach ($questionnaireItems as $questionnaireItem) {

            $form->addFormField('question_' . $questionnaireItem->id, [
                'label' => &$GLOBALS['TL_LANG']['MSC']['TODO'], // ['This is the <legend>', 'This is the <label>']
                'inputType' => 'explanation',
                'eval' => ['text' => $questionnaireItem->question],
            ]);

            $form->addFormField('answer_' . $questionnaireItem->id, [
                'label' => &$GLOBALS['TL_LANG']['MSC']['TODO'], // ['This is the <legend>', 'This is the <label>']
                'inputType' => 'radio',
                'options' => ['1', '2', '3', '4', '5'],
                'default' => $formData['answer_' . $questionnaireItem->id] ?? '',
                'eval' => ['mandatory' => true],
            ]);
        }

        $form->addCaptchaFormField();
        $form->addSubmitFormField('Auswerten');

        return $form;
    }

    public static function validateQuestionnaireForm(Form $form): bool {

        return $form->validate();
    }

    public static function generateQuestionnaireForm(Form $form): string {

        return $form->generate();
    }

}