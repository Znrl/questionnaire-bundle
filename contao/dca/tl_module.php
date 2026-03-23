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


// Add palettes to tl_module
$GLOBALS['TL_DCA']['tl_module']['palettes']['questionnaire'] = '{title_legend},name,headline,type;{config_legend},questionnaire;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},cssID';

// Add fields to tl_module
$GLOBALS['TL_DCA']['tl_module']['fields']['questionnaire'] = array
(
    'inputType'               => 'select',
    'foreignKey'              => 'tl_questionnaire.title',
    'options_callback'        => array('tl_module_questionnaire', 'getQuestionnaires'),
    'eval'                    => array('chosen'=>true, 'tl_class'=>'w50 wizard'),
    'sql'                     => array('type'=>'integer', 'unsigned'=>true, 'default'=>0),
    'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
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