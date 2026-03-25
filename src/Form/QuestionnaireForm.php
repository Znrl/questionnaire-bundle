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
use Contao\Validator;

class QuestionnaireForm
{

    public static function createForm(int $questionnaireId, array $questionnaireItems, array $sessionFormData = []): Form {

        $form = new Form('questionnaire_form_' . $questionnaireId, 'POST');


        $options = ['1', '2', '3', '4', '5'];

        foreach ($questionnaireItems as $questionnaireItem) {

            $form->addFormField('question_' . $questionnaireItem->id, [
                'inputType' => 'explanation',
                'eval' => ['text' => $questionnaireItem->question],
            ]);

            $form->addFormField('answer_' . $questionnaireItem->id, [
                'inputType' => 'radio',
                'options' => $options,
                'default' => $sessionFormData['answer_' . $questionnaireItem->id] ?? '',
                'eval' => ['decodeEntities' => true, 'mandatory' => true],
                'save_callback' => [
                    static function (string $value) use ($options): string {
                        if (!in_array($value, $options, true)) {
                            throw new \InvalidArgumentException('Submitted Option is not valid!');
                        }
                        return $value;
                    }
                ]
            ]);
        }

        $form->addCaptchaFormField();
        $form->addSubmitFormField($GLOBALS['TL_LANG']['MSC']['questionnaire_submit_label']);

        return $form;
    }
}