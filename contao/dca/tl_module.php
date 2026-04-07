<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Backend;
use Contao\Database;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;


// Add palettes to tl_module
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'questionnaire_allow_send_mail';
$GLOBALS['TL_DCA']['tl_module']['palettes']['questionnaire'] = '{title_legend},name,headline,type;{config_legend},questionnaire;{mail_config_legend},questionnaire_allow_send_mail;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['questionnaire_allow_send_mail'] = 'questionnaire_send_mail_copy_to_user,questionnaire_mail_recipient,questionnaire_mail_subject,questionnaire_mail_text,questionnaire_mail_form_label';

// Add fields to tl_module
$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire'] = array
(
    'inputType'             => 'select',
    'foreignKey'            => 'tl_questionnaire.title',
    'options_callback'      => array('tl_module_questionnaire', 'getQuestionnaires'),
    'eval'                  => array('chosen'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                   => array('type'=>'integer', 'unsigned'=>true, 'default'=>0),
    'relation'              => array('type'=>'hasOne', 'load'=>'lazy')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_allow_send_mail'] = array
(
    'inputType'             => 'checkbox',
    'eval'                  => ['submitOnChange' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql'                   => ['type' => 'boolean', 'default' => true],
);
$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_send_mail_copy_to_user'] = array
(
    'inputType'             => 'checkbox',
    'eval'                  => ['chosen' => true, 'tl_class' => 'w50'],
    'sql'                   => ['type' => 'boolean', 'default' => true],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_mail_recipient'] = array
(
    'exclude'               => true,
    'inputType'             => 'text',
    'eval'                  => ['decodeEntities' => true, 'tl_class' => 'clr w50', 'rgxp' => 'email', 'mandatory' => true],
    'sql'                   => ['type' => 'text', 'default' => null, 'notnull' => false],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_mail_subject'] = array
(
    'inputType'             => 'text',
    'eval'                  => ['tl_class' => 'long clr', 'decodeEntities' => true, 'mandatory' => true],
    'sql'                   => ['type' => 'string', 'length' => 255, 'default' => null, 'notnull' => false],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_mail_text'] = array
(
    'default'               => $GLOBALS['TL_LANG']['tl_module']['questionnaire_mail_text_default'][0] ?? null,
    'inputType'             => 'textarea',
    'eval'                  => array('style'=>'height:120px', 'decodeEntities'=>true, 'mandatory' => true),
    'sql'                   => array('type'=>'text', 'length'=>AbstractMySQLPlatform::LENGTH_LIMIT_TEXT, 'notnull'=>false)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire_mail_form_label'] = array
(
    'inputType'             => 'text',
    'eval'                  => ['tl_class' => 'long clr', 'decodeEntities' => true],
    'sql'                   => ['type' => 'string', 'length' => 255, 'default' => null, 'notnull' => false],
);

/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @internal
 */
class tl_module_questionnaire extends Backend
{

    /**
     * Get all questionnaires and return them as an array
     *
     * @return array
     */
    public function getQuestionnaires(): array
    {

        $arrQuestionnaires = array();
        $objQuestionnaires = Database::getInstance()->execute("SELECT id, title FROM tl_questionnaire ORDER BY title");

        while ($objQuestionnaires->next())
        {
            $arrQuestionnaires[$objQuestionnaires->id] = $objQuestionnaires->title;
        }

        return $arrQuestionnaires;
    }
}