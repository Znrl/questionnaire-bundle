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

class FormHelper
{

    public static function validateForm(Form $form): bool
    {

        return $form->validate();
    }

    public static function generateForm(Form $form): string
    {

        return $form->generate();
    }

}