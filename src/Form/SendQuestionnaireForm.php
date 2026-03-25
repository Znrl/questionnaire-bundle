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

class SendQuestionnaireForm
{

    public static function createForm(int $questionnaireId): Form
    {

        $form = new Form('questionnaire_form_send_' . $questionnaireId, 'POST');

        $form->addFormField('name', [
            'label' => &$GLOBALS['TL_LANG']['MSC']['send_questionnaire_name_label'],
            'inputType' => 'text',
            'eval' => ['decodeEntities' => true, 'mandatory' => true, 'maxlength' => 64],
        ]);

        $form->addFormField('email', [
            'label' => &$GLOBALS['TL_LANG']['MSC']['send_questionnaire_email_label'],
            'inputType' => 'text',
            'eval' => ['decodeEntities' => true, 'mandatory' => true, 'maxlength' => 255],
            'save_callback' => [
                static function (mixed $value): mixed {
                    if (!Validator::isEmail($value)) {
                        throw new \InvalidArgumentException('Not a single, valid email address!');
                    }
                    return $value;
                }
            ]
        ]);

        $form->addCaptchaFormField();
        $form->addSubmitFormField($GLOBALS['TL_LANG']['MSC']['send_questionnaire_submit_label']);

        return $form;
    }
}