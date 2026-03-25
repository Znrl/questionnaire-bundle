<?php

declare(strict_types=1);

/*
 * This file is part of Questionnaire Bundle
 *
 * (c) Znrl
 *
 * @license LGPL-3.0-or-later
 */

use Contao\DataContainer;
use Contao\DC_Table;

$GLOBALS['TL_DCA']['tl_questionnaire'] = array
(
    // Config
    'config' => array
    (
        'dataContainer' => DC_Table::class,
        'ctable' => array('tl_questionnaire_item', 'tl_questionnaire_result'),
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => DataContainer::MODE_SORTED,
            'fields' => array('title'),
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'panelLayout' => 'search,limit',
            'defaultSearchField' => 'title'
        ),
        'label' => array
        (
            'fields' => array('title'),
            'format' => '%s'
        ),
        'operations' => array
        (
            'modules' => array
            (
                'href' => 'table=tl_questionnaire_result',
                'prefetch'  => true,
                'icon' => 'modules.svg',
                'primary' => true,
            )
        )
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},title,headline'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'autoincrement' => true)
        ),
        'tstamp' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0)
        ),
        'title' => array
        (
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => array('type' => 'string', 'length' => 255, 'default' => '')
        ),
        'headline' => array
        (
            'search' => true,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'),
            'sql' => array('type' => 'string', 'length' => 255, 'default' => '')
        )
    )
);