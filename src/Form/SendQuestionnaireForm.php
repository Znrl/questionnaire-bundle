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

class SendQuestionnaireForm
{

    public static function createForm($questionnaireId): Form
    {

        $form = new Form('send_questionnaire_form-' . $questionnaireId, 'POST');

        $form->addFormField('name', [
            'label' => &$GLOBALS['TL_LANG']['MSC']['send_questionnaire_name_label'],
            'inputType' => 'text',
            'eval' => ['mandatory' => true],
        ]);

        $form->addFormField('email', [
            'label' => &$GLOBALS['TL_LANG']['MSC']['send_questionnaire_email_label'],
            'inputType' => 'text',
            'eval' => ['mandatory' => true],
        ]);

        $form->addCaptchaFormField();
        $form->addSubmitFormField($GLOBALS['TL_LANG']['MSC']['send_questionnaire_submit_label']);

        return $form;
    }
}