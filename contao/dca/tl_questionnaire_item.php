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

$GLOBALS['TL_DCA']['tl_questionnaire_item'] = array
(
    // Config
    'config' => array
    (
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_questionnaire',
        'enableVersioning' => true,
        'markAsCopy' => 'question',
        'sql' => array
        (
            'keys' => array
            (
                'id' => 'primary',
                'pid,published' => 'index',
            )
        )
    ),

    // List
    'list' => array
    (
        'sorting' => array
        (
            'mode' => DataContainer::MODE_PARENT,
            'fields' => array('sorting'),
            'panelLayout' => 'search,filter,limit',
            'defaultSearchField' => 'question',
            'headerFields' => array('title', 'headline', 'tstamp'),
            'renderAsGrid' => true,
            'limitHeight' => 160
        ),
        'label' => array
        (
            'fields' => array('question'),
            'format' => '%s'
        ),
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{title_legend},question,weightingFactor;{publish_legend},published'
    ),

    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'autoincrement' => true)
        ),
        'pid' => array
        (
            'foreignKey' => 'tl_questionnaire.title',
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0),
            'relation' => array('type' => 'belongsTo', 'load' => 'lazy')
        ),
        'sorting' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0)
        ),
        'tstamp' => array
        (
            'sql' => array('type' => 'integer', 'unsigned' => true, 'default' => 0)
        ),
        'question' => array
        (
            'search' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'text',
            'eval' => array('mandatory' => true, 'basicEntities' => true, 'maxlength' => 255, 'tl_class' => 'long'),
            'sql' => array('type' => 'string', 'length' => 255, 'default' => '')
        ),
        'weightingFactor' => array
        (
            'inputType' => 'text',
            'eval' => array('rgxp' => 'digit', 'nospace'=> true, 'tl_class' => 'w50'),
            'sql' => array('type' => 'float', 'unsigned' => true, 'default' => '0')
        ),
        'published' => array
        (
            'toggle' => true,
            'filter' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_DESC,
            'inputType' => 'checkbox',
            'eval' => array('doNotCopy' => true),
            'sql' => array('type' => 'boolean', 'default' => false)
        )
    )
);